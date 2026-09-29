<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentBatchRequest;
use App\Http\Resources\PaymentBatchResource;
use App\Models\PaymentBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentBatchController extends Controller
{
    public function __construct()
    {
        $this->name          = 'Payment Batch';
        $this->model         = PaymentBatch::class;
        $this->resource      = PaymentBatchResource::class;
        $this->relationships = ['student.person.nationality', 'entries'];
    }

    public function index(Request $request)
    {
        return $this->list($request, function ($query) use ($request) {
            if ($studentId = $request->input('student_id')) {
                $query->where('student_id', $studentId);
            }

            return $query;
        });
    }

    /**
     * SA uploads a payment invoice/proof image — uploaded_by and paid_at
     * are set server-side (this call IS the moment of marking paid, so
     * there's nothing for SA to pick). invoice_path/invoice_type are
     * computed from the stored file, not accepted as raw input — routes
     * through the base save() helper via a plain Model::create() would
     * mass-assign the raw UploadedFile onto a non-existent column, so this
     * bypasses it and builds the array explicitly instead.
     */
    public function store(PaymentBatchRequest $request)
    {
        $data = $request->validated();
        $file = $request->file('invoice_file');
        unset($data['invoice_file']);

        if ($file) {
            $data['invoice_path'] = $file->store('payment-invoices', 'public');
            $data['invoice_type'] = $file->getClientMimeType();
        }

        $data['uploaded_by'] = auth()->id();
        $data['paid_at']     = now();

        $batch = PaymentBatch::create($data);

        return new PaymentBatchResource($this->reload($batch));
    }

    public function show(PaymentBatch $paymentBatch)
    {
        return $this->view($paymentBatch);
    }

    /**
     * SA corrects a recorded payment — replaces the proof image and/or
     * edits the remark. Same explicit build as store() so the raw
     * UploadedFile never reaches mass assignment. student_id, paid_at and
     * uploaded_by stay as recorded: the payment still belongs to the same
     * student and happened when it happened. Sent as POST + _method=PUT,
     * since PHP doesn't parse multipart bodies on a real PUT.
     */
    public function update(PaymentBatchRequest $request, PaymentBatch $paymentBatch)
    {
        $data    = ['remark' => $request->validated('remark')];
        $file    = $request->file('invoice_file');
        $oldPath = $paymentBatch->invoice_path;

        if ($file) {
            $data['invoice_path'] = $file->store('payment-invoices', 'public');
            $data['invoice_type'] = $file->getClientMimeType();
        }

        $paymentBatch->update($data);

        // Only after the new path is saved, so a failed update never
        // leaves the record pointing at a deleted file.
        if ($file && $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return new PaymentBatchResource($this->reload($paymentBatch));
    }

    public function destroy(PaymentBatch $paymentBatch)
    {
        return $this->disable($paymentBatch);
    }

    public function restore(PaymentBatch $paymentBatch)
    {
        return $this->enable($paymentBatch);
    }

    public function force_destroy(PaymentBatch $paymentBatch)
    {
        return $this->clear($paymentBatch);
    }
}

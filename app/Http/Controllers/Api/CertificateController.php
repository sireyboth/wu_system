<?php
namespace App\Http\Controllers\Api;

use App\Helpers\NumberGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\CertificateRequest;
use App\Http\Resources\CertificateResource;
use App\Models\Certificate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct(protected NumberGenerator $generator)
    {
        $this->name          = 'Certificate';
        $this->model         = Certificate::class;
        $this->resource      = CertificateResource::class;
        $this->relationships = $this->withStudent();

    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->list($request, fn($query) => $request->type === Certificate::PROVISIONAL
                ? $query->provisional()
                : $query->status());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CertificateRequest $request)
    {
        $data = $request->validated();
        return execute(function () use ($request, $data) {
            $code = $this->generator->generate(
                $data['type'] ?? 'status',
                Carbon::parse($data['issue_date'])
            );

            return $this->save($request, ['certificate_no' => $code]);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Certificate $certificate)
    {
        return $this->view($certificate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CertificateRequest $request, Certificate $certificate)
    {
        return $this->release($request, $certificate);
    }

    /**
     * Disable the specified resource from storage.
     */
    public function destroy(Certificate $certificate)
    {
        return $this->disable($certificate);
    }

    /**
     * Restore a soft-deleted of the resource.
     */
    public function restore(Certificate $certificate)
    {
        return $this->enable($certificate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function force_destroy(Certificate $certificate)
    {
        return $this->clear($certificate);
    }

    public function preview(Request $request)
    {
        $request->validate(['issue_date' => 'required|date']);
        $preview = $this->generator->preview(
            $request->type,
            Carbon::parse($request->issue_date)
        );

        return response()->json(['certificate_no' => $preview]);
    }
}

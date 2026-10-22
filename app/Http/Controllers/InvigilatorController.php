<?php
namespace App\Http\Controllers;

use App\Models\Invigilator;
use Illuminate\Support\Facades\Storage;

class InvigilatorController extends Controller
{
    public function index()
    {
        return view('invigilator.index');
    }

    /**
     * Public page the printed card's QR opens — no login. Looked up by
     * public_token, never by id, so pages can't be enumerated.
     */
    public function show(string $token)
    {
        $invigilator = Invigilator::query()
            ->where('public_token', $token)
            ->with('histories')
            ->firstOrFail();

        return view('invigilator.public', compact('invigilator'));
    }

    /** Streams the photo for the public page, the admin list and the card. */
    public function photo(string $token)
    {
        $path = Invigilator::query()->where('public_token', $token)->value('photo_path');

        if (! $path || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }
}

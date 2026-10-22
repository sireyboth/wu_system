<?php
namespace App\Http\Controllers;

use App\Helpers\Degree;
use App\Models\Major;
use App\Models\Province;
use App\Models\Subject;
use App\Models\Transcript;
use Illuminate\Http\Request;

class TranscriptController extends Controller
{
    /** The form's pick-lists ride along with the page — no extra API round trips. */
    public function index()
    {
        $majors = Major::query()->orderBy('name_en')->get(['id', 'name_en', 'name_kh']);

        $subjects = Subject::query()->orderBy('code')->get(['id', 'code', 'name_en', 'name_kh', 'credit'])
            ->map(fn ($s) => [
                'id'      => $s->id,
                'code'    => $s->code,
                'name_en' => $s->name_en,
                'name_kh' => $s->name_kh,
                'credit'  => $s->credit,
            ]);

        $nationalities = collect(get_data('nationalities'))->pluck('name_en')->filter()->unique()->values();
        $degrees       = collect(Degree::cases())->map(fn ($d) => ['value' => $d->value, 'label' => $d->name]);

        $docTypes      = Transcript::DOC_TYPES;

        // Place of birth as printed: "Kampong Chhnang Province" / "ខេត្តកំពង់ឆ្នាំង"; Phnom Penh is the capital.
        $provinces = Province::query()->orderBy('name_en')->get(['name_en', 'name_kh'])
            ->map(fn ($p) => $p->name_en === 'Phnom Penh'
                ? ['en' => 'Phnom Penh', 'kh' => 'រាជធានីភ្នំពេញ']
                : ['en' => "{$p->name_en} Province", 'kh' => "ខេត្ត{$p->name_kh}"]);

        return view('transcript.index', compact('majors', 'subjects', 'nationalities', 'degrees', 'docTypes', 'provinces'));
    }

    /**
     * Content only — the university prints onto pre-printed letterhead,
     * so this page has no logo/frame, just the body placed inside the
     * letterhead's blank area. Blocked until the entrance exam is passed.
     * doc_type picks the layout: the transcript's course table, or the
     * bilingual provisional certificate.
     */
    public function print(Request $request, Transcript $transcript)
    {
        abort_if($transcript->isDraft(), 403, 'This document is still a draft — finish it and Save before printing.');
        abort_unless($transcript->canPrint(), 403, 'Entrance exam not passed — this document cannot be printed.');

        if ($transcript->isProvisional()) {
            $transcript->load('major');

            return view('transcript.provisional', compact('transcript'));
        }

        $transcript->load(['major', 'subjects']);

        // [year => [1 => rows, 2 => rows]], only years that have courses.
        $years = $transcript->subjects->groupBy('year')->sortKeys()
            ->map(fn ($rows) => [1 => $rows->where('semester', 1)->values(), 2 => $rows->where('semester', 2)->values()]);

        $detail = $request->boolean('detail');

        return view('transcript.print', compact('transcript', 'years', 'detail'));
    }
}

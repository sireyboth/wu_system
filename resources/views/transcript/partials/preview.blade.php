{{--
    Preview mode (?preview=1): look at the layout without logging a print.
    The browser's own print (Ctrl+P) gets a notice instead of the document,
    so every real print still goes through Request Docs → Print and is counted.
--}}
@if ($preview)
    <style>
        .preview-bar {
            padding: 8px 16px;
            background: #fef3c7;
            color: #92400e;
            border-bottom: 1px solid #fcd34d;
            font: 600 13px/1.5 system-ui, sans-serif;
        }

        .preview-bar ul {
            margin: 4px 0 0;
            padding-left: 20px;
            font-weight: 500;
            color: #b91c1c;
        }

        .preview-print-note {
            display: none;
        }

        @media print {
            body > *:not(.preview-print-note) {
                display: none !important;
            }

            .preview-print-note {
                display: block !important;
                padding: 40mm 20mm;
                text-align: center;
                font: 600 16pt/1.6 system-ui, sans-serif;
            }
        }
    </style>

    <div class="preview-bar">
        👁 មើលជាមុន (Preview) — not counted as a print. To print, use Request Docs → Print.
        @if ($blockers)
            <ul>
                @foreach ($blockers as $blocker)
                    <li>{{ $blocker }}</li>
                @endforeach
            </ul>
        @endif
    </div>
    <div class="preview-print-note">
        Preview only — this copy cannot be printed.<br>
        Print it from Request Docs → Print so it is recorded.
    </div>
@endif

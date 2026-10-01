@php
    /*
     * What a person sees after scanning an invigilator's card — styled to
     * match the printed "Formal Modern" badge (navy / lime / cream), front
     * and back. Standalone page with its own CSS (no Vite, no JS) so it
     * loads fast on a phone and doesn't depend on the asset build.
     */
    $histories = $invigilator->histories;
    $rated     = $histories->whereNotNull('rating');
    $avgRating = $rated->isNotEmpty() ? round($rated->avg('rating'), 1) : null;
    $initial   = mb_substr($invigilator->name_en ?: $invigilator->name_kh, 0, 1);
    $isTop     = $avgRating !== null && $avgRating >= 4.5;
    $expired   = $invigilator->isExpired();
    $isCutout  = $invigilator->photoIsCutout();

    // Decorative barcode derived from the ID, so each card's differs.
    $bars = collect(str_split((string) $invigilator->code))
        ->flatMap(fn ($c) => [1 + ord($c) % 3, 1 + intdiv(ord($c), 3) % 3])
        ->take(34);
@endphp
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0b1322">
    <title>{{ $invigilator->name_en }} — Invigilator · Western University</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy: #101b2e;
            --navy-2: #172640;
            --page: #0b1322;
            --lime: #d4f56b;
            --cream: #f3f1ea;
            --cream-2: #e8e4d8;
            --ink: #101b2e;
            --muted: #6b7280;
            --line: rgba(16, 27, 46, .1);
            --display: 'Bricolage Grotesque', 'Kantumruy Pro', system-ui, sans-serif;
            --body: 'Kantumruy Pro', system-ui, sans-serif;
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; }
        body {
            min-height: 100vh;
            font-family: var(--body);
            color: var(--ink);
            background:
                repeating-radial-gradient(circle at 50% -10%, transparent 0 46px, rgba(255,255,255,.035) 47px 48px),
                var(--page);
            -webkit-font-smoothing: antialiased;
        }

        .wrap {
            max-width: 880px;
            margin: 0 auto;
            padding: 28px 16px 40px;
            display: grid;
            gap: 24px;
            /* minmax(0, …) — the ticker is one long nowrap line; without it
               that line's width would stretch the column past the screen. */
            grid-template-columns: minmax(0, 1fr);
            justify-items: center;
        }
        @media (min-width: 860px) {
            .wrap { grid-template-columns: repeat(2, minmax(0, 1fr)); align-items: start; padding-top: 48px; }
        }

        .card {
            width: 100%; min-width: 0;
            max-width: 400px;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 30px 60px -20px rgba(0,0,0,.6), 0 0 0 1px rgba(255,255,255,.06);
            position: relative;
        }
        .slot {
            width: 64px; height: 9px; border-radius: 9px;
            background: #fff; margin: 14px auto 0;
            box-shadow: inset 0 1px 2px rgba(0,0,0,.25);
        }

        /* ---------------- FRONT ---------------- */
        .front {
            background: linear-gradient(180deg, #0f1a2e 0%, #15213a 45%, #3d4659 100%);
            color: #fff;
            isolation: isolate;
        }
        /* Faint Roumdoul outlines on the navy, as on the printed card. */
        .roumdoul {
            position: absolute; z-index: -1; pointer-events: none;
            background: url('{{ asset('images/roumdol.png') }}') center / contain no-repeat;
            opacity: .1;
        }
        .roumdoul.r1 { width: 210px; height: 196px; left: -58px; top: -46px; }
        .roumdoul.r2 { width: 250px; height: 233px; left: -120px; top: 150px; }

        .front-head {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            padding: 18px 14px 0 16px;
        }
        .brand { display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1; }
        .brand-logo { width: 38px; height: 50px; flex-shrink: 0; object-fit: contain; }
        .brand-kh { font-weight: 700; font-size: 16px; line-height: 1.3; white-space: nowrap; }
        .brand-name { font-weight: 600; font-size: 12.5px; letter-spacing: .03em; text-transform: uppercase; white-space: nowrap; }
        .pill-outline {
            flex-shrink: 0;
            font-weight: 700; font-size: 9.5px; letter-spacing: .02em; text-transform: uppercase;
            padding: 4px 10px; border-radius: 999px; border: 1.5px solid rgba(255,255,255,.9);
            white-space: nowrap;
        }

        .front-body {
            display: grid; grid-template-columns: auto minmax(0, 1fr); gap: 6px;
            padding: 0 12px 0 10px; height: 362px;
        }
        /* Rotated text column. In vertical writing mode a flex *column* lays
           the big word and the small label side by side (as on the badge);
           the padding keeps the word clear of the overlapping panel. */
        .vertical {
            display: flex; flex-direction: column; gap: 3px; align-items: flex-start;
            writing-mode: vertical-rl; transform: rotate(180deg);
            padding-top: 30px; overflow: hidden;
        }
        .vertical-big {
            font-family: var(--display); font-weight: 800; color: var(--lime);
            font-size: 48px; line-height: .8; letter-spacing: -.01em;
        }
        .vertical-small { font-size: 11px; font-weight: 600; color: #fff; letter-spacing: .02em; white-space: nowrap; }

        /* The arch is the light backdrop on the right; the photo is centred
           on it. A background-removed cutout may rise above the arch
           (.photo-cutout); an ordinary photo is clipped inside it. */
        .arch-wrap { position: relative; height: 100%; }
        .figure { position: absolute; top: 0; right: 0; bottom: 0; width: 78%; }
        .arch {
            position: absolute; left: 0; right: 0; bottom: 36px; height: 76%;
            border-radius: 999px 999px 14px 14px; overflow: hidden;
            background: #f5f4f0;
        }
        /* Kbach pattern behind the person — 20% opacity, multiply. */
        .arch::before {
            content: ''; position: absolute; inset: 0;
            background: url('{{ asset('images/kbach-pattern.webp') }}') center / 260px auto repeat;
            opacity: .2; mix-blend-mode: multiply;
        }
        .arch .photo-inside { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center top; }
        .photo-cutout {
            position: absolute; bottom: 0; left: 50%; transform: translateX(-50%);
            height: calc(100% - 40px); width: auto; max-width: none;
            object-fit: contain; object-position: bottom;
            filter: drop-shadow(0 10px 18px rgba(0,0,0,.2));
        }
        .arch-initial {
            position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
            font-family: var(--display); font-weight: 800; font-size: 110px; color: var(--navy);
        }
        /* Sits just left of the arch, overlapping its edge. */
        .status {
            position: absolute; left: 4px; bottom: 78px; z-index: 4;
            display: inline-flex; align-items: center; gap: 9px;
            padding: 5px 20px 5px 9px; border-radius: 999px;
            background: var(--lime); color: var(--navy);
            font-weight: 700; font-size: 10px; letter-spacing: .02em;
        }
        .status::before { content: ''; width: 9px; height: 9px; border-radius: 50%; background: currentColor; }
        .status.expired { background: #f43f5e; color: #fff; }

        .front-panel {
            position: relative; z-index: 3; overflow: hidden;
            margin: -22px 14px 12px; padding: 12px 18px 14px;
            background: #f6f5f1; color: var(--ink); border-radius: 22px;
            isolation: isolate;
        }
        /* Roumdoul in the panel's corner — multiplied so it reads on cream. */
        .front-panel::after {
            content: ''; position: absolute; z-index: -1; right: -46px; bottom: -70px; width: 210px; height: 196px;
            background: url('{{ asset('images/roumdol.png') }}') center / contain no-repeat;
            opacity: .45; mix-blend-mode: multiply; pointer-events: none;
        }
        .name-kh-big { font-weight: 700; font-size: 25px; line-height: 1.35; overflow-wrap: anywhere; }
        .name-sub {
            font-size: 11px; font-weight: 500; letter-spacing: .02em;
            text-transform: uppercase; color: #6b7280; overflow-wrap: anywhere;
        }
        .facts {
            display: grid; grid-template-columns: 1.25fr 1fr 1fr 1fr; gap: 6px;
            margin: 10px 26px 0 0; padding-top: 9px; border-top: 1px solid rgba(16,27,46,.35);
        }
        .fact dt { font-size: 8.5px; font-weight: 600; text-transform: uppercase; color: #6b7280; white-space: nowrap; }
        .fact dd { margin: 2px 0 0; font-weight: 600; font-size: 11px; text-transform: uppercase; overflow-wrap: anywhere; }

        /* ---------------- BACK ---------------- */
        .back { background: var(--cream); }
        .back-head {
            position: relative; overflow: hidden;
            background: var(--navy); color: #fff; padding: 0 24px 26px;
        }
        .back-head::before {
            content: ''; position: absolute; width: 360px; height: 360px; right: -150px; top: -110px;
            background: repeating-radial-gradient(circle, transparent 0 20px, rgba(255,255,255,.07) 21px 22px);
            border-radius: 50%;
        }
        .eyebrow { position: relative; margin-top: 22px; font-family: var(--display); font-weight: 800; font-size: 10.5px; letter-spacing: .14em; color: var(--lime); text-transform: uppercase; }
        .back-title { position: relative; margin: 4px 0 0; font-family: var(--display); font-weight: 800; font-size: 30px; letter-spacing: -.02em; line-height: 1.05; }
        .back-kh { position: relative; margin-top: 4px; font-size: 13px; color: rgba(255,255,255,.75); }
        .summary { position: relative; margin-top: 12px; display: inline-flex; gap: 8px; flex-wrap: wrap; }
        .summary span {
            font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 999px;
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12);
        }

        .back-body { padding: 18px 16px 6px; }

        .item {
            display: flex; gap: 12px; align-items: flex-start;
            background: #fff; border: 1px solid var(--line); border-radius: 14px;
            padding: 12px 14px; margin-bottom: 8px;
        }
        .num {
            width: 32px; height: 32px; flex-shrink: 0; border-radius: 9px;
            background: var(--navy); color: var(--lime);
            display: flex; align-items: center; justify-content: center;
            font-family: var(--display); font-weight: 800; font-size: 12.5px;
        }
        .item-title { font-weight: 700; font-size: 13.5px; line-height: 1.35; white-space: pre-line; overflow-wrap: anywhere; }
        .item-sub { margin-top: 2px; font-size: 12px; color: #4b5563; white-space: pre-line; overflow-wrap: anywhere; }
        .item-meta { margin-top: 4px; display: flex; flex-wrap: wrap; align-items: center; gap: 4px 10px; font-size: 11.5px; color: var(--muted); }
        .stars { color: #f59e0b; letter-spacing: 1px; font-size: 13px; }
        .stars .off { color: #d1d5db; }
        .empty {
            text-align: center; padding: 18px 12px; border: 1px dashed rgba(16,27,46,.2); border-radius: 14px;
            font-size: 13px; color: var(--muted);
        }
        .note { background: #fffbeb; border-color: #fde68a; }
        .note .num { background: #f59e0b; color: #fff; }

        .issued {
            margin: 16px 0 0; padding: 14px; border-radius: 18px; background: var(--navy); color: #fff;
            display: flex; gap: 14px; align-items: center;
        }
        .seal {
            width: 74px; height: 74px; flex-shrink: 0; border-radius: 14px; background: var(--cream);
            display: flex; align-items: center; justify-content: center;
        }
        .seal svg { width: 40px; height: 40px; color: #16a34a; }
        .issued-label { font-size: 9.5px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: rgba(255,255,255,.6); }
        .issued-name { margin-top: 2px; font-family: var(--display); font-weight: 800; font-size: 14px; }
        .issued-time { margin-top: 8px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,.18); font-size: 11px; color: rgba(255,255,255,.75); }

        .foot {
            display: flex; justify-content: space-between; align-items: flex-end; gap: 12px;
            padding: 16px 20px 14px;
        }
        .barcode { display: flex; align-items: stretch; gap: 2px; height: 34px; }
        .barcode i { display: block; background: var(--ink); }
        .barcode-id { margin-top: 5px; font-family: ui-monospace, monospace; font-size: 10.5px; letter-spacing: .25em; }
        .return { text-align: right; font-size: 10px; color: var(--muted); line-height: 1.5; }
        .return b { display: block; color: var(--ink); font-size: 10.5px; letter-spacing: .06em; }

        .ticker { background: var(--lime); overflow: hidden; white-space: nowrap; padding: 8px 0; }
        .ticker-track { display: inline-block; animation: ticker 28s linear infinite; }
        .ticker span {
            font-family: var(--display); font-weight: 800; font-size: 10.5px; letter-spacing: .14em;
            text-transform: uppercase; color: var(--navy); padding-right: 22px;
        }
        @keyframes ticker { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        @media (prefers-reduced-motion: reduce) { .ticker-track { animation: none; } }

        .page-foot { grid-column: 1 / -1; text-align: center; font-size: 11px; color: rgba(255,255,255,.4); letter-spacing: .04em; }
    </style>
</head>
<body>
<main class="wrap">

    {{-- ================= FRONT ================= --}}
    <section class="card front" aria-label="Invigilator identity">
        <div class="roumdoul r1" aria-hidden="true"></div>
        <div class="roumdoul r2" aria-hidden="true"></div>

        <header class="front-head">
            <div class="brand">
                <img class="brand-logo" src="{{ asset('images/logo.png') }}" alt="Western University logo">
                <div style="min-width:0">
                    <div class="brand-kh">សាកលវិទ្យាល័យវេស្ទើន</div>
                    <div class="brand-name">Western University</div>
                </div>
            </div>
            @if ($isTop)
                <div class="pill-outline" title="Average rating {{ $avgRating }} / 5">Top Invigilator</div>
            @endif
        </header>

        <div class="front-body">
            <div class="vertical" aria-hidden="true">
                <span class="vertical-big">INVIGILATOR</span>
                <span class="vertical-small">អនុរក្សប្រឡង · Official Exam</span>
            </div>

            <div class="arch-wrap">
                <div class="figure">
                    <div class="arch">
                        @if ($invigilator->photo_url && ! $isCutout)
                            <img class="photo-inside" src="{{ $invigilator->photo_url }}" alt="Photo of {{ $invigilator->name_en }}">
                        @elseif (! $invigilator->photo_url)
                            <div class="arch-initial">{{ $initial }}</div>
                        @endif
                    </div>
                    @if ($invigilator->photo_url && $isCutout)
                        <img class="photo-cutout" src="{{ $invigilator->photo_url }}" alt="Photo of {{ $invigilator->name_en }}">
                    @endif
                </div>

                @if ($expired)
                    <span class="status expired" title="Valid until {{ $invigilator->valid_until->format('d M Y') }}">EXPIRED</span>
                @else
                    <span class="status">ON DUTY</span>
                @endif
            </div>
        </div>

        <div class="front-panel">
            <div class="name-kh-big">{{ $invigilator->name_kh }}</div>
            <div class="name-sub">
                {{ $invigilator->name_en }}@if ($invigilator->department) &nbsp;•&nbsp; {{ $invigilator->department }}@endif
            </div>

            <dl class="facts">
                <div class="fact"><dt>Student ID</dt><dd>{{ $invigilator->code }}</dd></div>
                <div class="fact"><dt>Batch</dt><dd>{{ $invigilator->batch ?: '—' }}</dd></div>
                <div class="fact"><dt>Room</dt><dd>{{ $invigilator->room ?: '—' }}</dd></div>
                <div class="fact"><dt>Valid To</dt><dd>{{ $invigilator->valid_until?->format('d-m-y') ?? '—' }}</dd></div>
            </dl>
        </div>
    </section>

    {{-- ================= BACK ================= --}}
    <section class="card back" aria-label="Duty record">
        <div class="back-head">
            <div class="slot" style="position:relative"></div>
            <div class="eyebrow">For Invigilators</div>
            <h1 class="back-title">Duty Record</h1>
            <div class="back-kh">ប្រវត្តិការងារអនុរក្ស</div>
            @if ($histories->isNotEmpty())
                <div class="summary">
                    <span>{{ $histories->count() }} {{ $histories->count() === 1 ? 'record' : 'records' }}</span>
                    @if ($avgRating)
                        <span>★ {{ $avgRating }} / 5 avg</span>
                    @endif
                </div>
            @endif
        </div>

        <div class="back-body">
            @if ($invigilator->remark)
                <div class="item note">
                    <div class="num" aria-hidden="true">!</div>
                    <div style="min-width:0">
                        <div class="item-title">សម្គាល់ (Note)</div>
                        <div class="item-sub">{{ $invigilator->remark }}</div>
                    </div>
                </div>
            @endif

            @forelse ($histories as $i => $history)
                <div class="item">
                    <div class="num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
                    <div style="min-width:0">
                        <div class="item-title">{{ $history->description }}</div>
                        <div class="item-meta">
                            <span>{{ $history->date?->format('d M Y') ?? 'No date' }}</span>
                            @if ($history->rating)
                                <span class="stars" title="{{ $history->rating }} / 5">{{ str_repeat('★', $history->rating) }}<span class="off">{{ str_repeat('★', 5 - $history->rating) }}</span></span>
                            @endif
                        </div>
                        @if ($history->remark)
                            <div class="item-sub">{{ $history->remark }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty">មិនទាន់មានប្រវត្តិ · No duty records yet</div>
            @endforelse

            <div class="issued">
                <div class="seal" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
                <div style="min-width:0">
                    <div class="issued-label">Issued &amp; verified by</div>
                    <div class="issued-name">Western University</div>
                    <div class="issued-time">Checked {{ now()->format('d M Y · H:i') }} — this record is active in the university system.</div>
                </div>
            </div>
        </div>

        <div class="foot">
            <div>
                <div class="barcode" aria-hidden="true">
                    @foreach ($bars as $w)
                        <i style="width: {{ $w }}px; {{ $loop->odd ? '' : 'background: transparent;' }}"></i>
                    @endforeach
                </div>
                <div class="barcode-id">{{ $invigilator->code }}</div>
            </div>
            <div class="return">
                IF FOUND, RETURN TO
                <b>WESTERN UNIVERSITY</b>
            </div>
        </div>

        <div class="ticker" aria-hidden="true">
            <div class="ticker-track">
                @for ($i = 0; $i < 2; $i++)
                    <span>Official Invigilator</span><span>·</span><span>អនុរក្ស</span><span>·</span><span>Non-transferable</span><span>·</span>
                    <span>Official Invigilator</span><span>·</span><span>អនុរក្ស</span><span>·</span><span>Non-transferable</span><span>·</span>
                @endfor
            </div>
        </div>
    </section>

    <p class="page-foot">Western University · Official Exam Portal</p>
</main>
</body>
</html>

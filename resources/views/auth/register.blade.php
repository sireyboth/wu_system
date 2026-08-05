<x-guest-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700;9..144,900&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap');

        :root{
            --ink-900:#0B1B34;
            --ink-700:#1b3862;
            --brass:#C9A227;
            --sage:#5F7D5A;
            --sage-bright:#7A9C74;
            --sage-tint:#E7EEE4;
            --parchment:#F7F3E9;
            --parchment-dim:#EFE9DA;
            --slate-900:#232A36;
            --slate-500:#6b7280;

            --rg-form-bg:var(--parchment);
            --rg-form-bg-dim:var(--parchment-dim);
            --rg-form-text:var(--ink-900);
            --rg-form-subtext:var(--slate-500);
            --rg-form-border:#dcd5c2;
            --rg-form-placeholder:#a8a08c;
            --rg-input-bg:#FFFFFF;
            --rg-submit-bg:var(--sage);
            --rg-submit-bg-hover:#4d6a49;
            --rg-submit-text:#F7F3E9;
            --rg-left-1:var(--ink-700);
            --rg-left-2:var(--ink-900);
            --rg-left-3:#0a1730;
            --rg-badge-bg:var(--sage-tint);
            --rg-badge-text:var(--sage);
        }
        html[data-rg-theme="dark"]{
            --rg-form-bg:#14171d;
            --rg-form-bg-dim:#1c2029;
            --rg-form-text:#F2EEE3;
            --rg-form-subtext:#9aa3b2;
            --rg-form-border:#333947;
            --rg-form-placeholder:#5b6270;
            --rg-input-bg:#1c212b;
            --rg-submit-bg:var(--sage-bright);
            --rg-submit-bg-hover:#8fb389;
            --rg-submit-text:#0e1710;
            --rg-left-1:#132340;
            --rg-left-2:#060c1a;
            --rg-left-3:#04070f;
            --rg-badge-bg:#1c2a1e;
            --rg-badge-text:var(--sage-bright);
        }

        .registrar-reg-wrap{
            position:fixed; inset:0; width:100vw; height:100vh;
            display:flex; flex-wrap:wrap-reverse; overflow-y:auto;
            font-family:'Inter',sans-serif; z-index:1;
        }
        .rg-theme-toggle{
            position:fixed; top:1.5rem; right:1.5rem; z-index:50; width:44px; height:44px; border-radius:50%;
            border:1px solid rgba(120,120,120,0.25); background:rgba(120,120,120,0.08); backdrop-filter:blur(6px);
            display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--sage);
            transition:transform .25s ease, background .25s ease;
        }
        .rg-theme-toggle:hover{ transform:scale(1.08); }
        .rg-theme-toggle svg{ width:20px; height:20px; }
        .rg-theme-toggle .icon-moon{ display:none; }
        html[data-rg-theme="dark"] .rg-theme-toggle .icon-sun{ display:none; }
        html[data-rg-theme="dark"] .rg-theme-toggle .icon-moon{ display:block; }

        .rg-inst{
            flex:1 1 44%; min-width:320px; position:relative;
            background:
                radial-gradient(1200px 800px at 85% -10%, var(--rg-left-1) 0%, transparent 55%),
                linear-gradient(200deg, var(--rg-left-2) 0%, var(--rg-left-3) 100%);
            color:var(--parchment); padding:clamp(2.5rem,5vw,5rem);
            display:flex; flex-direction:column; justify-content:space-between; overflow:hidden;
            min-height:480px; transition:background .4s ease;
        }
        .rg-inst::before{
            content:""; position:absolute; inset:0;
            background-image:repeating-linear-gradient(115deg, rgba(255,255,255,0.02) 0 1px, transparent 1px 6px);
            pointer-events:none;
        }
        .rg-brand{
            display:flex; align-items:center; gap:.85rem; letter-spacing:.22em; text-transform:uppercase;
            font-size:.7rem; font-weight:600; color:var(--sage-bright);
            opacity:0; transform:translateY(-6px); animation:rgFadeDown .7s .15s cubic-bezier(.16,1,.3,1) forwards;
        }
        .rg-brand .rule{ width:26px; height:1px; background:var(--sage-bright); }
        .rg-headline{
            margin:2.2rem 0 0; font-family:'Fraunces',serif; font-weight:600;
            font-size:clamp(2rem,3.2vw,2.8rem); line-height:1.1; letter-spacing:-0.01em; color:#fbf9f4;
            opacity:0; transform:translateY(10px); animation:rgFadeUp .8s .3s cubic-bezier(.16,1,.3,1) forwards;
        }
        .rg-headline em{ font-style:italic; color:var(--sage-bright); font-weight:500; }
        .rg-sub{
            margin-top:1rem; max-width:34ch; color:#b9c2d4; font-size:.98rem; line-height:1.65;
            opacity:0; transform:translateY(10px); animation:rgFadeUp .8s .45s cubic-bezier(.16,1,.3,1) forwards;
        }
        .rg-seal-stage{ position:relative; width:180px; height:180px; margin:2.6rem 0 1rem; }
        .rg-seal{ position:absolute; inset:0; opacity:0; animation:rgFadeIn .7s .7s ease forwards; }
        .rg-seal svg{ width:100%; height:100%; display:block; }
        .rg-seal .dash-ring{ animation:rgRotateDash 14s linear infinite; transform-origin:90px 90px; }
        .rg-seal .quill-line{ stroke-dasharray:120; stroke-dashoffset:120; animation:rgDrawLine 1.1s .9s cubic-bezier(.4,0,.2,1) forwards; }
        .rg-ticker{
            margin-top:auto; padding-top:2.4rem; border-top:1px solid rgba(255,255,255,0.12);
            display:flex; flex-wrap:wrap; gap:.5rem 1.4rem; font-family:'IBM Plex Mono',monospace;
            font-size:.68rem; letter-spacing:.08em; color:#7d8aa3;
            opacity:0; animation:rgFadeUp .8s .9s cubic-bezier(.16,1,.3,1) forwards;
        }
        .rg-ticker b{ color:var(--sage-bright); font-weight:500; }

        .rg-form-panel{
            flex:1 1 56%; min-width:320px; background:var(--rg-form-bg);
            display:flex; align-items:center; justify-content:center;
            padding:clamp(2rem,6vw,4rem); transition:background .4s ease;
        }
        .rg-card{ width:100%; max-width:440px; opacity:0; transform:translateY(14px); animation:rgFadeUp .7s .2s cubic-bezier(.16,1,.3,1) forwards; }
        .rg-badge{
            display:inline-flex; align-items:center; gap:.5rem; background:var(--rg-badge-bg); color:var(--rg-badge-text);
            padding:.4rem .8rem; border-radius:999px; font-family:'IBM Plex Mono',monospace; font-size:.66rem;
            font-weight:500; letter-spacing:.14em; text-transform:uppercase; margin-bottom:1.3rem;
            transition:background .4s ease, color .4s ease;
        }
        .rg-badge .dot{ width:6px; height:6px; border-radius:50%; background:var(--rg-badge-text); }
        .rg-title{ font-family:'Fraunces',serif; font-weight:600; font-size:2.15rem; letter-spacing:-0.01em; color:var(--rg-form-text); margin:0 0 .5rem; transition:color .4s ease; }
        .rg-rule{ width:44px; height:3px; background:var(--sage); margin-bottom:1.1rem; border-radius:2px; }
        .rg-lede{ color:var(--rg-form-subtext); font-size:.92rem; line-height:1.6; margin-bottom:2rem; transition:color .4s ease; }

        .rg-field-row{ display:flex; gap:1rem; }
        .rg-field-row .rg-field{ flex:1; min-width:0; }
        .rg-field{ position:relative; margin-bottom:1.1rem; }
        .rg-field label{ display:block; font-size:.68rem; font-weight:600; letter-spacing:.14em; text-transform:uppercase; color:var(--rg-form-subtext); margin-bottom:.5rem; transition:color .4s ease; }
        .rg-select-wrap{ position:relative; }
        .rg-field select, .rg-field input{
            width:100%; padding:.8rem 1rem; background:var(--rg-input-bg); border:1.5px solid var(--rg-form-border); border-radius:10px;
            font-family:'Inter',sans-serif; font-size:.95rem; color:var(--rg-form-text); outline:none; appearance:none;
            transition:border-color .2s ease, box-shadow .2s ease, background .3s ease, color .4s ease;
            box-shadow:0 1px 2px rgba(15,20,30,.04);
        }
        .rg-field select{ cursor:pointer; padding-right:2.2rem; }
        .rg-field select option{ background:var(--rg-input-bg); color:var(--rg-form-text); }
        .rg-select-wrap::after{
            content:""; position:absolute; right:1.1rem; top:calc(50% + 11px); translate:0 -65%; width:7px; height:7px;
            border-right:1.5px solid var(--rg-form-subtext); border-bottom:1.5px solid var(--rg-form-subtext); transform:rotate(45deg); pointer-events:none;
        }
        .rg-field input::placeholder{ color:var(--rg-form-placeholder); }
        .rg-field select:focus, .rg-field input:focus{ border-color:var(--sage); box-shadow:0 0 0 4px rgba(95,125,90,0.16); }
        .rg-error{ color:#e0635f; font-size:.74rem; margin-top:.4rem; }

        .rg-agree{ display:flex; align-items:flex-start; gap:.6rem; font-size:.8rem; color:var(--rg-form-subtext); line-height:1.5; margin:.6rem 0 1.3rem; }
        .rg-agree input{ width:15px; height:15px; margin-top:2px; accent-color:var(--sage); border:none; box-shadow:none; flex-shrink:0; }

        .rg-submit{
            margin-top:.2rem; width:100%; padding:1.05rem; background:var(--rg-submit-bg); color:var(--rg-submit-text);
            border:none; border-radius:10px; font-family:'Inter',sans-serif; font-weight:600; font-size:.78rem;
            letter-spacing:.16em; text-transform:uppercase; cursor:pointer; position:relative; overflow:hidden;
            transition:transform .15s ease, box-shadow .3s ease, background .3s ease;
            box-shadow:0 10px 24px -8px rgba(95,125,90,.4);
        }
        .rg-submit::before{
            content:""; position:absolute; inset:0; background:linear-gradient(100deg, transparent, rgba(255,255,255,.25), transparent);
            translate:-120% 0; transition:translate .6s ease;
        }
        .rg-submit:hover::before{ translate:120% 0; }
        .rg-submit:hover{ background:var(--rg-submit-bg-hover); }
        .rg-submit:active{ transform:scale(.98); }

        .rg-foot{ margin-top:1.8rem; padding-top:1.5rem; border-top:1px solid var(--rg-form-bg-dim); text-align:center; font-size:.85rem; color:var(--rg-form-subtext); transition:color .4s ease, border-color .4s ease; }
        .rg-foot a{ color:var(--rg-form-text); font-weight:700; text-decoration:none; border-bottom:1px solid var(--sage); transition:color .4s ease; }

        @keyframes rgFadeDown{ to{ opacity:1; transform:translateY(0); } }
        @keyframes rgFadeUp{ to{ opacity:1; transform:translateY(0); } }
        @keyframes rgFadeIn{ to{ opacity:1; } }
        @keyframes rgRotateDash{ to{ transform:rotate(360deg); } }
        @keyframes rgDrawLine{ to{ stroke-dashoffset:0; } }

        @media (prefers-reduced-motion: reduce){
            .rg-brand, .rg-headline, .rg-sub, .rg-seal, .rg-ticker, .rg-card{ animation-duration:.01ms !important; }
        }
        @media (max-width: 860px){
            .rg-inst{ min-height:300px; }
            .rg-ticker{ display:none; }
            .rg-field-row{ flex-direction:column; }
        }
    </style>

    <div class="rg-theme-toggle" onclick="rgToggleTheme()" role="button" aria-label="Toggle dark mode">
        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="4"/>
            <path stroke-linecap="round" d="M12 3v1m0 16v1m9-9h1M4 12H3m15.364-6.364l.707-.707M6.343 17.657l-.707.707m12.728 0l.707-.707M6.343 6.343l-.707-.707"/>
        </svg>
        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
        </svg>
    </div>

    <div class="registrar-reg-wrap">

        <div class="rg-form-panel">
            <div class="rg-card">
                <div class="rg-badge"><span class="dot"></span>New Account Request</div>
                <h1 class="rg-title">Request Registrar Access</h1>
                <div class="rg-rule"></div>
                <p class="rg-lede">Create your staff account. A registrar administrator will review your request before granting access.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="rg-field">
                        <label for="name">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Sokha Chan">
                        @error('name')<div class="rg-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="rg-field">
                        <label for="email">Staff Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="staff.id@university.edu">
                        @error('email')<div class="rg-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="rg-field-row">
                        <div class="rg-field">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                            @error('password')<div class="rg-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="rg-field">
                            <label for="password_confirmation">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                        </div>
                    </div>

                    <button class="rg-submit" type="submit">Submit Application &rarr;</button>

                    <div class="rg-foot">
                        Already have access? <a href="{{ route('login') }}">Sign in instead</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="rg-inst">
            <div class="rg-brand"><span class="rule"></span>Office of the Registrar</div>
            <div>
                <h1 class="rg-headline">Every record<br/>begins with an <em>application</em>.</h1>
                <p class="rg-sub">New staff accounts are reviewed before access is granted &mdash; your credentials aren't active until approved.</p>
                <div class="rg-seal-stage">
                    <div class="rg-seal">
                        <svg viewBox="0 0 180 180" fill="none">
                            <circle cx="90" cy="90" r="78" stroke="#7A9C74" stroke-width="1.4" stroke-dasharray="3 9" class="dash-ring"/>
                            <circle cx="90" cy="90" r="58" fill="#0f2445" stroke="#7A9C74" stroke-width="1.2"/>
                            <path class="quill-line" d="M62 108 C72 78, 96 66, 118 58" stroke="#7A9C74" stroke-width="2.4" stroke-linecap="round" fill="none"/>
                            <circle cx="118" cy="58" r="3.4" fill="#7A9C74"/>
                            <text x="90" y="132" text-anchor="middle" fill="#7A9C74" font-family="IBM Plex Mono, monospace" font-size="7.5" letter-spacing="1.8">PENDING REVIEW</text>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="rg-ticker">
                @isset($campuses)
                    @foreach($campuses as $i => $campus)
                        <span>CAMPUS <b>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</b> &middot; {{ strtoupper($campus->name) }}</span>
                    @endforeach
                @else
                    <span>CAMPUS <b>01</b> &middot; MAIN</span>
                    <span>CAMPUS <b>02</b> &middot; EAST</span>
                    <span>CAMPUS <b>03</b> &middot; SOUTH</span>
                @endisset
            </div>
        </div>

    </div>

    <script>
        (function(){
            var saved = localStorage.getItem('registrar-theme');
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            var theme = saved || (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-rg-theme', theme);
        })();
        function rgToggleTheme(){
            var html = document.documentElement;
            var isDark = html.getAttribute('data-rg-theme') === 'dark';
            var next = isDark ? 'light' : 'dark';
            html.setAttribute('data-rg-theme', next);
            localStorage.setItem('registrar-theme', next);
        }
    </script>
</x-guest-layout>

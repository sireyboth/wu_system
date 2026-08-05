<x-guest-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700;9..144,900&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap');

        :root{
            --ink-900:#0B1B34;
            --ink-800:#122a4d;
            --ink-700:#1b3862;
            --brass:#C9A227;
            --brass-bright:#E4C34A;
            --parchment:#F7F3E9;
            --parchment-dim:#EFE9DA;
            --slate-900:#232A36;
            --slate-500:#6b7280;
            --slate-300:#c7cdd6;

            --rp-form-bg:var(--parchment);
            --rp-form-bg-dim:var(--parchment-dim);
            --rp-form-text:var(--ink-900);
            --rp-form-subtext:var(--slate-500);
            --rp-form-border:#dcd5c2;
            --rp-form-placeholder:#a8a08c;
            --rp-input-bg:#FFFFFF;
            --rp-submit-bg:var(--ink-900);
            --rp-submit-bg-hover:#0d2140;
            --rp-submit-text:var(--parchment);
            --rp-left-1:var(--ink-700);
            --rp-left-2:var(--ink-900);
            --rp-left-3:#0a1730;
        }
        html[data-rp-theme="dark"]{
            --rp-form-bg:#14171d;
            --rp-form-bg-dim:#1c2029;
            --rp-form-text:#F2EEE3;
            --rp-form-subtext:#9aa3b2;
            --rp-form-border:#333947;
            --rp-form-placeholder:#5b6270;
            --rp-input-bg:#1c212b;
            --rp-submit-bg:var(--brass);
            --rp-submit-bg-hover:var(--brass-bright);
            --rp-submit-text:#161310;
            --rp-left-1:#132340;
            --rp-left-2:#060c1a;
            --rp-left-3:#04070f;
        }

        .registrar-wrap{
            position:fixed; inset:0; width:100vw; height:100vh;
            display:flex; flex-wrap:wrap; overflow-y:auto;
            font-family:'Inter',sans-serif; z-index:1;
        }

        .rp-theme-toggle{
            position:fixed; top:1.5rem; right:1.5rem; z-index:50;
            width:44px; height:44px; border-radius:50%;
            border:1px solid rgba(255,255,255,0.25);
            background:rgba(255,255,255,0.08);
            backdrop-filter:blur(6px);
            display:flex; align-items:center; justify-content:center;
            cursor:pointer; color:var(--brass);
            transition:transform .25s ease, background .25s ease;
        }
        .rp-theme-toggle:hover{ transform:scale(1.08); background:rgba(255,255,255,0.15); }
        .rp-theme-toggle svg{ width:20px; height:20px; }
        .rp-theme-toggle .icon-moon{ display:none; }
        html[data-rp-theme="dark"] .rp-theme-toggle .icon-sun{ display:none; }
        html[data-rp-theme="dark"] .rp-theme-toggle .icon-moon{ display:block; }

        .rp-left{
            flex:1 1 46%; min-width:320px; position:relative; min-height:480px;
            background:
                radial-gradient(1200px 800px at 15% -10%, var(--rp-left-1) 0%, transparent 55%),
                linear-gradient(160deg, var(--rp-left-2) 0%, var(--rp-left-3) 100%);
            color:var(--parchment); padding:clamp(2.5rem,5vw,5rem);
            display:flex; flex-direction:column; justify-content:space-between; overflow:hidden;
            transition:background .4s ease;
        }
        .rp-left::before{
            content:""; position:absolute; inset:0;
            background-image:repeating-linear-gradient(115deg, rgba(255,255,255,0.02) 0 1px, transparent 1px 6px);
            pointer-events:none;
        }
        .rp-brand{
            display:flex; align-items:center; gap:.85rem; letter-spacing:.22em; text-transform:uppercase;
            font-size:.7rem; font-weight:600; color:var(--brass);
            opacity:0; transform:translateY(-6px); animation:rpFadeDown .7s .15s cubic-bezier(.16,1,.3,1) forwards;
        }
        .rp-brand .rule{ width:26px; height:1px; background:var(--brass); }
        .rp-headline{
            margin:2.2rem 0 0; font-family:'Fraunces',serif; font-weight:600;
            font-size:clamp(2.1rem,3.4vw,3rem); line-height:1.05; letter-spacing:-0.01em; color:#fbf9f4;
            opacity:0; transform:translateY(10px); animation:rpFadeUp .8s .3s cubic-bezier(.16,1,.3,1) forwards;
        }
        .rp-headline em{ font-style:italic; color:var(--brass); font-weight:500; }
        .rp-sub{
            margin-top:1rem; max-width:34ch; color:#b9c2d4; font-size:.98rem; line-height:1.65;
            opacity:0; transform:translateY(10px); animation:rpFadeUp .8s .45s cubic-bezier(.16,1,.3,1) forwards;
        }
        .rp-seal-stage{ position:relative; width:180px; height:180px; margin:2.6rem 0 1rem; }
        .rp-ripple{ position:absolute; inset:0; border-radius:50%; border:1px solid var(--brass); opacity:0; animation:rpRipple 1.1s .95s cubic-bezier(.2,.7,.3,1) forwards; }
        .rp-seal{
            position:absolute; inset:0; opacity:0; transform:scale(2.6) rotate(-18deg);
            animation:rpStamp .62s .78s cubic-bezier(.34,1.4,.4,1) forwards;
            filter:drop-shadow(0 8px 18px rgba(0,0,0,.35));
        }
        .rp-seal svg{ width:100%; height:100%; display:block; }
        .rp-seal .ring-outer{ animation:rpSpin 60s linear infinite; transform-origin:90px 90px; }
        .rp-seal .ring-inner{ animation:rpSpin 40s linear infinite reverse; transform-origin:90px 90px; }
        .rp-ticker{
            margin-top:auto; padding-top:2.4rem; border-top:1px solid rgba(255,255,255,0.12);
            display:flex; flex-wrap:wrap; gap:.5rem 1.4rem; font-family:'IBM Plex Mono',monospace;
            font-size:.68rem; letter-spacing:.08em; color:#7d8aa3;
            opacity:0; animation:rpFadeUp .8s .9s cubic-bezier(.16,1,.3,1) forwards;
        }
        .rp-ticker b{ color:var(--brass); font-weight:500; }

        .rp-right{
            flex:1 1 54%; min-width:320px; background:var(--rp-form-bg);
            display:flex; align-items:center; justify-content:center; padding:clamp(2rem,6vw,4rem);
            transition:background .4s ease;
        }
        .rp-card{ width:100%; max-width:400px; opacity:0; transform:translateY(14px); animation:rpFadeUp .7s .2s cubic-bezier(.16,1,.3,1) forwards; }
        .rp-eyebrow{ display:flex; align-items:center; gap:.6rem; font-family:'IBM Plex Mono',monospace; font-size:.68rem; letter-spacing:.2em; text-transform:uppercase; color:var(--brass); font-weight:500; margin-bottom:1.1rem; }
        .rp-eyebrow .dot{ width:6px; height:6px; border-radius:50%; background:var(--brass); }
        .rp-title{ font-family:'Fraunces',serif; font-weight:600; font-size:2.3rem; letter-spacing:-0.01em; color:var(--rp-form-text); margin:0 0 .5rem; transition:color .4s ease; }
        .rp-rule{ width:44px; height:3px; background:var(--brass); margin-bottom:1.1rem; border-radius:2px; }
        .rp-lede{ color:var(--rp-form-subtext); font-size:.92rem; line-height:1.6; margin-bottom:1.8rem; transition:color .4s ease; }

        .rp-field{ position:relative; margin-bottom:1.3rem; }
        .rp-field label{ display:block; font-size:.68rem; font-weight:600; letter-spacing:.14em; text-transform:uppercase; color:var(--rp-form-subtext); margin-bottom:.5rem; transition:color .4s ease; }
        .rp-select-wrap{ position:relative; }
        .rp-field select, .rp-field input{
            width:100%; padding:.85rem 1rem; background:var(--rp-input-bg); border:1.5px solid var(--rp-form-border); border-radius:10px;
            font-family:'Inter',sans-serif; font-size:.96rem; color:var(--rp-form-text); outline:none; appearance:none;
            transition:border-color .2s ease, box-shadow .2s ease, background .3s ease, color .4s ease;
            box-shadow:0 1px 2px rgba(15,20,30,.04);
        }
        .rp-field select{ cursor:pointer; padding-right:2.4rem; }
        .rp-field select option{ background:var(--rp-input-bg); color:var(--rp-form-text); }
        .rp-select-wrap::after{
            content:""; position:absolute; right:1.1rem; top:calc(50% + 12px); translate:0 -65%; width:8px; height:8px;
            border-right:1.5px solid var(--rp-form-subtext); border-bottom:1.5px solid var(--rp-form-subtext); transform:rotate(45deg); pointer-events:none;
        }
        .rp-field input::placeholder{ color:var(--rp-form-placeholder); }
        .rp-field select:focus, .rp-field input:focus{ border-color:var(--brass); box-shadow:0 0 0 4px rgba(201,162,39,0.18); }
        .rp-error{ color:#e0635f; font-size:.76rem; margin-top:.5rem; }

        .rp-row-between{ display:flex; align-items:center; justify-content:space-between; margin:.4rem 0 1.4rem; }
        .rp-remember{ display:flex; align-items:center; gap:.55rem; font-size:.8rem; color:var(--rp-form-subtext); cursor:pointer; user-select:none; }
        .rp-remember input{ width:15px; height:15px; accent-color:var(--brass); border:none; padding:0; }
        .rp-recover{ font-size:.72rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:var(--rp-form-text); text-decoration:none; border-bottom:1px solid var(--brass); padding-bottom:1px; transition:color .4s ease; }
        .rp-recover:hover{ color:var(--brass); }

        .rp-submit{
            margin-top:.2rem; width:100%; padding:1.05rem; background:var(--rp-submit-bg); color:var(--rp-submit-text); border:none;
            border-radius:6px; font-family:'Inter',sans-serif; font-weight:600; font-size:.78rem; letter-spacing:.16em;
            text-transform:uppercase; cursor:pointer; position:relative; overflow:hidden;
            transition:transform .15s ease, box-shadow .3s ease, background .3s ease, color .3s ease;
            box-shadow:0 10px 24px -8px rgba(11,27,52,.5);
        }
        .rp-submit::before{
            content:""; position:absolute; inset:0; background:linear-gradient(100deg, transparent, rgba(255,255,255,.25), transparent);
            translate:-120% 0; transition:translate .6s ease;
        }
        .rp-submit:hover::before{ translate:120% 0; }
        .rp-submit:hover{ background:var(--rp-submit-bg-hover); }
        .rp-submit:active{ transform:scale(.98); }

        .rp-foot{ margin-top:2rem; padding-top:1.6rem; border-top:1px solid var(--rp-form-bg-dim); text-align:center; font-size:.85rem; color:var(--rp-form-subtext); transition:color .4s ease, border-color .4s ease; }
        .rp-foot a{ color:var(--rp-form-text); font-weight:700; text-decoration:none; border-bottom:1px solid var(--brass); transition:color .4s ease; }

        @keyframes rpFadeDown{ to{ opacity:1; transform:translateY(0); } }
        @keyframes rpFadeUp{ to{ opacity:1; transform:translateY(0); } }
        @keyframes rpStamp{ 0%{opacity:0; transform:scale(2.6) rotate(-18deg);} 55%{opacity:1;} 100%{opacity:1; transform:scale(1) rotate(0deg);} }
        @keyframes rpRipple{ 0%{opacity:.9; transform:scale(.6);} 100%{opacity:0; transform:scale(1.9);} }
        @keyframes rpSpin{ to{ transform:rotate(360deg); } }

        @media (prefers-reduced-motion: reduce){
            .rp-brand, .rp-headline, .rp-sub, .rp-ripple, .rp-seal, .rp-ticker, .rp-card{ animation-duration:.01ms !important; }
        }
        @media (max-width: 860px){
            .rp-left{ min-height:340px; }
            .rp-ticker{ display:none; }
        }
    </style>

    <div class="rp-theme-toggle" onclick="rpToggleTheme()" role="button" aria-label="Toggle dark mode">
        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="4"/>
            <path stroke-linecap="round" d="M12 3v1m0 16v1m9-9h1M4 12H3m15.364-6.364l.707-.707M6.343 17.657l-.707.707m12.728 0l.707-.707M6.343 6.343l-.707-.707"/>
        </svg>
        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
        </svg>
    </div>

    <div class="registrar-wrap">

        <div class="rp-left">
            <div class="rp-brand"><span class="rule"></span>Office of the Registrar</div>

            <div>
                <h1 class="rp-headline">Records held<br/>to a <em>higher</em><br/>standard.</h1>
                <p class="rp-sub">Enrollment, transcripts, and academic history for every campus &mdash; kept accurate, current, and accountable.</p>

                <div class="rp-seal-stage">
                    <div class="rp-ripple"></div>
                    <div class="rp-seal">
                        <svg viewBox="0 0 180 180" fill="none">
                            <circle cx="90" cy="90" r="86" stroke="#C9A227" stroke-width="1.2" class="ring-outer" stroke-dasharray="4 6"/>
                            <circle cx="90" cy="90" r="70" stroke="#C9A227" stroke-width="1" class="ring-inner" opacity="0.6"/>
                            <circle cx="90" cy="90" r="58" fill="#0f2445" stroke="#C9A227" stroke-width="1.5"/>
                            <path d="M90 42 L98 66 L124 66 L103 81 L111 105 L90 90 L69 105 L77 81 L56 66 L82 66 Z" fill="#C9A227"/>
                            <text x="90" y="132" text-anchor="middle" fill="#C9A227" font-family="IBM Plex Mono, monospace" font-size="8" letter-spacing="2">CERTIFIED</text>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rp-ticker">
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

        <div class="rp-right">
            <div class="rp-card">
                <div class="rp-eyebrow"><span class="dot"></span>Staff Portal &middot; Restricted Access</div>
                <h1 class="rp-title">Registrar Sign In</h1>
                <div class="rp-rule"></div>
                <p class="rp-lede">Authenticate with your staff credentials to access student records, enrollment, and campus dashboards.</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf


                    <div class="rp-field">
                        <label for="email">Staff Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="staff.id@university.edu">
                        @error('email')<div class="rp-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="rp-field">
                        <label for="password">Security Key</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                        @error('password')<div class="rp-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="rp-row-between">
                        <label class="rp-remember">
                            <input type="checkbox" name="remember"> Keep me signed in
                        </label>
                        <a class="rp-recover" href="{{ route('password.request') }}">Recover access</a>
                    </div>

                    <button class="rp-submit" type="submit">Enter Records System &rarr;</button>

                    <div class="rp-foot">
                        New staff member? <a href="{{ route('register') }}">Request registrar access</a>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        (function(){
            var saved = localStorage.getItem('registrar-theme');
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            var theme = saved || (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-rp-theme', theme);
        })();
        function rpToggleTheme(){
            var html = document.documentElement;
            var isDark = html.getAttribute('data-rp-theme') === 'dark';
            var next = isDark ? 'light' : 'dark';
            html.setAttribute('data-rp-theme', next);
            localStorage.setItem('registrar-theme', next);
        }
    </script>
</x-guest-layout>

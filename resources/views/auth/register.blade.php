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
            --rg-badge-bg:var(--sage-tint);
            --rg-badge-text:var(--sage);
            --bento-card-bg: #FFFFFF;
        }
        html[data-rg-theme="dark"]{
            --rg-form-bg:#0a0d14;
            --rg-form-bg-dim:#121620;
            --rg-form-text:#F2EEE3;
            --rg-form-subtext:#9aa3b2;
            --rg-form-border:#222834;
            --rg-form-placeholder:#4b5363;
            --rg-input-bg:#121722;
            --rg-submit-bg:var(--sage-bright);
            --rg-submit-bg-hover:#8fb389;
            --rg-submit-text:#0e1710;
            --rg-badge-bg:#1c2a1e;
            --rg-badge-text:var(--sage-bright);
            --bento-card-bg: #121620;
        }

        .register-bento-wrap {
            position: fixed; inset: 0; width: 100vw; height: 100vh;
            background: var(--rg-form-bg);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Inter', sans-serif; z-index: 1; overflow-y: auto;
            padding: 2rem; transition: background .4s ease;
        }

        /* Bento Grid Layout Configuration */
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
            width: 100%;
            max-width: 1040px;
        }

        .bento-card {
            background: var(--bento-card-bg);
            border: 1px solid var(--rg-form-border);
            border-radius: 20px;
            padding: 2.2rem;
            position: relative;
            overflow: hidden;
            transition: background .4s ease, border-color .4s ease, transform .3s cubic-bezier(.16,1,.3,1), box-shadow .3s ease;
        }
        .bento-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.04);
        }

        /* Form Card Layout Spanning */
        .bento-main-form {
            grid-column: span 2;
            grid-row: span 2;
        }

        /* Institutional Badging */
        .rg-badge {
            display: inline-flex; align-items: center; gap: .5rem; background: var(--rg-badge-bg); color: var(--rg-badge-text);
            padding: .4rem .8rem; border-radius: 999px; font-family: 'IBM Plex Mono', monospace; font-size: .66rem;
            font-weight: 500; letter-spacing: .14em; text-transform: uppercase; margin-bottom: 1.5rem;
            transition: background .4s ease, color .4s ease;
        }
        .rg-badge .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--rg-badge-text); animation: pulse 2s infinite; }

        .rg-title { font-family: 'Fraunces', serif; font-weight: 600; font-size: 2.2rem; letter-spacing: -0.01em; color: var(--rg-form-text); margin: 0 0 .5rem; transition: color .4s ease; }
        .rg-lede { color: var(--rg-form-subtext); font-size: .92rem; line-height: 1.6; margin-bottom: 2.2rem; transition: color .4s ease; }

        /* Dynamic Input Row Layouts */
        .rg-field-row { display: flex; gap: 1rem; }
        .rg-field-row .rg-field { flex: 1; min-width: 0; }
        .rg-field { position: relative; margin-bottom: 1.3rem; }
        .rg-field label { display: block; font-size: .68rem; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--rg-form-subtext); margin-bottom: .5rem; transition: color .4s ease; }

        .rg-field input {
            width: 100%; padding: .85rem 1.1rem; background: var(--rg-input-bg); border: 1.5px solid var(--rg-form-border); border-radius: 12px;
            font-family: 'Inter', sans-serif; font-size: .95rem; color: var(--rg-form-text); outline: none;
            transition: border-color .2s ease, box-shadow .2s ease, background .3s ease, color .4s ease;
        }
        .rg-field input::placeholder { color: var(--rg-form-placeholder); }
        .rg-field input:focus { border-color: var(--sage); box-shadow: 0 0 0 4px rgba(95, 125, 90, 0.14); }
        html[data-rg-theme="dark"] .rg-field input:focus { box-shadow: 0 0 0 4px rgba(122, 156, 116, 0.18); }
        .rg-error { color: #e0635f; font-size: .74rem; margin-top: .4rem; }

        .rg-agree { display: flex; align-items: flex-start; gap: .6rem; font-size: .8rem; color: var(--rg-form-subtext); line-height: 1.5; margin: .4rem 0 1.6rem; }
        .rg-agree input { width: 15px; height: 15px; margin-top: 2px; accent-color: var(--sage); flex-shrink: 0; }

        .rg-submit {
            width: 100%; padding: 1.1rem; background: var(--rg-submit-bg); color: var(--rg-submit-text);
            border: none; border-radius: 12px; font-family: 'Inter', sans-serif; font-weight: 600; font-size: .8rem;
            letter-spacing: .16em; text-transform: uppercase; cursor: pointer; position: relative; overflow: hidden;
            transition: transform .15s ease, box-shadow .3s ease, background .3s ease;
        }
        .rg-submit:hover { background: var(--rg-submit-bg-hover); }
        .rg-submit:active { transform: scale(.98); }

        /* Right Brand Panel (Top Right Bento) */
        .bento-brand-panel {
            background: linear-gradient(135deg, var(--ink-700) 0%, var(--ink-900) 100%);
            color: #FFFFFF;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .bento-brand-panel::before {
            content: ""; position: absolute; inset: 0;
            background-image: repeating-linear-gradient(115deg, rgba(255,255,255,0.02) 0 1px, transparent 1px 6px);
        }
        .brand-meta { font-family: 'IBM Plex Mono', monospace; font-size: 0.65rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--sage-bright); }
        .brand-quote { font-family: 'Fraunces', serif; font-size: 1.4rem; line-height: 1.35; font-weight: 500; margin-top: 2rem; }
        .brand-quote em { font-style: italic; color: var(--parchment); opacity: 0.95; }

        /* Animated Pending Seal (Bottom Right Bento) */
        .bento-seal-panel {
            display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem;
        }
        .animated-seal-container { width: 110px; height: 110px; position: relative; }
        .animated-seal-container svg { width: 100%; height: 100%; }
        .dash-ring { animation: rotateSeal 25s linear infinite; transform-origin: 55px 55px; }
        .inner-shield { fill: var(--rg-form-bg-dim); stroke: var(--rg-form-border); transition: fill .4s, stroke .4s; }

        /* Global Theme Switcher */
        .rg-theme-toggle{
            position:fixed; top:1.5rem; right:1.5rem; z-index:50; width:44px; height:44px; border-radius:50%;
            border:1px solid var(--rg-form-border); background: var(--bento-card-bg);
            display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--rg-form-text);
            transition:transform .25s ease, background .4s ease, color .4s ease;
        }
        .rg-theme-toggle:hover{ transform:scale(1.08); }
        .rg-theme-toggle svg{ width:20px; height:20px; }
        .rg-theme-toggle .icon-moon{ display:none; }
        html[data-rg-theme="dark"] .rg-theme-toggle .icon-sun{ display:none; }
        html[data-rg-theme="dark"] .rg-theme-toggle .icon-moon{ display:block; }

        .rg-foot { margin-top: 1.4rem; text-align: center; font-size: .82rem; color: var(--rg-form-subtext); }
        .rg-foot a { color: var(--rg-form-text); font-weight: 700; text-decoration: none; border-bottom: 1px dashed var(--rg-badge-text); }

        /* Sequential Load Animations */
        .bento-card:nth-child(1) { opacity: 0; transform: translateY(15px); animation: bentoFlyIn .6s cubic-bezier(.16,1,.3,1) forwards; }
        .bento-card:nth-child(2) { opacity: 0; transform: translateY(15px); animation: bentoFlyIn .6s .1s cubic-bezier(.16,1,.3,1) forwards; }
        .bento-card:nth-child(3) { opacity: 0; transform: translateY(15px); animation: bentoFlyIn .6s .2s cubic-bezier(.16,1,.3,1) forwards; }

        @keyframes bentoFlyIn { to { opacity: 1; transform: translateY(0); } }
        @keyframes rotateSeal { to { transform: rotate(360deg); } }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

        @media (max-width: 900px) {
            .bento-grid { grid-template-columns: 1fr; gap: 1rem; }
            .bento-main-form { grid-column: span 1; padding: 1.8rem; }
            .bento-brand-panel, .bento-seal-panel { display: none; }
        }
    </style>

    <!-- Theme Controller -->
    <div class="rg-theme-toggle" onclick="rgToggleTheme()" role="button" aria-label="Toggle dark mode">
        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="4"/>
            <path stroke-linecap="round" d="M12 3v1m0 16v1m9-9h1M4 12H3m15.364-6.364l.707-.707M6.343 17.657l-.707.707m12.728 0l.707-.707M6.343 6.343l-.707-.707"/>
        </svg>
        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
        </svg>
    </div>

    <div class="register-bento-wrap">
        <div class="bento-grid">

            <!-- NODE 1: REGISTRATION INTERACTION CARD -->
            <div class="bento-card bento-main-form">
                <div class="rg-badge"><span class="dot"></span>New Account Request</div>
                <h1 class="rg-title">Request Registrar Access</h1>
                <p class="rg-lede">Submit your administrative profile. Your registration parameters must be validated prior to core node provisioning.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Full Name Field -->
                    <div class="rg-field">
                        <label for="name">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Sokha Chan">
                        @error('name')<div class="rg-error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Staff Email Field -->
                    <div class="rg-field">
                        <label for="email">Staff E-mail Identifier</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="staff.id@university.edu">
                        @error('email')<div class="rg-error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Password Fields Split Row -->
                    <div class="rg-field-row">
                        <div class="rg-field">
                            <label for="password">Create Password</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                            @error('password')<div class="rg-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="rg-field">
                            <label for="password_confirmation">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                        </div>
                    </div>

                    <!-- User Confirmation Checkbox -->
                    <label class="rg-agree">
                        <input type="checkbox" name="agree" required>
                        <span>I verify that the provided organizational data is accurate and understand that access is contingent upon administrative clearance.</span>
                    </label>

                    <button class="rg-submit" type="submit">Submit Application &rarr;</button>

                    <div class="rg-foot">
                        Already have access keys? <a href="{{ route('login') }}">Sign in instead</a>
                    </div>
                </form>
            </div>

            <!-- NODE 2: STRATEGIC INSCRIPTION CARD -->
            <div class="bento-card bento-brand-panel">
                <div class="brand-meta">Security Protocol</div>
                <div class="brand-quote">
                    Every immutable academic ledger entry <em>originates from a verified identity</em>.
                </div>
                <div class="brand-meta" style="color: rgba(255,255,255,0.45);">Office of the Registrar</div>
            </div>

            <!-- NODE 3: GRAPHICAL PENDING SECURITY INSIGNIA -->
            <div class="bento-card bento-seal-panel">
                <div class="animated-seal-container">
                    <svg viewBox="0 0 110 110" fill="none">
                        <circle cx="55" cy="55" r="48" stroke="var(--rg-badge-text)" stroke-width="1.2" stroke-dasharray="4 6" class="dash-ring"/>
                        <circle cx="55" cy="55" r="36" fill="var(--rg-form-bg-dim)" stroke="var(--rg-form-border)" stroke-width="1"/>
                        <path d="M42 62 C48 46, 62 40, 74 36" stroke="var(--rg-badge-text)" stroke-width="1.8" stroke-linecap="round" fill="none"/>
                        <circle cx="74" cy="36" r="2" fill="var(--rg-badge-text)"/>
                        <text x="55" y="76" text-anchor="middle" fill="var(--rg-badge-text)" font-family="IBM Plex Mono, monospace" font-size="5" letter-spacing="1">VERIFYING</text>
                    </svg>
                </div>
                <div style="font-family: 'IBM Plex Mono', monospace; font-size: 0.6rem; letter-spacing: 0.12em; color: var(--rg-form-subtext); margin-top: 1.2rem; text-align: center; text-transform: uppercase;">
                    Awaiting Review
                </div>
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

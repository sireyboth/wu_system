/**
 * Public attendance scan page — no login. The QR the lecturer projects
 * encodes a link to this page with ?token=... already filled in; the
 * student only has to type their own student code.
 *
 * Location is requested on page load AND freshly on every scan. A website
 * can't force the browser's permission prompt once a student has chosen
 * "Block" — the browser just answers "denied" without asking — so when
 * that happens the page explains how to turn it back on instead of failing
 * with a generic error. Whether a location is actually *required* is still
 * decided server-side (only campuses with a geofence need one).
 */
(() => {
    'use strict';

    const DOM = {
        codeInput: document.getElementById('attendStudentCode'),
        tokenInput: document.getElementById('attendToken'),
        submitBtn: document.getElementById('attendSubmitBtn'),
        result: document.getElementById('attendResult'),
        locationStatus: document.getElementById('attendLocationStatus'),
        locationHelp: document.getElementById('attendLocationHelp'),
        locationHelpTitle: document.getElementById('attendLocationHelpTitle'),
        locationHelpSteps: document.getElementById('attendLocationHelpSteps'),
        locationRetryBtn: document.getElementById('attendLocationRetryBtn'),
    };

    // A random id kept in this browser's own storage — not a fingerprint
    // scraped from hardware, just "have we seen this browser before".
    // Used server-side only for one thing: catching the same device
    // scanning in on behalf of multiple different students.
    function getDeviceId() {
        try {
            const key = 'wu_attend_device_id';
            let id = localStorage.getItem(key);
            if (!id) {
                id = (crypto.randomUUID?.() ?? `${Date.now()}-${Math.random().toString(36).slice(2)}`);
                localStorage.setItem(key, id);
            }
            return id;
        } catch {
            return null; // private browsing / storage blocked — scan still works, just unverified
        }
    }

    // maximumAge: 0 — always a fresh fix, never a cached one from earlier.
    // Resolves (never rejects) to { ok: true, latitude, longitude } or
    // { ok: false, reason: 'denied' | 'unavailable' | 'timeout' | 'unsupported' }.
    function requestLocation() {
        return new Promise((resolve) => {
            if (!navigator.geolocation) {
                resolve({ ok: false, reason: 'unsupported' });
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (position) => resolve({ ok: true, latitude: position.coords.latitude, longitude: position.coords.longitude }),
                (error) => resolve({
                    ok: false,
                    reason: error.code === 1 ? 'denied' : error.code === 3 ? 'timeout' : 'unavailable',
                }),
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        });
    }

    const LOCATION_HELP = {
        denied: {
            title: 'ទីតាំងត្រូវបានបិទសម្រាប់គេហទំព័រនេះ (Location is blocked for this page)',
            steps: [
                'Android / Chrome: tap the lock (or settings) icon beside the address bar → Permissions → Location → Allow.',
                'iPhone / Safari: tap "aA" in the address bar → Website Settings → Location → Allow. (Also check Settings → Privacy & Security → Location Services is on.)',
                'Then come back here and tap "Try location again", or reload this page.',
            ],
        },
        timeout: {
            title: 'រកមិនឃើញទីតាំងទាន់ពេលវេលា (Could not get your location in time)',
            steps: [
                'Make sure GPS / Location is switched on for your phone.',
                'Move near a window or outdoors for a better signal, then tap "Try location again".',
            ],
        },
        unavailable: {
            title: 'មិនអាចកំណត់ទីតាំងរបស់អ្នកបានទេ (Your location is unavailable)',
            steps: [
                'Turn on Location / GPS in your phone settings.',
                'Then tap "Try location again".',
            ],
        },
        unsupported: {
            title: 'កម្មវិធីរុករកនេះមិនគាំទ្រទីតាំងទេ (This browser cannot share location)',
            steps: [
                'Open this page in Chrome or Safari, then scan the QR code again.',
            ],
        },
    };

    function showLocationProblem(reason) {
        const help = LOCATION_HELP[reason] ?? LOCATION_HELP.unavailable;
        DOM.locationHelpTitle.textContent = help.title;
        DOM.locationHelpSteps.replaceChildren(...help.steps.map((text) => {
            const li = document.createElement('li');
            li.textContent = text;
            return li;
        }));
        DOM.locationHelp.classList.remove('hidden');
    }

    function clearLocationProblem() {
        DOM.locationHelp.classList.add('hidden');
    }

    function setLocationStatus(text, tone) {
        const tones = {
            neutral: 'mt-3 text-xs text-center text-neutral-400 dark:text-neutral-500',
            ok: 'mt-3 text-xs text-center font-semibold text-emerald-600 dark:text-emerald-400',
            bad: 'mt-3 text-xs text-center font-semibold text-amber-600 dark:text-amber-400',
        };
        DOM.locationStatus.className = tones[tone] ?? tones.neutral;
        DOM.locationStatus.textContent = text;
    }

    // Asks the browser for a fresh position and updates the on-page status /
    // help box to match. Used both at page load and on every scan.
    async function refreshLocation() {
        setLocationStatus('កំពុងស្នើសុំទីតាំង… (Checking your location…)', 'neutral');
        const location = await requestLocation();

        if (location.ok) {
            clearLocationProblem();
            setLocationStatus('ទីតាំងបានបើក (Location is on) ✓', 'ok');
        } else {
            showLocationProblem(location.reason);
            setLocationStatus('ទីតាំងមិនអាចប្រើបាន (Location is off)', 'bad');
        }
        return location;
    }

    function showResult(success, message) {
        DOM.result.classList.remove('hidden');
        DOM.result.className = success
            ? 'mt-5 px-4 py-4 rounded-2xl border text-sm text-center font-semibold border-emerald-300 bg-emerald-50 text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400'
            : 'mt-5 px-4 py-4 rounded-2xl border text-sm text-center font-semibold border-rose-300 bg-rose-50 text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-400';
        DOM.result.textContent = message;
    }

    async function submit() {
        const code = DOM.codeInput.value.trim();
        const token = DOM.tokenInput.value.trim();

        if (!code) {
            showResult(false, 'Enter your student code first.');
            return;
        }
        if (!token) {
            showResult(false, 'No QR token found — scan the lecturer\'s QR code again rather than opening this page directly.');
            return;
        }

        DOM.submitBtn.disabled = true;
        try {
            const location = await refreshLocation();
            const response = await fetch('/api/v1/attend/scan', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({
                    token,
                    student_code: code,
                    device_id: getDeviceId(),
                    latitude: location.ok ? location.latitude : null,
                    longitude: location.ok ? location.longitude : null,
                }),
            });
            const json = await response.json().catch(() => null);
            showResult(response.ok, json?.message || (response.ok ? 'Marked present.' : 'Something went wrong.'));

            // The server says this class needs a location but we couldn't
            // supply one — make sure the fix-it box is showing.
            if (!response.ok && json?.reason === 'location_required' && !location.ok) {
                showLocationProblem(location.reason);
            }
        } catch (err) {
            showResult(false, 'Network error — check your connection and try again.');
        } finally {
            DOM.submitBtn.disabled = false;
        }
    }

    DOM.submitBtn?.addEventListener('click', submit);
    DOM.codeInput?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') submit();
    });
    DOM.locationRetryBtn?.addEventListener('click', refreshLocation);

    // Ask straight away so the browser's prompt appears as soon as the
    // student lands here, not only after they tap the button.
    refreshLocation();

    // Where supported, notice when the student fixes the setting in their
    // browser and re-check on its own — no reload needed.
    try {
        navigator.permissions?.query({ name: 'geolocation' })
            .then((status) => { status.onchange = () => refreshLocation(); })
            .catch(() => {});
    } catch {
        // Permissions API unavailable — the Try again button still works.
    }
})();

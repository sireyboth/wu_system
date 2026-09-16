/**
 * Public attendance scan page — no login. The QR the lecturer projects
 * encodes a link to this page with ?token=... already filled in; the
 * student only has to type their own student code.
 */
(() => {
    'use strict';

    const DOM = {
        codeInput: document.getElementById('attendStudentCode'),
        tokenInput: document.getElementById('attendToken'),
        submitBtn: document.getElementById('attendSubmitBtn'),
        result: document.getElementById('attendResult'),
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

    // Only some campuses require a location at all (see Campus::hasGeofence()
    // server-side) — so this never blocks the scan itself here. If the
    // browser can't or won't give a position, we just submit without one;
    // the backend decides whether that's actually required for this class.
    function getLocation() {
        return new Promise((resolve) => {
            if (!navigator.geolocation) {
                resolve(null);
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (position) => resolve({ latitude: position.coords.latitude, longitude: position.coords.longitude }),
                () => resolve(null),
                { enableHighAccuracy: true, timeout: 8000, maximumAge: 30000 }
            );
        });
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
            const location = await getLocation();
            const response = await fetch('/api/v1/attend/scan', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({
                    token,
                    student_code: code,
                    device_id: getDeviceId(),
                    latitude: location?.latitude ?? null,
                    longitude: location?.longitude ?? null,
                }),
            });
            const json = await response.json().catch(() => null);
            showResult(response.ok, json?.message || (response.ok ? 'Marked present.' : 'Something went wrong.'));
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
})();

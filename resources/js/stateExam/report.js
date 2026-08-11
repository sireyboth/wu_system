/**
 * Admin-only exam attendance report page (charts + KPIs).
 * Pulls from /api/v1/exam-states/report (per-session present/absent summary,
 * see Controller::summarize()) and /api/v1/exam-states (room list, for room
 * count + majors breakdown). Chart.js is loaded globally via CDN in the Blade view.
 */

const REPORT_URL = window.EXAM_STATE_REPORT_URL;
const ROOMS_URL = '/api/v1/exam-states?per_page=1000';

const els = {
    kpiRooms: document.getElementById('kpiTotalRooms'),
    kpiStudents: document.getElementById('kpiTotalStudents'),
    kpiAbsent: document.getElementById('kpiTotalAbsent'),
    kpiRate: document.getElementById('kpiAttendanceRate'),
    tableBody: document.getElementById('sessionTableBody'),
    refreshBtn: document.getElementById('refreshReportBtn'),
    refreshIcon: document.getElementById('refreshReportIcon'),
};

let sessionBarChart = null;
let overallDoughnutChart = null;
let majorBarChart = null;

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;');
}

async function fetchJson(url) {
    const res = await fetch(url, { headers: { Accept: 'application/json' } });
    if (!res.ok) throw new Error(`Request failed: ${res.status}`);
    return res.json();
}

function renderKpis(sessions, rooms) {
    const totalRooms = rooms.length;
    const totalStudents = sessions[0]?.total ?? 0;
    const totalAbsent = sessions.reduce((sum, s) => sum + (s.absent ?? 0), 0);
    const totalPresent = sessions.reduce((sum, s) => sum + (s.present ?? 0), 0);
    const totalPossible = sessions.reduce((sum, s) => sum + (s.total ?? 0), 0);
    const overallRate = totalPossible > 0 ? Math.round((totalPresent / totalPossible) * 100) : 0;

    if (els.kpiRooms) els.kpiRooms.textContent = totalRooms;
    if (els.kpiStudents) els.kpiStudents.textContent = totalStudents;
    if (els.kpiAbsent) els.kpiAbsent.textContent = totalAbsent;
    if (els.kpiRate) els.kpiRate.textContent = `${overallRate}%`;

    return { totalPresent, totalAbsent, overallRate };
}

function renderTable(sessions) {
    if (!els.tableBody) return;

    if (!sessions.length) {
        els.tableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-10 text-center text-neutral-400">No data available.</td></tr>';
        return;
    }

    els.tableBody.innerHTML = sessions.map((s) => `
        <tr>
            <td class="px-6 py-4 font-bold text-neutral-900 dark:text-white">${escapeHtml(s.label)}</td>
            <td class="px-6 py-4 font-mono">${escapeHtml(s.total)}</td>
            <td class="px-6 py-4 font-mono text-emerald-600 dark:text-emerald-400 font-bold">${escapeHtml(s.present)}</td>
            <td class="px-6 py-4 font-mono text-rose-600 dark:text-rose-400 font-bold">${escapeHtml(s.absent)}</td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-2 rounded-full bg-neutral-100 dark:bg-white/10 overflow-hidden max-w-[120px]">
                        <div class="h-full rounded-full bg-emerald-500" style="width:${s.percent ?? 0}%"></div>
                    </div>
                    <span class="text-xs font-bold text-neutral-600 dark:text-neutral-300">${s.percent ?? 0}%</span>
                </div>
            </td>
        </tr>`).join('');
}

function renderSessionBarChart(sessions) {
    const canvas = document.getElementById('sessionBarChart');
    if (!canvas) return;

    sessionBarChart?.destroy();
    sessionBarChart = new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: sessions.map((s) => s.label),
            datasets: [
                {
                    label: 'Present',
                    data: sessions.map((s) => s.present),
                    backgroundColor: '#10b981',
                    borderRadius: 6,
                },
                {
                    label: 'Absent',
                    data: sessions.map((s) => s.absent),
                    backgroundColor: '#f43f5e',
                    borderRadius: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { color: '#737373', usePointStyle: true } } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#737373' } },
                y: { beginAtZero: true, grid: { color: 'rgba(115,115,115,0.1)' }, ticks: { color: '#737373', precision: 0 } },
            },
        },
    });
}

function renderOverallDoughnut(totalPresent, totalAbsent) {
    const canvas = document.getElementById('overallDoughnutChart');
    if (!canvas) return;

    overallDoughnutChart?.destroy();
    overallDoughnutChart = new Chart(canvas.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent'],
            datasets: [{
                data: [totalPresent, totalAbsent],
                backgroundColor: ['#10b981', '#f43f5e'],
                borderWidth: 0,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { position: 'bottom', labels: { color: '#737373', usePointStyle: true } } },
        },
    });
}

function renderMajorBarChart(rooms) {
    const canvas = document.getElementById('majorBarChart');
    if (!canvas) return;

    const counts = {};
    rooms.forEach((room) => {
        const major = room.major || 'Unknown';
        counts[major] = (counts[major] || 0) + 1;
    });

    const labels = Object.keys(counts);
    const data = Object.values(counts);

    majorBarChart?.destroy();
    majorBarChart = new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Rooms',
                data,
                backgroundColor: '#6366f1',
                borderRadius: 6,
                barThickness: 28,
            }],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { color: 'rgba(115,115,115,0.1)' }, ticks: { color: '#737373', precision: 0 } },
                y: { grid: { display: false }, ticks: { color: '#737373' } },
            },
        },
    });
}

async function loadReport() {
    els.refreshIcon?.classList.add('animate-spin');
    try {
        const [sessions, roomsResponse] = await Promise.all([
            fetchJson(REPORT_URL),
            fetchJson(ROOMS_URL),
        ]);

        const rooms = Array.isArray(roomsResponse.data) ? roomsResponse.data : [];

        const { totalPresent, totalAbsent } = renderKpis(sessions, rooms);
        renderTable(sessions);
        renderSessionBarChart(sessions);
        renderOverallDoughnut(totalPresent, totalAbsent);
        renderMajorBarChart(rooms);
    } catch (err) {
        console.error('[stateExam report] failed to load:', err);
    } finally {
        els.refreshIcon?.classList.remove('animate-spin');
    }
}

els.refreshBtn?.addEventListener('click', loadReport);

loadReport();

// Keep the report current for whoever's leaving this page open (e.g. on a display screen).
const AUTO_REFRESH_MS = 1 * 60 * 1000;
setInterval(loadReport, AUTO_REFRESH_MS);

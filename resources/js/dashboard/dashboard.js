// resources/js/dashboard/dashboard.js

document.addEventListener('DOMContentLoaded', () => {
    loadDashboard();
});

async function loadDashboard() {
    try {
        // Change: Removed "/stats" to match your apiResource 'index' route
        const res = await fetch('/api/dashboard'); 
        
        if (!res.ok) throw new Error(`Server Error: ${res.status}`);

        const json = await res.json();
        
        // Safety check to ensure data exists before parsing
        if (!json.success) {
            console.error("API Error:", json.message);
            return;
        }

        const data = json.daily;
        const history = json.history;

        // Populate Daily
        document.getElementById('d-revenue').innerText = `$${parseFloat(data.revenue || 0).toLocaleString()}`;
        document.getElementById('d-occupancy').innerText = `${data.occupancy || 0}%`;
        document.getElementById('d-available').innerText = data.available || 0;
        document.getElementById('d-sold').innerText = data.rooms_sold || 0;
        document.getElementById('d-checkin').innerText = data.checkins || 0;
        document.getElementById('d-checkout').innerText = data.checkouts || 0;

        // Populate History
        document.getElementById('h-total-rev').innerText = `$${parseFloat(history.total_revenue_all_time || 0).toLocaleString()}`;
        document.getElementById('h-total-bookings').innerText = history.total_bookings_count || 0;
        document.getElementById('h-total-customers').innerText = history.total_customers_served || 0;

        if (json.chart) {
            renderChart(json.chart);
        }
    } catch (error) {
        console.error("Fetch Error:", error);
    }
}

function renderChart(chartData) {
    const canvas = document.getElementById('historyChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(d => d.date),
            datasets: [{
                label: 'Daily Revenue',
                data: chartData.map(d => d.total),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#737373' } },
                y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#737373' } }
            }
        }
    });
}
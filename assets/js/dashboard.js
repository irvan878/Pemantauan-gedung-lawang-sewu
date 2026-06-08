// ==========================================
// CLOCK & SIDEBAR (shared)
// ==========================================
setInterval(() => {
    const now = new Date();
    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const dateStr = now.toLocaleDateString('id-ID', dateOptions);
    const timeStr = now.toLocaleTimeString('id-ID').replace(/\./g, ':');
    const dateElement = document.getElementById('live-date');
    const timeElement = document.getElementById('live-time');
    if (dateElement) dateElement.innerText = dateStr;
    if (timeElement) timeElement.innerText = `${timeStr} WIB`;
}, 1000);

const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('main-content');
const topAppBar = document.getElementById('top-app-bar');
const sidebarToggle = document.getElementById('sidebar-toggle');
const toggleIcon = document.getElementById('toggle-icon');

if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('minimized');
        const isMinimized = sidebar.classList.contains('minimized');
        if (isMinimized) {
            mainContent.style.marginLeft = '80px';
            topAppBar.style.left = '80px';
            topAppBar.style.width = 'calc(100% - 80px)';
            toggleIcon.textContent = 'menu_open';
        } else {
            mainContent.style.marginLeft = '280px';
            topAppBar.style.left = '280px';
            topAppBar.style.width = 'calc(100% - 280px)';
            toggleIcon.textContent = 'chevron_left';
        }
    });
}

// ==========================================
// CHART INSTANCES & CONFIG
// ==========================================
let chartSuhu, chartKelembapan, chartGetaran, chartKemiringan;
let currentRange = '12h';

const commonOptions = {
    chart: {
        type: 'area',
        height: 250,
        toolbar: { show: false },
        background: 'transparent',
        fontFamily: 'Inter, sans-serif',
        animations: { enabled: true, easing: 'easeinout', speed: 600 }
    },
    theme: { mode: 'dark' },
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    xaxis: {
        type: 'datetime',
        categories: [],
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: { 
            style: { colors: '#8c909f' },
            datetimeUTC: false,
            format: 'HH:mm'
        }
    },
    yaxis: {
        labels: { style: { colors: '#8c909f' } }
    },
    grid: {
        borderColor: 'rgba(255,255,255,0.05)',
        strokeDashArray: 4,
        yaxis: { lines: { show: true } },
        xaxis: { lines: { show: false } }
    },
    tooltip: { 
        theme: 'dark',
        x: { format: 'HH:mm' }
    }
};

function createChart(selector, name, color) {
    const opts = {
        ...commonOptions,
        chart: { ...commonOptions.chart },
        colors: [color],
        series: [{ name: name, data: [] }],
        xaxis: { ...commonOptions.xaxis },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.0, stops: [0, 100] } }
    };
    const chart = new ApexCharts(document.querySelector(selector), opts);
    chart.render();
    return chart;
}

// ==========================================
// FETCH LATEST DATA (setiap 5 detik)
// ==========================================
async function fetchLatestData() {
    try {
        const res = await fetch('api/dashboard.php?action=latest');
        const json = await res.json();
        if (!json.success) return;

        const d = json.data;

        // Update sensor values
        const valSuhu = document.getElementById('val-suhu');
        const valKelembapan = document.getElementById('val-kelembapan');
        const valGetaran = document.getElementById('val-getaran');
        const valKemiringan = document.getElementById('val-kemiringan');

        if (valSuhu) valSuhu.textContent = d.suhu;
        if (valKelembapan) valKelembapan.textContent = d.kelembapan;
        if (valGetaran) valGetaran.textContent = d.getaran;
        if (valKemiringan) valKemiringan.textContent = d.kemiringan;

        // Update status hero
        const statusText = document.getElementById('status-text');
        const statusPenyebab = document.getElementById('status-penyebab');
        const statusIcon = document.getElementById('status-icon');
        const statusIconContainer = document.getElementById('status-icon-container');
        const statusGradient = document.getElementById('status-gradient');
        const statusHero = document.getElementById('status-hero');

        let color, icon, gradientColor, glowClass;
        switch (d.status) {
            case 'BAHAYA':
                color = '#ef4444'; icon = 'error'; gradientColor = 'rgba(239,68,68,0.05)'; glowClass = 'status-bahaya-glow'; break;
            case 'WASPADA':
                color = '#fbbf24'; icon = 'warning'; gradientColor = 'rgba(251,191,36,0.05)'; glowClass = 'status-waspada-glow'; break;
            default:
                color = 'rgb(74, 222, 128)'; icon = 'check_circle'; gradientColor = 'rgba(34,197,94,0.05)'; glowClass = 'status-aman-glow'; break;
        }

        if (statusText) {
            statusText.textContent = d.status;
            statusText.style.color = color;
            statusText.style.textShadow = `${color} 0px 0px 20px`;
        }
        if (statusPenyebab) {
            statusPenyebab.textContent = 'Penyebab: ' + (d.penyebab === '-' ? 'Normal' : d.penyebab);
        }
        if (statusIcon) {
            statusIcon.textContent = icon;
            statusIcon.style.color = color;
        }
        if (statusIconContainer) {
            statusIconContainer.style.borderColor = color;
            statusIconContainer.style.backgroundColor = color + '1a';
        }
        if (statusGradient) {
            statusGradient.style.background = `linear-gradient(to right, ${gradientColor}, transparent)`;
        }
        if (statusHero) {
            statusHero.classList.remove('status-aman-glow', 'status-waspada-glow', 'status-bahaya-glow');
            statusHero.classList.add(glowClass);
        }
    } catch (e) {
        console.error('Error fetching latest data:', e);
    }
}

// ==========================================
// FETCH CHART DATA
// ==========================================
async function fetchChartData(range) {
    try {
        const res = await fetch(`api/dashboard.php?action=chart&range=${range}`);
        const json = await res.json();
        if (!json.success) return;

        const maxLabels = 30;
        let labels = json.labels;
        let suhu = json.suhu;
        let kelembapan = json.kelembapan;
        let getaran = json.getaran;
        let kemiringan = json.kemiringan;



        if (chartSuhu) {
            chartSuhu.updateOptions({ xaxis: { categories: labels } });
            chartSuhu.updateSeries([{ name: 'Suhu', data: suhu }]);
        }
        if (chartKelembapan) {
            chartKelembapan.updateOptions({ xaxis: { categories: labels } });
            chartKelembapan.updateSeries([{ name: 'Kelembapan', data: kelembapan }]);
        }
        if (chartGetaran) {
            chartGetaran.updateOptions({ xaxis: { categories: labels } });
            chartGetaran.updateSeries([{ name: 'Getaran', data: getaran }]);
        }
        if (chartKemiringan) {
            chartKemiringan.updateOptions({ xaxis: { categories: labels } });
            chartKemiringan.updateSeries([{ name: 'Kemiringan', data: kemiringan }]);
        }
    } catch (e) {
        console.error('Error fetching chart data:', e);
    }
}

// ==========================================
// FILTER BUTTONS
// ==========================================
function changeRange(range, btn) {
    currentRange = range;
    // Update button styles
    document.querySelectorAll('#filter-bar button').forEach(b => {
        b.className = 'px-lg py-sm rounded-full font-label-md text-label-md bg-white/5 border border-white/10 text-on-surface-variant hover:text-on-surface transition-colors';
    });
    btn.className = 'px-lg py-sm rounded-full font-label-md text-label-md bg-primary text-on-primary font-bold shadow-[0_0_15px_rgba(173,198,255,0.3)] transition-colors';
    fetchChartData(range);
}

// ==========================================
// INIT
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
    // Create charts
    if (document.querySelector("#chart-suhu")) {
        chartSuhu = createChart("#chart-suhu", "Suhu", "#ffb4ab");
    }
    if (document.querySelector("#chart-kelembapan")) {
        chartKelembapan = createChart("#chart-kelembapan", "Kelembapan", "#adc6ff");
    }
    if (document.querySelector("#chart-getaran")) {
        chartGetaran = createChart("#chart-getaran", "Getaran", "#ffb786");
    }
    if (document.querySelector("#chart-kemiringan")) {
        chartKemiringan = createChart("#chart-kemiringan", "Kemiringan", "#c084fc");
    }

    // Initial load
    fetchLatestData();
    fetchChartData(currentRange);

    // Auto-refresh: sensor cards every 5s, charts every 60s
    setInterval(fetchLatestData, 5000);
    setInterval(() => fetchChartData(currentRange), 60000);
});

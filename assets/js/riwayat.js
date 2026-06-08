// ==========================================
// CLOCK & SIDEBAR
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
// STATUS BADGE HELPER
// ==========================================
function statusBadge(status) {
    if (status === 'AMAN') {
        return `<div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-primary/10 border border-primary/20 text-primary font-label-md text-[11px]" style="box-shadow: 0 0 10px rgba(173,198,255,0.2);"><div class="w-1.5 h-1.5 rounded-full bg-primary" style="box-shadow: 0 0 8px currentColor;"></div>AMAN</div>`;
    } else if (status === 'WASPADA') {
        return `<div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-tertiary/10 border border-tertiary/20 text-tertiary font-label-md text-[11px]"><div class="w-1.5 h-1.5 rounded-full bg-tertiary animate-pulse" style="box-shadow: 0 0 8px currentColor;"></div>WASPADA</div>`;
    } else {
        return `<div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-error/10 border border-error/20 text-error font-label-md text-[11px]" style="box-shadow: 0 0 10px rgba(255,180,171,0.2);"><div class="w-1.5 h-1.5 rounded-full bg-error animate-pulse" style="box-shadow: 0 0 8px currentColor;"></div>BAHAYA</div>`;
    }
}

// ==========================================
// LOAD HISTORY
// ==========================================
let currentPage = 1;

async function loadHistory(page) {
    currentPage = page;
    const tbody = document.getElementById('history-tbody');
    const info = document.getElementById('pagination-info');
    const pagination = document.getElementById('pagination-container');

    try {
        const res = await fetch(`api/riwayat.php?page=${page}&limit=15`);
        const json = await res.json();
        if (!json.success) return;

        // Render rows
        let html = '';
        json.data.forEach((row, index) => {
            const no = (json.page - 1) * json.limit + index + 1;
            const bgClass = index % 2 === 1 ? 'bg-white/[0.02]' : '';
            const penyebabText = row.penyebab === '-' ? 'Normal' : row.penyebab;
            const penyebabClass = row.penyebab === '-' ? 'text-on-surface-variant' : 'text-error';

            html += `
            <tr class="hover:bg-white/5 transition-colors group ${bgClass}">
                <td class="py-4 px-6 text-on-surface-variant group-hover:text-on-surface">${no}</td>
                <td class="py-4 px-6">${row.waktu}</td>
                <td class="py-4 px-6">${row.suhu}</td>
                <td class="py-4 px-6">${row.kelembapan}</td>
                <td class="py-4 px-6">${row.getaran}</td>
                <td class="py-4 px-6">${row.kemiringan}</td>
                <td class="py-4 px-6">${statusBadge(row.status)}</td>
                <td class="py-4 px-6 ${penyebabClass}">${penyebabText}</td>
            </tr>`;
        });
        tbody.innerHTML = html;

        // Pagination info
        const start = (json.page - 1) * json.limit + 1;
        const end = Math.min(json.page * json.limit, json.total);
        info.textContent = `Showing ${start}-${end} of ${json.total}`;

        // Pagination buttons
        let pagHtml = '';
        // Prev
        pagHtml += `<button class="p-1.5 rounded-md border border-white/10 text-on-surface-variant hover:text-on-surface hover:bg-white/5 ${json.page <= 1 ? 'disabled:opacity-50' : ''} transition-colors" ${json.page <= 1 ? 'disabled' : ''} onclick="loadHistory(${json.page - 1})"><span class="material-symbols-outlined text-[20px]">chevron_left</span></button>`;

        // Page numbers
        const maxPages = Math.min(json.totalPages, 7);
        let startPage = Math.max(1, json.page - 3);
        let endPage = Math.min(json.totalPages, startPage + maxPages - 1);
        if (endPage - startPage < maxPages - 1) startPage = Math.max(1, endPage - maxPages + 1);

        for (let i = startPage; i <= endPage; i++) {
            if (i === json.page) {
                pagHtml += `<button class="w-8 h-8 rounded-md bg-primary/20 text-primary border border-primary/30 font-label-md flex items-center justify-center">${i}</button>`;
            } else {
                pagHtml += `<button class="w-8 h-8 rounded-md border border-white/10 text-on-surface-variant hover:bg-white/5 font-label-md flex items-center justify-center transition-colors" onclick="loadHistory(${i})">${i}</button>`;
            }
        }
        if (endPage < json.totalPages) {
            pagHtml += `<span class="px-2 text-on-surface-variant">...</span>`;
            pagHtml += `<button class="w-8 h-8 rounded-md border border-white/10 text-on-surface-variant hover:bg-white/5 font-label-md flex items-center justify-center transition-colors" onclick="loadHistory(${json.totalPages})">${json.totalPages}</button>`;
        }

        // Next
        pagHtml += `<button class="p-1.5 rounded-md border border-white/10 text-on-surface-variant hover:text-on-surface hover:bg-white/5 transition-colors" ${json.page >= json.totalPages ? 'disabled style="opacity:0.5"' : ''} onclick="loadHistory(${json.page + 1})"><span class="material-symbols-outlined text-[20px]">chevron_right</span></button>`;

        pagination.innerHTML = pagHtml;

    } catch (e) {
        console.error('Error loading history:', e);
        tbody.innerHTML = '<tr><td colspan="8" class="py-8 text-center text-on-surface-variant">Gagal memuat data</td></tr>';
    }
}

// ==========================================
// EXPORT
// ==========================================
document.addEventListener('DOMContentLoaded', () => {
    loadHistory(1);

    const btnExport = document.getElementById('btn-export');
    if (btnExport) {
        btnExport.addEventListener('click', () => {
            window.location.href = 'api/export.php';
        });
    }
});

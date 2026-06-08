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
// SETTINGS
// ==========================================
async function loadSettings() {
    try {
        const res = await fetch('api/notifikasi.php?action=settings');
        const json = await res.json();
        if (json.success) {
            if (json.token_fonnte) document.getElementById('tokenFonnte').value = json.token_fonnte;
            if (json.nomor_wa) document.getElementById('nomorWA').value = json.nomor_wa;
        }
    } catch (e) {
        console.error('Error loading settings:', e);
    }
}

const btnSaveSettings = document.getElementById('btn-save-settings');
if (btnSaveSettings) {
    btnSaveSettings.addEventListener('click', async () => {
        const tokenFonnte = document.getElementById('tokenFonnte').value;
        const nomorWA = document.getElementById('nomorWA').value;
        if (!tokenFonnte || !nomorWA) {
            alert('Harap isi token dan nomor WA');
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('token_fonnte', tokenFonnte);
            formData.append('nomor_wa', nomorWA);
            
            const res = await fetch('api/notifikasi.php?action=save_settings', {
                method: 'POST',
                body: formData
            });
            const json = await res.json();
            if (json.success) {
                alert('Pengaturan berhasil disimpan');
            } else {
                alert('Gagal menyimpan: ' + json.message);
            }
        } catch (e) {
            console.error('Error saving settings:', e);
            alert('Terjadi kesalahan saat menyimpan pengaturan');
        }
    });
}

// ==========================================
// NOTIFICATION HISTORY
// ==========================================
let currentPage = 1;
async function loadHistory(page = 1) {
    currentPage = page;
    const tbody = document.getElementById('notifikasi-tbody');
    if (!tbody) return;
    
    try {
        const res = await fetch(`api/notifikasi.php?action=list&page=${page}&limit=10`);
        const json = await res.json();
        if (!json.success) return;

        let html = '';
        json.data.forEach(row => {
            const isSuccess = row.status_kirim === 'BERHASIL';
            let statusHtml = '';
            if (isSuccess) {
                statusHtml = `
                <div class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#aeb9d0]/10 border border-[#aeb9d0]/20 text-primary">
                    <span class="material-symbols-outlined text-[14px]" data-icon="check">check</span>
                </div>`;
            } else {
                statusHtml = `
                <div class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-error/10 border border-error/20 text-error" style="box-shadow: 0 0 8px rgba(255, 180, 171, 0.3);">
                    <span class="material-symbols-outlined text-[14px]" data-icon="close">close</span>
                </div>`;
            }

            html += `
            <tr class="border-b border-white/5 hover:bg-white/5 transition-colors group">
                <td class="py-md px-md text-on-surface-variant whitespace-nowrap">${row.waktu}</td>
                <td class="py-md px-md text-on-surface max-w-[300px] truncate">${row.pesan}</td>
                <td class="py-md px-md text-center">${statusHtml}</td>
                <td class="py-md px-md text-right">
                    <button onclick="deleteNotif(${row.id})" class="text-outline hover:text-error transition-colors p-xs rounded-md hover:bg-white/5 opacity-100">
                        <span class="material-symbols-outlined text-[18px]" data-icon="delete">delete</span>
                    </button>
                </td>
            </tr>`;
        });
        
        if (json.data.length === 0) {
            html = '<tr><td colspan="4" class="py-8 text-center text-on-surface-variant">Tidak ada riwayat notifikasi</td></tr>';
        } else if (page === 1) {
            // Update System Status UI based on latest entry
            const latestStatus = json.data[0].status_kirim;
            const textEl = document.getElementById('system-status-text');
            const indEl = document.getElementById('system-status-indicator');
            const cardEl = document.getElementById('system-status-card');
            
            if (textEl && indEl && cardEl) {
                if (latestStatus === 'BERHASIL') {
                    textEl.textContent = 'Gateway Terhubung';
                    textEl.className = 'font-body-md text-body-md text-primary mt-1';
                    indEl.innerHTML = `
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-primary" style="box-shadow: 0 0 8px currentColor;"></span>
                    `;
                    cardEl.className = 'glass-panel rounded-xl p-md flex items-center justify-between border-l-4 border-l-primary/50';
                } else {
                    textEl.textContent = 'Gateway Tidak Terhubung';
                    textEl.className = 'font-body-md text-body-md text-error mt-1';
                    indEl.innerHTML = `
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-error" style="box-shadow: 0 0 8px currentColor;"></span>
                    `;
                    cardEl.className = 'glass-panel rounded-xl p-md flex items-center justify-between border-l-4 border-l-error/50';
                }
            }
        }
        
        tbody.innerHTML = html;
        
        // Update pagination info
        const info = document.getElementById('notif-pagination-info');
        if (info) {
            const start = (json.page - 1) * json.limit + 1;
            const end = Math.min(json.page * json.limit, json.total);
            info.textContent = json.total > 0 ? `Menampilkan ${start}-${end} dari ${json.total} pesan` : 'Menampilkan 0 dari 0 pesan';
        }
        
        // Update pagination buttons
        const pagination = document.getElementById('notif-pagination-container');
        if (pagination) {
            let pagHtml = '';
            
            pagHtml += `<button class="p-xs rounded hover:bg-white/5 ${json.page <= 1 ? 'disabled:opacity-50' : ''}" ${json.page <= 1 ? 'disabled' : ''} onclick="loadHistory(${json.page - 1})">
                <span class="material-symbols-outlined text-[18px]" data-icon="chevron_left">chevron_left</span>
            </button>`;
            
            pagHtml += `<button class="p-xs rounded hover:bg-white/5 ${json.page >= json.totalPages ? 'disabled:opacity-50' : ''}" ${json.page >= json.totalPages ? 'disabled' : ''} onclick="loadHistory(${json.page + 1})">
                <span class="material-symbols-outlined text-[18px]" data-icon="chevron_right">chevron_right</span>
            </button>`;
            
            pagination.innerHTML = pagHtml;
        }

    } catch (e) {
        console.error('Error loading history:', e);
        tbody.innerHTML = '<tr><td colspan="4" class="py-8 text-center text-on-surface-variant">Gagal memuat data</td></tr>';
    }
}

window.deleteNotif = async function(id) {
    if (!confirm('Apakah anda yakin ingin menghapus notifikasi ini?')) return;
    
    try {
        const formData = new FormData();
        formData.append('id', id);
        
        const res = await fetch('api/notifikasi.php?action=delete', {
            method: 'POST',
            body: formData
        });
        const json = await res.json();
        if (json.success) {
            loadHistory(currentPage);
        } else {
            alert('Gagal menghapus: ' + json.message);
        }
    } catch (e) {
        console.error('Error deleting notif:', e);
    }
}

const btnDeleteAll = document.getElementById('btn-delete-all');
if (btnDeleteAll) {
    btnDeleteAll.addEventListener('click', async () => {
        if (!confirm('HAPUS SEMUA: Apakah anda yakin ingin menghapus seluruh riwayat notifikasi?')) return;
        
        try {
            const res = await fetch('api/notifikasi.php?action=delete_all', {
                method: 'POST'
            });
            const json = await res.json();
            if (json.success) {
                alert('Semua notifikasi dihapus');
                loadHistory(1);
            } else {
                alert('Gagal menghapus: ' + json.message);
            }
        } catch (e) {
            console.error('Error deleting all:', e);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    loadSettings();
    loadHistory(1);
});

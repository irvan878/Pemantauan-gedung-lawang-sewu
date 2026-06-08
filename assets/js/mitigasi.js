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
// LOAD MITIGASI
// ==========================================
async function loadMitigasi() {
    const tbody = document.getElementById('mitigasi-tbody');
    if (!tbody) return;
    
    try {
        const res = await fetch('api/mitigasi.php');
        const json = await res.json();
        if (!json.success) return;

        let html = '';
        json.data.forEach(row => {
            const statusColor = row.status.toLowerCase() === 'bahaya' ? 'error' : 'tertiary';
            let statusHtml = '';
            if (row.status.toLowerCase() === 'bahaya') {
                statusHtml = `<span class="inline-flex items-center gap-xs px-2 py-1 rounded-full bg-error-container/20 text-error border border-error-container/30 font-label-md text-label-md">
                    <span class="w-2 h-2 rounded-full bg-error animate-pulse" style="box-shadow: 0 0 12px currentColor;"></span>
                    Bahaya
                </span>`;
            } else {
                statusHtml = `<span class="inline-flex items-center gap-xs px-2 py-1 rounded-full bg-tertiary-container/20 text-tertiary-fixed-dim border border-tertiary-container/30 font-label-md text-label-md">
                    <span class="w-2 h-2 rounded-full bg-tertiary-fixed-dim" style="box-shadow: 0 0 8px currentColor;"></span>
                    Waspada
                </span>`;
            }
            
            const editDataObj = {
                id: row.id,
                category: row.kategori.replace(/\b\w/g, l => l.toUpperCase()),
                status: row.status.replace(/\b\w/g, l => l.toUpperCase()),
                statusColor: statusColor,
                impact: row.dampak,
                advice: row.saran,
                source: row.sumber
            };
            const editDataJson = JSON.stringify(editDataObj).replace(/"/g, '&quot;');

            html += `
            <tr class="hover:bg-white/[0.03] transition-colors group">
                <td class="py-md px-lg font-body-md text-body-md text-on-surface whitespace-nowrap">${editDataObj.category}</td>
                <td class="py-md px-lg">${statusHtml}</td>
                <td class="py-md px-lg font-body-md text-body-md text-on-surface-variant">
                    <div class="text-clamp-2">${row.dampak}</div>
                </td>
                <td class="py-md px-lg font-body-md text-body-md text-on-surface-variant">
                    <div class="text-clamp-2">${row.saran}</div>
                </td>
                <td class="py-md px-lg text-right">
                    <button @click="isEditModalOpen = true; editData = ${editDataJson}" class="p-2 rounded-lg text-on-surface-variant hover:text-primary hover:bg-white/10 transition-colors opacity-100 group-hover:opacity-100 focus:opacity-100">
                        <span class="material-symbols-outlined text-[20px]">edit</span>
                    </button>
                </td>
            </tr>`;
        });
        tbody.innerHTML = html;
    } catch (e) {
        console.error('Error loading mitigasi:', e);
        tbody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-on-surface-variant">Gagal memuat data</td></tr>';
    }
}

// ==========================================
// SAVE MITIGASI
// ==========================================
window.saveMitigasi = async function(editData) {
    if (!editData || !editData.id) return;
    try {
        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('id', editData.id);
        formData.append('dampak', editData.impact);
        formData.append('saran', editData.advice);
        formData.append('sumber', editData.source);

        const res = await fetch('api/mitigasi.php', {
            method: 'POST',
            body: formData
        });
        const json = await res.json();
        
        if (json.success) {
            alert('Data mitigasi berhasil diupdate');
            loadMitigasi();
            
            // Close AlpineJS modal natively if possible
            const alpineEl = document.querySelector('[x-data]');
            if (alpineEl && alpineEl.__x && alpineEl.__x.$data) {
                alpineEl.__x.$data.isEditModalOpen = false;
            }
        } else {
            alert('Gagal update: ' + json.message);
        }
    } catch (e) {
        console.error('Error updating mitigasi:', e);
        alert('Terjadi kesalahan saat mengupdate data.');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadMitigasi();
});

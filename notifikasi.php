<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <?php include 'components/head.php'; ?>
</head>
<body class="font-body-lg text-on-surface bg-surface min-h-screen selection:bg-primary/30 overflow-x-hidden">
    <!-- SideNavBar -->
    <?php include 'components/sidebar.php'; ?>
    
    <!-- TopAppBar -->
    <?php include 'components/header.php'; ?>

    <!-- Main Content Area -->
    <main class="ml-[280px] p-gutter min-h-screen pt-36 relative overflow-hidden" id="main-content">
        <!-- Ambient Background Effects -->
        <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] bg-primary/5 rounded-full blur-[120px] pointer-events-none z-[-1]"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[40%] h-[40%] bg-secondary-container/10 rounded-full blur-[100px] pointer-events-none z-[-1]"></div>
        
        <div class="max-w-[1200px] mx-auto space-y-gutter">
            <!-- Bento Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
                <!-- WhatsApp Settings Card (Left Column) -->
                <div class="lg:col-span-4 flex flex-col gap-gutter">
                    <div class="glass-panel rounded-xl p-lg flex flex-col h-full shadow-lg relative overflow-hidden group">
                        <!-- Decorative accent -->
                        <div class="absolute top-0 left-0 w-1 h-full bg-primary/50 group-hover:bg-primary transition-colors duration-300"></div>
                        <div class="flex items-center gap-sm mb-lg pb-sm border-b border-white/5">
                            <div class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center border border-white/10">
                                <span class="material-symbols-outlined text-primary text-[18px]" data-icon="chat">chat</span>
                            </div>
                            <h3 class="font-title-lg text-title-lg text-on-surface">WhatsApp Settings</h3>
                        </div>
                        <form class="flex flex-col gap-md flex-1">
                            <div class="flex flex-col gap-xs">
                                <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="tokenFonnte">Token Fonnte</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]" data-icon="key">key</span>
                                    <input class="w-full bg-surface-container-high border border-white/10 rounded-lg py-sm pl-10 pr-sm text-on-surface focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors font-body-md text-body-md placeholder:text-outline-variant" id="tokenFonnte" placeholder="Masukkan Token" type="password" value="••••••••••••••••">
                                </div>
                                <p class="text-[11px] text-outline mt-1">API Token dari layanan gateway Fonnte.</p>
                            </div>
                            <div class="flex flex-col gap-xs">
                                <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="nomorWA">Nomor WA Tujuan</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]" data-icon="phone_iphone">phone_iphone</span>
                                    <input class="w-full bg-surface-container-high border border-white/10 rounded-lg py-sm pl-10 pr-sm text-on-surface focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors font-body-md text-body-md placeholder:text-outline-variant" id="nomorWA" placeholder="Contoh: 0812..." type="text" value="081234567890">
                                </div>
                            </div>
                            <div class="mt-auto pt-md">
                                <button id="btn-save-settings" class="w-full bg-primary text-on-primary font-label-md text-label-md py-md rounded-lg hover:bg-primary-container transition-all duration-300 glow-primary flex items-center justify-center gap-xs font-semibold" type="button">
                                    <span class="material-symbols-outlined text-[18px]" data-icon="save">save</span>
                                    Simpan Pengaturan
                                </button>
                            </div>
                        </form>
                    </div>
                    <!-- Mini Stats Card -->
                    <div id="system-status-card" class="glass-panel rounded-xl p-md flex items-center justify-between border-l-4 border-l-primary/50">
                        <div>
                            <p class="font-label-md text-label-md text-on-surface-variant uppercase">Sistem Status</p>
                            <p id="system-status-text" class="font-body-md text-body-md text-primary mt-1">Gateway Terhubung</p>
                        </div>
                        <span id="system-status-indicator" class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-primary" style="box-shadow: 0 0 8px currentColor;"></span>
                        </span>
                    </div>
                </div>

                <!-- Notification History Card (Right Column) -->
                <div class="lg:col-span-8 flex flex-col">
                    <div class="glass-panel rounded-xl flex flex-col h-full shadow-lg relative overflow-hidden">
                        <!-- Header -->
                        <div class="p-lg flex justify-between items-center border-b border-white/5 bg-surface-container-low/30">
                            <div class="flex items-center gap-sm">
                                <div class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center border border-white/10">
                                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]" data-icon="history_edu">history_edu</span>
                                </div>
                                <h3 class="font-title-lg text-title-lg text-on-surface">Riwayat Notifikasi</h3>
                            </div>
                            <button id="btn-delete-all" class="bg-error/10 text-error border border-error/20 font-label-md text-label-md py-sm px-md rounded-lg hover:bg-error/20 transition-all duration-300 glow-error flex items-center gap-xs font-semibold">
                                <span class="material-symbols-outlined text-[16px]" data-icon="delete_sweep">delete_sweep</span>
                                Hapus Semua
                            </button>
                        </div>
                        <!-- Table Container -->
                        <div class="flex-1 overflow-x-auto p-sm">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-white/10">
                                        <th class="py-md px-md font-label-md text-label-md text-on-surface-variant uppercase tracking-wider w-[150px]">Waktu</th>
                                        <th class="py-md px-md font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Isi Pesan</th>
                                        <th class="py-md px-md font-label-md text-label-md text-on-surface-variant uppercase tracking-wider text-center w-[100px]">Status</th>
                                        <th class="py-md px-md font-label-md text-label-md text-on-surface-variant uppercase tracking-wider text-right w-[80px]">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="notifikasi-tbody" class="font-body-md text-body-md">
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination/Footer -->
                        <div class="p-sm border-t border-white/5 flex items-center justify-between text-on-surface-variant font-label-md text-label-md">
                            <span id="notif-pagination-info">Menampilkan 0 dari 0 pesan</span>
                            <div class="flex gap-sm" id="notif-pagination-container">
                                <button class="p-xs rounded hover:bg-white/5 disabled:opacity-50" disabled="">
                                    <span class="material-symbols-outlined text-[18px]" data-icon="chevron_left">chevron_left</span>
                                </button>
                                <button class="p-xs rounded hover:bg-white/5">
                                    <span class="material-symbols-outlined text-[18px]" data-icon="chevron_right">chevron_right</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="assets/js/notifikasi.js"></script>
</body>
</html>

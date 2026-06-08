<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <?php include 'components/head.php'; ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-surface text-on-surface min-h-screen flex font-body-md overflow-x-hidden selection:bg-primary/30 selection:text-primary" x-data="{ isEditModalOpen: false, editData: {} }">
    <!-- SideNavBar -->
    <?php include 'components/sidebar.php'; ?>
    
    <!-- TopAppBar -->
    <?php include 'components/header.php'; ?>

    <!-- Main Content Area -->
    <main class="ml-[280px] p-gutter min-h-screen pt-36 w-full" id="main-content">
        <div class="max-w-[1600px] mx-auto space-y-gutter">
            <!-- Context & Actions Row -->
            <div class="space-y-sm">
                <h2 class="font-headline-md text-headline-md font-bold text-on-surface">Manajemen Data Mitigasi</h2>
                <p class="text-on-surface-variant font-body-md text-body-md max-w-2xl">
                    Kelola panduan mitigasi dan respons otomatis untuk berbagai status sensor. Panduan ini akan ditampilkan saat sistem mendeteksi anomali struktural atau lingkungan.
                </p>
            </div>
            <!-- Mitigation Table Card -->
            <div class="glass-panel rounded-xl overflow-hidden flex flex-col shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-white/10 bg-white/[0.02]">
                                <th class="py-md px-lg font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Kategori</th>
                                <th class="py-md px-lg font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Status</th>
                                <th class="py-md px-lg font-label-md text-label-md text-on-surface-variant uppercase tracking-wider w-1/4">Dampak Potensial</th>
                                <th class="py-md px-lg font-label-md text-label-md text-on-surface-variant uppercase tracking-wider w-1/3">Saran Tindakan</th>
                                <th class="py-md px-lg font-label-md text-label-md text-on-surface-variant uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="mitigasi-tbody" class="divide-y divide-white/5">
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Spacer for bottom padding -->
            <div class="h-8"></div>
        </div>
    </main>

    <!-- Edit Mitigation Modal -->
    <div class="fixed inset-0 z-50 flex items-center justify-center px-4 sm:px-0" style="display: none;" x-show="isEditModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-end="opacity-100" x-transition:enter-start="opacity-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0" x-transition:leave-start="opacity-100">
        <!-- Backdrop -->
        <div @click="isEditModalOpen = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        <!-- Modal Content -->
        <div class="bg-surface-container border border-white/10 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] relative z-10 flex flex-col" x-transition:enter="transition ease-out duration-300" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-lg border-b border-white/10">
                <h3 class="font-title-lg text-title-lg text-on-surface">Edit Data Mitigasi</h3>
                <button @click="isEditModalOpen = false" class="text-on-surface-variant hover:text-on-surface transition-colors p-1 rounded-md hover:bg-white/5">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <!-- Modal Body -->
            <div class="p-lg space-y-lg overflow-y-auto">
                <!-- Readonly Info Row -->
                <div class="flex flex-col sm:flex-row gap-lg">
                    <div class="flex-1 space-y-2">
                        <label class="font-label-md text-label-md text-on-surface-variant uppercase">Kategori Sensor</label>
                        <div class="bg-surface-container-highest px-4 py-3 rounded-lg border border-white/5 text-on-surface font-body-md opacity-70 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-on-surface-variant">sensors</span>
                            <span x-text="editData.category"></span>
                        </div>
                    </div>
                    <div class="flex-1 space-y-2">
                        <label class="font-label-md text-label-md text-on-surface-variant uppercase">Status Kondisi</label>
                        <div class="bg-surface-container-highest px-4 py-3 rounded-lg border border-white/5 font-body-md opacity-70 flex items-center">
                            <span :class="{'text-error': editData.statusColor === 'error', 'text-tertiary-fixed-dim': editData.statusColor === 'tertiary'}" class="font-medium" x-text="editData.status"></span>
                        </div>
                    </div>
                </div>
                <!-- Editable Fields -->
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface-variant uppercase block" for="impactInput">Dampak Potensial</label>
                        <textarea class="w-full bg-surface-container-low border border-white/10 rounded-lg px-4 py-3 text-on-surface font-body-md placeholder-outline-variant focus:outline-none focus:border-primary transition-colors resize-none" id="impactInput" rows="3" x-model="editData.impact"></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface-variant uppercase block" for="adviceInput">Saran Tindakan</label>
                        <textarea class="w-full bg-surface-container-low border border-white/10 rounded-lg px-4 py-3 text-on-surface font-body-md placeholder-outline-variant focus:outline-none focus:border-primary transition-colors resize-none" id="adviceInput" rows="4" x-model="editData.advice"></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface-variant uppercase block" for="sourceInput">Sumber</label>
                        <textarea class="w-full bg-surface-container-low border border-white/10 rounded-lg px-4 py-3 text-on-surface font-body-md placeholder-outline-variant focus:outline-none focus:border-primary transition-colors resize-none" id="sourceInput" rows="2" x-model="editData.source"></textarea>
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="p-lg border-t border-white/10 flex justify-end gap-md bg-white/[0.02] rounded-b-xl">
                <button @click="isEditModalOpen = false" class="px-6 py-2 rounded-lg font-body-md text-body-md font-medium text-on-surface-variant hover:text-on-surface hover:bg-white/5 transition-colors border border-transparent">
                    Batal
                </button>
                <button @click="saveMitigasi(editData)" class="px-6 py-2 rounded-lg font-body-md text-body-md font-medium bg-primary text-on-primary hover:bg-primary/90 transition-colors shadow-[inset_0_1px_0_rgba(255,255,255,0.2)] flex items-center gap-2">
                    💾 Simpan Perubahan
                </button>
            </div>
        </div>
    </div>

    <script src="assets/js/mitigasi.js"></script>
</body>
</html>

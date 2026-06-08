<?php include 'components/session.php'; ?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <?php include 'components/head.php'; ?>
</head>
<body class="bg-surface text-on-surface min-h-screen flex font-body-md overflow-x-hidden selection:bg-primary/30 selection:text-primary">
    <!-- SideNavBar -->
    <?php include 'components/sidebar.php'; ?>
    
    <!-- TopAppBar -->
    <?php include 'components/header.php'; ?>

    <main class="ml-[280px] p-gutter min-h-screen pt-36 w-full" id="main-content">
        <div class="max-w-7xl mx-auto space-y-gutter">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-lg">
                <div>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface mb-1">Riwayat Data Sensor</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant">Riwayat data struktural dan lingkungan.</p>
                </div>
                <button class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary/20 text-primary border border-primary/30 hover:bg-primary/30 transition-all duration-300 font-label-md text-label-md shadow-[0_0_15px_rgba(173,198,255,0.1)]" id="btn-export">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Export to Excel
                </button>
            </div>
            
            <div class="rounded-xl border border-white/10 bg-white/5 backdrop-blur-xl shadow-2xl overflow-hidden flex flex-col">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-container/50 border-b border-white/10">
                                <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">No</th>
                                <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Waktu</th>
                                <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Suhu (°C)</th>
                                <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Kelembapan (%)</th>
                                <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Getaran (m/s²)</th>
                                <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Kemiringan (°)</th>
                                <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Status</th>
                                <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Penyebab</th>
                            </tr>
                        </thead>
                        <tbody class="font-body-md text-body-md text-on-surface divide-y divide-white/5" id="history-tbody">
                            <!-- Data loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4 border-t border-white/10 bg-surface-container/30 flex items-center justify-between">
                    <span class="font-body-md text-body-md text-on-surface-variant" id="pagination-info">Loading...</span>
                    <div class="flex items-center gap-1" id="pagination-container">
                        <!-- Pagination buttons loaded via JS -->
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="assets/js/riwayat.js"></script>
</body>
</html>

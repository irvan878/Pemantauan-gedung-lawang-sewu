<?php include 'components/session.php'; ?>
<?php
include 'koneksi.php';
$latestQuery = mysqli_query($koneksi, "SELECT * FROM tb_monitor ORDER BY waktu DESC LIMIT 1");
$latest = mysqli_fetch_assoc($latestQuery);
if (!$latest) {
    $latest = ['suhu'=>0, 'kelembapan'=>0, 'getaran'=>0, 'kemiringan'=>0, 'status'=>'AMAN', 'penyebab'=>'-'];
}

// Determine status colors
$statusText = $latest['status'];
$penyebabText = ($latest['penyebab'] == '-' || empty($latest['penyebab'])) ? 'Normal' : htmlspecialchars($latest['penyebab']);

switch ($statusText) {
    case 'BAHAYA':
        $statusColor = '#ef4444';
        $statusGlowClass = 'status-bahaya-glow';
        $statusIcon = 'error';
        $gradientColor = 'rgba(239,68,68,0.05)';
        break;
    case 'WASPADA':
        $statusColor = '#fbbf24';
        $statusGlowClass = 'status-waspada-glow';
        $statusIcon = 'warning';
        $gradientColor = 'rgba(251,191,36,0.05)';
        break;
    default:
        $statusColor = 'rgb(74, 222, 128)';
        $statusGlowClass = 'status-aman-glow';
        $statusIcon = 'check_circle';
        $gradientColor = 'rgba(34,197,94,0.05)';
        break;
}
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <?php include 'components/head.php'; ?>
</head>
<body class="bg-surface text-on-surface font-body-md antialiased overflow-x-hidden">
    <!-- SideNavBar -->
    <?php include 'components/sidebar.php'; ?>
    
    <!-- TopAppBar -->
    <?php include 'components/header.php'; ?>

    <!-- Main Content Canvas -->
    <main class="ml-[280px] p-gutter min-h-screen pt-36" id="main-content">
        <div class="max-w-[1600px] mx-auto space-y-gutter">
            <!-- Status Hero -->
            <section class="glass-card rounded-xl p-lg flex items-center justify-between <?= $statusGlowClass ?> relative overflow-hidden" id="status-hero">
                <div class="absolute inset-0 bg-gradient-to-r to-transparent pointer-events-none" id="status-gradient" style="background: linear-gradient(to right, <?= $gradientColor ?>, transparent);"></div>
                <div>
                    <h3 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-widest mb-sm">System Status</h3>
                    <div class="flex items-end gap-md">
                        <span class="font-display-lg text-display-lg" id="status-text" style="color: <?= $statusColor ?>; text-shadow: <?= $statusColor ?> 0px 0px 20px;"><?= htmlspecialchars($statusText) ?></span>
                        <span class="font-body-lg text-body-lg text-on-surface-variant mb-xs" id="status-penyebab">Penyebab: <?= $penyebabText ?></span>
                    </div>
                </div>
                <div class="w-16 h-16 rounded-full border-2 flex items-center justify-center bg-opacity-10" id="status-icon-container" style="border-color: <?= $statusColor ?>; background-color: <?= $statusColor ?>1a;">
                    <span class="material-symbols-outlined text-[32px]" id="status-icon" style="color: <?= $statusColor ?>;"><?= $statusIcon ?></span>
                </div>
            </section>

            <!-- Sensor Grid -->
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter">
                <!-- Suhu -->
                <div class="glass-card rounded-xl p-lg hover:bg-white/10 transition-colors">
                    <div class="flex items-center gap-md mb-md">
                        <div class="w-10 h-10 rounded-lg bg-error/10 flex items-center justify-center border border-error/20">
                            <span class="material-symbols-outlined text-error" data-icon="device_thermostat">device_thermostat</span>
                        </div>
                        <span class="font-title-lg text-title-lg text-on-surface-variant">Suhu</span>
                    </div>
                    <div class="font-headline-lg text-headline-lg text-on-surface"><span id="val-suhu"><?= $latest['suhu'] ?></span>&nbsp;&nbsp;<span class="text-headline-md text-on-surface-variant">°C</span></div>
                </div>
                <!-- Kelembapan -->
                <div class="glass-card rounded-xl p-lg hover:bg-white/10 transition-colors">
                    <div class="flex items-center gap-md mb-md">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center border border-primary/20">
                            <span class="material-symbols-outlined text-primary" data-icon="water_drop">water_drop</span>
                        </div>
                        <span class="font-title-lg text-title-lg text-on-surface-variant">Kelembapan</span>
                    </div>
                    <div class="font-headline-lg text-headline-lg text-on-surface"><span id="val-kelembapan"><?= $latest['kelembapan'] ?></span>&nbsp;<span class="text-headline-md text-on-surface-variant">%</span></div>
                </div>
                <!-- Getaran -->
                <div class="glass-card rounded-xl p-lg hover:bg-white/10 transition-colors">
                    <div class="flex items-center gap-md mb-md">
                        <div class="w-10 h-10 rounded-lg bg-tertiary/10 flex items-center justify-center border border-tertiary/20">
                            <span class="material-symbols-outlined text-tertiary" data-icon="waves">waves</span>
                        </div>
                        <span class="font-title-lg text-title-lg text-on-surface-variant">Getaran</span>
                    </div>
                    <div class="font-headline-lg text-headline-lg text-on-surface"><span id="val-getaran"><?= $latest['getaran'] ?></span>&nbsp;<span class="text-headline-md text-on-surface-variant">m/s²</span></div>
                </div>
                <!-- Kemiringan -->
                <div class="glass-card rounded-xl p-lg hover:bg-white/10 transition-colors">
                    <div class="flex items-center gap-md mb-md">
                        <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center border border-purple-500/20">
                            <span class="material-symbols-outlined text-purple-400" data-icon="straighten">straighten</span>
                        </div>
                        <span class="font-title-lg text-title-lg text-on-surface-variant">Kemiringan</span>
                    </div>
                    <div class="font-headline-lg text-headline-lg text-on-surface"><span id="val-kemiringan"><?= $latest['kemiringan'] ?></span><span class="text-headline-md text-on-surface-variant">°</span></div>
                </div>
            </section>

            <!-- Filter Bar -->
            <section class="flex gap-sm" id="filter-bar">
                <button class="px-lg py-sm rounded-full font-label-md text-label-md bg-white/5 border border-white/10 text-on-surface-variant hover:text-on-surface transition-colors" data-range="1h" onclick="changeRange('1h', this)">1 Jam</button>
                <button class="px-lg py-sm rounded-full font-label-md text-label-md bg-primary text-on-primary font-bold shadow-[0_0_15px_rgba(173,198,255,0.3)] transition-colors" data-range="12h" onclick="changeRange('12h', this)">12 Jam</button>
                <button class="px-lg py-sm rounded-full font-label-md text-label-md bg-white/5 border border-white/10 text-on-surface-variant hover:text-on-surface transition-colors" data-range="24h" onclick="changeRange('24h', this)">24 Jam</button>
            </section>

            <!-- Charts Grid -->
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-gutter pb-xl">
                <!-- Chart 1 -->
                <div class="glass-card rounded-xl p-lg">
                    <h4 class="font-title-lg text-title-lg text-on-surface mb-md">Grafik Suhu</h4>
                    <div class="w-full h-[250px]" id="chart-suhu" style="min-height: 265px;"></div>
                </div>
                <!-- Chart 2 -->
                <div class="glass-card rounded-xl p-lg">
                    <h4 class="font-title-lg text-title-lg text-on-surface mb-md">Grafik Kelembapan</h4>
                    <div class="w-full h-[250px]" id="chart-kelembapan" style="min-height: 265px;"></div>
                </div>
                <!-- Chart 3 -->
                <div class="glass-card rounded-xl p-lg">
                    <h4 class="font-title-lg text-title-lg text-on-surface mb-md">Grafik Getaran</h4>
                    <div class="w-full h-[250px]" id="chart-getaran" style="min-height: 265px;"></div>
                </div>
                <!-- Chart 4 -->
                <div class="glass-card rounded-xl p-lg">
                    <h4 class="font-title-lg text-title-lg text-on-surface mb-md">Grafik Kemiringan</h4>
                    <div class="w-full h-[250px]" id="chart-kemiringan" style="min-height: 265px;"></div>
                </div>
            </section>
        </div>
    </main>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>

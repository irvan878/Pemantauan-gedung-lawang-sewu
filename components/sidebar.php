<?php
$activePage = basename($_SERVER['PHP_SELF']);
?>
<nav class="fixed left-0 top-0 h-screen w-[280px] border-r border-white/10 backdrop-blur-xl bg-white/5 flex flex-col py-lg z-20" id="sidebar">
<!-- Header -->
<div class="px-md mb-xl flex items-center gap-md relative">
<div class="min-w-[40px] w-10 h-10 rounded-lg bg-primary/20 flex items-center justify-center border border-primary/30">
<span class="material-symbols-outlined text-primary" data-icon="museum" data-weight="fill" style="font-variation-settings: 'FILL' 1;">museum</span>
</div>
<div class="sidebar-header-text overflow-hidden whitespace-nowrap">
<h1 class="font-headline-md text-headline-md font-bold text-primary">Lawang Sewu</h1>
<p class="font-label-md text-label-md text-on-surface-variant">Monitoring System</p>
</div>
<!-- Minimize Toggle -->
<button class="absolute -right-3 top-2 bg-primary text-on-primary w-6 h-6 rounded-full flex items-center justify-center border border-white/20 shadow-lg hover:scale-110 transition-transform z-30" id="sidebar-toggle">
<span class="material-symbols-outlined text-[16px]" id="toggle-icon">chevron_left</span>
</button>
</div>
<!-- Navigation Tabs -->
<ul class="flex-1 px-sm space-y-xs overflow-y-auto">
<li class="sidebar-nav-item flex">
<a class="sidebar-nav-link flex items-center gap-md px-md py-sm rounded-r-full transition-all duration-300 w-full <?= ($activePage == 'dashboard.php') ? 'text-primary bg-primary/15 font-bold border-r-4 border-primary active-nav' : 'text-on-surface-variant hover:text-on-surface hover:bg-white/5' ?>" href="dashboard.php">
<span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
<span class="sidebar-label font-body-md text-body-md whitespace-nowrap">Dashboard</span>
</a>
</li>
<li class="sidebar-nav-item flex">
<a class="sidebar-nav-link flex items-center gap-md px-md py-sm rounded-r-full transition-all duration-300 w-full <?= ($activePage == 'riwayat.php') ? 'text-primary bg-primary/15 font-bold border-r-4 border-primary active-nav' : 'text-on-surface-variant hover:text-on-surface hover:bg-white/5' ?>" href="riwayat.php">
<span class="material-symbols-outlined" data-icon="history">history</span>
<span class="sidebar-label font-body-md text-body-md whitespace-nowrap">History</span>
</a>
</li>
<li class="sidebar-nav-item flex">
<a class="sidebar-nav-link flex items-center gap-md px-md py-sm rounded-r-full transition-all duration-300 w-full <?= ($activePage == 'mitigasi.php') ? 'text-primary bg-primary/15 font-bold border-r-4 border-primary active-nav' : 'text-on-surface-variant hover:text-on-surface hover:bg-white/5' ?>" href="mitigasi.php">
<span class="material-symbols-outlined" data-icon="security">security</span>
<span class="sidebar-label font-body-md text-body-md whitespace-nowrap">Mitigation</span>
</a>
</li>
<li class="sidebar-nav-item flex">
<a class="sidebar-nav-link flex items-center gap-md px-md py-sm rounded-r-full transition-all duration-300 w-full <?= ($activePage == 'notifikasi.php') ? 'text-primary bg-primary/15 font-bold border-r-4 border-primary active-nav' : 'text-on-surface-variant hover:text-on-surface hover:bg-white/5' ?>" href="notifikasi.php">
<span class="material-symbols-outlined" data-icon="notifications">notifications</span>
<span class="sidebar-label font-body-md text-body-md whitespace-nowrap">Notification</span>
</a>
</li>
</ul>
<!-- Footer -->
<div class="px-sm mt-auto">
<a class="flex items-center gap-md px-md py-sm rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-white/5 transition-all duration-300 sidebar-nav-link" href="#">
<span class="material-symbols-outlined" data-icon="account_circle">account_circle</span>
<span class="sidebar-footer-text font-body-md text-body-md whitespace-nowrap">Admin User</span>
</a>
<a class="flex items-center gap-md px-md py-sm rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-white/5 transition-all duration-300 mt-xs sidebar-nav-link" href="api/auth.php?action=logout">
<span class="material-symbols-outlined" data-icon="logout">logout</span>
<span class="sidebar-footer-text font-body-md text-body-md whitespace-nowrap">Logout</span>
</a>
</div>
</nav>

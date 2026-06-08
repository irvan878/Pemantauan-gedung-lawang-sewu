<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <?php include 'components/head.php'; ?>
</head>
<body class="bg-background text-on-surface h-screen w-full flex flex-col items-center justify-center relative overflow-hidden font-body-md text-body-md">
    <!-- Background Layers -->
    <div class="absolute inset-0 z-0 bg-gradient-to-br from-surface-container-lowest to-surface"></div>
    <div class="absolute inset-0 z-0 bg-pattern opacity-50"></div>
    
    <!-- Main Card Container -->
    <main class="w-full max-w-md px-md relative z-10 flex flex-col items-center">
        <!-- Glassmorphism Card -->
        <div class="w-full backdrop-blur-xl bg-white/5 border border-white/10 rounded-xl p-2xl flex flex-col gap-xl ambient-glow">
            <!-- Header Section -->
            <header class="flex flex-col items-center text-center gap-sm">
                <div class="w-16 h-16 rounded-full bg-primary-container/20 border border-primary-container/50 flex items-center justify-center mb-sm shadow-[0_0_15px_theme(colors.primary-container)]">
                    <span class="material-symbols-outlined text-[32px] text-primary" style="font-variation-settings: 'FILL' 1;">account_balance</span>
                </div>
                <h1 class="font-headline-lg text-headline-lg text-primary font-bold tracking-tight">Lawang Sewu</h1>
                <h2 class="font-title-lg text-title-lg text-on-surface-variant">Monitoring System</h2>
            </header>
            
            <!-- Error Message Placeholder (Hidden by default, slides down) -->
            <div class="hidden overflow-hidden transition-all duration-300 max-h-0 opacity-0" id="error-message">
                <div class="bg-error/10 border border-error/50 rounded-lg p-md flex items-start gap-sm">
                    <span class="material-symbols-outlined text-error text-[20px]">error</span>
                    <p class="font-body-md text-body-md text-error flex-1">Invalid credentials. Please verify your access token and try again.</p>
                </div>
            </div>
            
            <!-- Login Form -->
            <form autocomplete="off" class="flex flex-col gap-lg w-full" id="login-form">
                <!-- Username Input -->
                <div class="flex flex-col gap-base">
                    <label class="font-label-md text-label-md text-on-surface-variant" for="username">USERNAME</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-md text-on-surface-variant pointer-events-none">person</span>
                        <input class="w-full bg-surface-container-low border border-white/10 rounded-lg py-md pl-[48px] pr-md text-on-surface font-body-md text-body-md focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors placeholder:text-outline" id="username" name="username" placeholder="Username" type="text">
                    </div>
                </div>
                <!-- Password Input -->
                <div class="flex flex-col gap-base">
                    <label class="font-label-md text-label-md text-on-surface-variant" for="password">PASSWORD</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-md text-on-surface-variant pointer-events-none">lock</span>
                        <input class="w-full bg-surface-container-low border border-white/10 rounded-lg py-md pl-[48px] pr-[48px] text-on-surface font-body-md text-body-md focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors placeholder:text-outline" id="password" name="password" placeholder="Password" type="password">
                        <button class="absolute right-md text-on-surface-variant hover:text-primary transition-colors focus:outline-none flex items-center justify-center" id="toggle-password" type="button">
                            <span class="material-symbols-outlined" id="toggle-icon">visibility</span>
                        </button>
                    </div>
                </div>
                <!-- Action Button -->
                <button class="mt-sm w-full bg-primary-container text-on-primary-container font-label-md text-label-md py-md rounded-lg flex items-center justify-center gap-sm hover:scale-[1.02] hover:bg-primary transition-all duration-300 shadow-[0_0_12px_theme(colors.primary-container)/30] border border-primary/20" type="submit">LOGIN <span class="material-symbols-outlined text-[18px]">arrow_forward</span></button>
            </form>
        </div>
    </main>
    
    <!-- Footer Outside Card -->
    <footer class="absolute bottom-xl w-full text-center z-10">
        <p class="font-body-md text-body-md text-outline-variant">© 2026 Lawang Sewu</p>
    </footer>

    <script src="assets/js/login.js"></script>
</body>
</html>

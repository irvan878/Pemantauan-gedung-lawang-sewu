document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.getElementById('password');
    const toggleButton = document.getElementById('toggle-password');
    const toggleIcon = document.getElementById('toggle-icon');
    const loginForm = document.getElementById('login-form');
    const errorMessage = document.getElementById('error-message');

    // Password Visibility Toggle
    toggleButton.addEventListener('click', () => {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.textContent = 'visibility_off';
        } else {
            passwordInput.type = 'password';
            toggleIcon.textContent = 'visibility';
        }
    });

    // Show error message with animation
    function showError(message) {
        const errorText = errorMessage.querySelector('p');
        if (errorText) errorText.textContent = message;

        errorMessage.classList.remove('hidden');
        void errorMessage.offsetWidth;
        errorMessage.style.maxHeight = errorMessage.scrollHeight + "px";
        errorMessage.style.opacity = "1";
        errorMessage.classList.add('mb-sm');

        const inputs = loginForm.querySelectorAll('input');
        inputs.forEach(input => {
            input.classList.add('border-error/50');
            input.classList.remove('border-white/10');
        });

        setTimeout(() => {
            errorMessage.style.maxHeight = "0";
            errorMessage.style.opacity = "0";
            errorMessage.classList.remove('mb-sm');
            setTimeout(() => errorMessage.classList.add('hidden'), 300);

            inputs.forEach(input => {
                input.classList.remove('border-error/50');
                input.classList.add('border-white/10');
            });
        }, 3000);
    }

    // Real Login via AJAX
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitBtn = loginForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span> LOADING...';
        submitBtn.disabled = true;

        try {
            const formData = new FormData();
            formData.append('action', 'login');
            formData.append('username', document.getElementById('username').value);
            formData.append('password', document.getElementById('password').value);

            const response = await fetch('api/auth.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                submitBtn.innerHTML = '<span class="material-symbols-outlined text-[18px]">check_circle</span> BERHASIL';
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 500);
            } else {
                showError(data.message || 'Login gagal');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        } catch (error) {
            showError('Terjadi kesalahan koneksi ke server');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
});

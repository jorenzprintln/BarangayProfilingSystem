function togglePasswordLogin() {
    const field = document.getElementById('password');
    const icon = document.getElementById('password-login-icon');

    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Loading state on login submit
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[action*="action=login"]');
    const btn = document.querySelector('.btn-login');

    if (form && btn) {
        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Signing in...';
        });
    }
});
<?php
$content = ob_start();
?>

<link rel="stylesheet" href="public/assets/css/login.css?v=<?= time() ?>">

<!-- Toast (error) -->
<?php if (isset($errors['email'])): ?>
    <div class="toast-container">
        <div class="custom-toast toast-error show" data-autodismiss>
            <div class="toast-icon"><i class="fas fa-exclamation-circle"></i></div>
            <div class="toast-body-text"><?= htmlspecialchars($errors['email']) ?></div>
            <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            <div class="toast-progress"></div>
        </div>
    </div>
<?php endif; ?>

<!-- Toast (success) -->
<?php if (isset($success)): ?>
    <div class="toast-container">
        <div class="custom-toast toast-success show">
            <div class="toast-icon"><i class="fas fa-check-circle"></i></div>
            <div class="toast-body-text"><?= htmlspecialchars($success) ?></div>
            <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    </div>
<?php endif; ?>

<div class="container">
    <div class="row">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
            <div class="border-0 shadow-lg rounded-lg my-5 login-card">
                <div class="card-body p-4 p-sm-5">

                    <!-- Logo Header -->
                    <div class="logo-header">
                        <div class="d-flex justify-content-between text-center mb-3">
                            <img src="public/assets/imgs/brgy.logo.png" alt="Logo" class="img-fluid"
                                style="width: 96px;">
                            <div class="d-flex flex-column">
                                <small class="text-white">Republic of the Philippines</small>
                                <small class="text-white">City of Tacloban</small>
                                <small class="text-white font-weight-bold">Barangay 36-A</small>
                            </div>
                            <img src="public/assets/imgs/city_logo.png" alt="Logo" class="img-fluid"
                                style="width: 96px;">
                        </div>
                        <h5 class="text-center mb-0 text-white font-weight-bold">Barangay Profiling System</h5>
                    </div>

                    <!-- Page Title -->
                    <div class="text-center mb-4">
                        <?php if (isset($success)): ?>
                            <div class="mb-3">
                                <div style="width:70px;height:70px;border-radius:50%;background:#ecfdf5;border:2px solid #86efac;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                                    <i class="fas fa-envelope-open-text" style="font-size:1.8rem;color:#16a34a;"></i>
                                </div>
                            </div>
                            <h4 class="card-title-main">Check Your Email!</h4>
                            <p class="card-subtitle">We've sent a password reset link to <strong><?= htmlspecialchars($email ?? 'your email') ?></strong>.</p>
                            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:0.85rem 1rem;margin-top:1rem;font-size:0.83rem;color:#166534;text-align:left;">
                                <p class="mb-1"><i class="fas fa-info-circle mr-1"></i> <strong>Didn't receive it?</strong></p>
                                <ul class="mb-0 pl-3" style="line-height:1.7;">
                                    <li>Check your <strong>Spam</strong> or <strong>Junk</strong> folder</li>
                                    <li>Make sure you entered the correct email</li>
                                    <li>The link expires in <strong>60 minutes</strong></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <h4 class="card-title-main">Forgot Password</h4>
                            <p class="card-subtitle">Enter your email address and we'll send you a reset link.</p>
                        <?php endif; ?>
                    </div>

                    <?php if (!isset($success)): ?>
                    <!-- Forgot Password Form -->
                    <form action="index.php?controller=auth&action=sendResetLink" method="post">
                        <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">

                        <!-- Email Field -->
                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope mr-1"></i> Email Address
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email"
                                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                    id="email"
                                    name="email"
                                    value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                                    placeholder="Enter your registered email"
                                    required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $errors['email'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-login w-100">
                                <i class="fas fa-paper-plane mr-2"></i> Send Reset Link
                            </button>
                        </div>

                    </form>
                    <?php endif; ?>

                    <!-- Back to Login -->
                    <div class="register-link">
                        <p class="mb-0">
                            <a href="index.php?controller=auth&action=login">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Sign In
                            </a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
.toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
.custom-toast {
    display: flex; align-items: center; gap: 10px;
    min-width: 300px; max-width: 420px;
    padding: 14px 16px; border-radius: 10px;
    background: #fff; color: #1e293b;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    border-left: 4px solid #ef4444;
    position: relative; overflow: hidden;
    animation: toastSlideIn 0.35s ease;
}
.custom-toast.toast-success { border-left-color: #22c55e; }
.custom-toast.toast-success .toast-icon { color: #22c55e; }
.custom-toast.toast-success .toast-progress { background: #22c55e; }
.custom-toast.toast-hiding { animation: toastSlideOut 0.3s ease forwards; }
.toast-icon { color: #ef4444; font-size: 1.15rem; flex-shrink: 0; }
.toast-body-text { flex: 1; font-size: 0.875rem; font-weight: 500; }
.toast-close {
    background: none; border: none; font-size: 1.2rem; color: #94a3b8;
    cursor: pointer; padding: 0 2px; line-height: 1;
}
.toast-close:hover { color: #475569; }
.toast-progress {
    position: absolute; bottom: 0; left: 0; height: 3px;
    background: #ef4444; border-radius: 0 0 0 10px;
    animation: toastProgress 4s linear forwards;
}
@keyframes toastSlideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
@keyframes toastSlideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
@keyframes toastProgress { from { width: 100%; } to { width: 0%; } }
</style>
<script>
(function() {
    document.querySelectorAll('.custom-toast[data-autodismiss]').forEach(function(toast) {
        setTimeout(function() {
            toast.classList.add('toast-hiding');
            setTimeout(function() { toast.remove(); }, 300);
        }, 4000);
    });

    // Loading state on forgot password submit
    const form = document.querySelector('form[action*="action=sendResetLink"]');
    const btn = form ? form.querySelector('button[type="submit"]') : null;
    if (form && btn) {
        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Sending...';
        });
    }
})();
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>

<?php
$content = ob_start();
?>

<link rel="stylesheet" href="public/assets/css/login.css?v=<?= time() ?>">

<!-- Toast (token error) -->
<?php if (isset($errors['token'])): ?>
    <div class="toast-container">
        <div class="custom-toast toast-error show">
            <div class="toast-icon"><i class="fas fa-exclamation-circle"></i></div>
            <div class="toast-body-text"><?= htmlspecialchars($errors['token']) ?></div>
            <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
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
                                <div class="status-icon-wrap" style="background:#ecfdf5;border:2px solid #86efac;">
                                    <i class="fas fa-lock" style="font-size:1.8rem;color:#16a34a;"></i>
                                </div>
                            </div>
                            <h4 class="card-title-main">Password Changed!</h4>
                            <p class="card-subtitle">Your password has been successfully reset. You can now log in with your new password.</p>
                        <?php elseif (isset($errors['token'])): ?>
                            <div class="mb-3">
                                <div class="status-icon-wrap" style="background:#fef2f2;border:2px solid #fca5a5;">
                                    <i class="fas fa-link-slash" style="font-size:1.8rem;color:#dc2626;"></i>
                                </div>
                            </div>
                            <h4 class="card-title-main">Link Expired!</h4>
                            <p class="card-subtitle">This reset link is invalid or has already expired. Please request a new one.</p>
                        <?php else: ?>
                            <h4 class="card-title-main">Reset Password</h4>
                            <p class="card-subtitle">Enter your new password below.</p>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($success)): ?>
                        <!-- Success — show login link -->
                        <div class="text-center mb-3">
                            <a href="index.php?controller=auth&action=login" class="btn btn-login w-100">
                                <i class="fas fa-sign-in-alt mr-2"></i> Go to Sign In
                            </a>
                        </div>
                    <?php elseif (isset($errors['token'])): ?>
                        <!-- Invalid/expired token — show request new link -->
                        <div class="text-center mb-3">
                            <a href="index.php?controller=auth&action=forgotPassword" class="btn btn-login w-100" style="white-space:normal;line-height:1.4;padding-top:0.6rem;padding-bottom:0.6rem;">
                                <i class="fas fa-redo mr-2"></i> Request New Reset Link
                            </a>
                        </div>
                    <?php elseif (isset($token)): ?>
                        <!-- Reset Form -->
                        <form action="index.php?controller=auth&action=processReset" method="post">
                            <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                            <!-- Password error toasts -->
                            <?php if (isset($errors['password'])): ?>
                                <div class="toast-container">
                                    <div class="custom-toast toast-error show" data-autodismiss>
                                        <div class="toast-icon"><i class="fas fa-exclamation-circle"></i></div>
                                        <div class="toast-body-text"><?= htmlspecialchars($errors['password']) ?></div>
                                        <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
                                        <div class="toast-progress"></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="toast-container">
                                    <div class="custom-toast toast-error show" data-autodismiss>
                                        <div class="toast-icon"><i class="fas fa-exclamation-circle"></i></div>
                                        <div class="toast-body-text"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                                        <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
                                        <div class="toast-progress"></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- New Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock mr-1"></i> New Password
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password"
                                        class="form-control"
                                        id="password"
                                        name="password"
                                        placeholder="Enter new password"
                                        required>
                                    <span class="password-toggle" onclick="togglePw('password')">
                                        <i class="fas fa-eye" id="password-icon"></i>
                                    </span>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Must be at least 8 characters
                                </small>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock mr-1"></i> Confirm Password
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password"
                                        class="form-control"
                                        id="confirm_password"
                                        name="confirm_password"
                                        placeholder="Confirm new password"
                                        required>
                                    <span class="password-toggle" onclick="togglePw('confirm_password')">
                                        <i class="fas fa-eye" id="confirm_password-icon"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-login w-100">
                                    <i class="fas fa-key mr-2"></i> Reset Password
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>

                    <!-- Back to Login -->
                    <?php if (!isset($success)): ?>
                    <div class="register-link">
                        <p class="mb-0">
                            <a href="index.php?controller=auth&action=login">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Sign In
                            </a>
                        </p>
                    </div>
                    <?php endif; ?>

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
/* ── Responsive Reset Password ── */
.login-card {
    margin-top: 1.5rem !important;
    margin-bottom: 1.5rem !important;
}

/* Status icon responsive */
.status-icon-wrap {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

@media (max-width: 575.98px) {
    .login-card {
        margin-top: 0.75rem !important;
        margin-bottom: 0.75rem !important;
        border-radius: 16px !important;
    }

    .card-body {
        padding: 1.25rem !important;
    }

    .status-icon-wrap {
        width: 58px;
        height: 58px;
    }

    .status-icon-wrap i {
        font-size: 1.4rem !important;
    }

    .toast-container {
        top: 18px;
        bottom: auto;
        right: 12px;
        left: 12px;
    }

    .custom-toast {
        min-width: unset;
        width: 100%;
        max-width: unset;
    }
}

@media (max-width: 360px) {
    .card-body {
        padding: 1rem !important;
    }

    .logo-header {
        padding: 0.85rem !important;
    }

    .logo-header img {
        width: 70px !important;
    }
}
</style>
<script>
function togglePw(fieldId) {
    var field = document.getElementById(fieldId);
    var icon = document.getElementById(fieldId + '-icon');
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

// Loading state on reset password submit
(function() {
    document.querySelectorAll('.custom-toast[data-autodismiss]').forEach(function(toast) {
        setTimeout(function() {
            toast.classList.add('toast-hiding');
            setTimeout(function() { toast.remove(); }, 300);
        }, 4000);
    });
})();

(function () {
    const form = document.querySelector('form[action*="action=processReset"]');
    const btn = form ? form.querySelector('button[type="submit"]') : null;
    if (form && btn) {
        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Resetting...';
        });
    }
})();
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>

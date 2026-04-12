<?php
$content = ob_start();
?>

<link rel="stylesheet" href="public/assets/css/login.css?v=<?= time() ?>">

<!-- Toast (login errors) -->
<?php if (isset($errors['login']) && !str_contains($errors['login'], 'pending approval') && !isset($lockout_seconds)): ?>
    <div class="toast-container">
        <div class="custom-toast toast-error show" data-autodismiss>
            <div class="toast-icon"><i class="fas fa-exclamation-circle"></i></div>
            <div class="toast-body-text"><?= htmlspecialchars($errors['login']) ?></div>
            <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            <div class="toast-progress"></div>
        </div>
    </div>
<?php endif; ?>

<!-- Toast (lockout with timer) -->
<?php if (isset($lockout_seconds) && $lockout_seconds > 0): ?>
    <div class="toast-container">
        <div class="custom-toast toast-warning show" id="lockout-toast">
            <div class="toast-icon"><i class="fas fa-lock"></i></div>
            <div class="toast-body-text">
                Too many failed attempts. Try again in
                <span id="lockout-timer" data-seconds="<?= (int)$lockout_seconds ?>" style="font-weight:700; font-variant-numeric:tabular-nums;"></span>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Toast (pending approval) -->
<?php if (isset($errors['login']) && str_contains($errors['login'], 'pending approval') && !isset($lockout_seconds)): ?>
    <div class="toast-container">
        <div class="custom-toast toast-warning show">
            <div class="toast-icon"><i class="fas fa-clock"></i></div>
            <div class="toast-body-text"><?= htmlspecialchars($errors['login']) ?></div>
            <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    </div>
<?php endif; ?>

<!-- Toast (logout success) -->
<?php if (isset($_GET['logged_out']) && $_GET['logged_out'] == '1'): ?>
    <div class="toast-container">
        <div class="custom-toast toast-success show" data-autodismiss>
            <div class="toast-icon"><i class="fas fa-check-circle"></i></div>
            <div class="toast-body-text">You have been successfully logged out.</div>
            <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            <div class="toast-progress"></div>
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
                        <h4 class="card-title-main">Sign In</h4>
                        <p class="card-subtitle">"Serving the community, one record at a time."</p>
                    </div>

                    <!-- Login Form -->
                    <form action="index.php?controller=auth&action=login" method="post">
                        <input type="hidden" name="csrf_token" value="<?= Session::generateCsrfToken() ?>">

                        <!-- Username Field -->
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user mr-1"></i> Username
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text"
                                    class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                                    id="username"
                                    name="username"
                                    value="<?= $username ?? '' ?>"
                                    placeholder="Enter your username"
                                    required>
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $errors['username'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock mr-1"></i> Password
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password"
                                    class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                    id="password"
                                    name="password"
                                    placeholder="Enter your password"
                                    required>
                                <span class="password-toggle" onclick="togglePasswordLogin()">
                                    <i class="fas fa-eye" id="password-login-icon"></i>
                                </span>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $errors['password'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-login w-100">
                                <i class="fas fa-sign-in-alt mr-2"></i> Log In
                            </button>
                        </div>

                        <!-- [FORGOT PASSWORD DISABLED] - uncomment when forgot password is re-enabled -->
                        <?php /* FORGOT PASSWORD LINK - re-enable when reset password feature is restored
                        <div class="text-center mt-3">
                            <a href="index.php?controller=auth&action=forgotPassword" style="color:#64748b; font-size:0.85rem; text-decoration:none;">
                                <i class="fas fa-key mr-1"></i> Forgot Password?
                            </a>
                        </div>
                        */ ?>

                        <!-- Register Link -->
                        <!-- <div class="register-link">
                            <p class="mb-0">
                                Don't have an account?
                                <a href="index.php?controller=auth&action=register">
                                    Create Account <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </p>
                        </div>-->

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="public/assets/js/login.js"></script>
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
.custom-toast.toast-warning { border-left-color: #f59e0b; }
.custom-toast.toast-warning .toast-icon { color: #f59e0b; }
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
    // Auto-dismiss toasts (only those with data-autodismiss)
    document.querySelectorAll('.custom-toast[data-autodismiss]').forEach(function(toast) {
        setTimeout(function() {
            toast.classList.add('toast-hiding');
            setTimeout(function() { toast.remove(); }, 300);
        }, 4000);
    });

    // Remove logged_out param so toast doesn't reappear on refresh
    var url = new URL(window.location);
    if (url.searchParams.has('logged_out')) {
        url.searchParams.delete('logged_out');
        url.searchParams.delete('_');
        history.replaceState(null, '', url.toString());
    }

    // Lockout countdown in toast
    var el = document.getElementById('lockout-timer');
    if (!el) return;
    var seconds = parseInt(el.dataset.seconds, 10);
    var toast = document.getElementById('lockout-toast');
    function pad(n) { return n < 10 ? '0' + n : n; }
    function update() {
        if (seconds <= 0) {
            toast.className = 'custom-toast toast-success show';
            toast.innerHTML =
                '<div class="toast-icon"><i class="fas fa-check-circle"></i></div>' +
                '<div class="toast-body-text">You can try logging in again now.</div>' +
                '<button class="toast-close" onclick="this.parentElement.remove()">&times;</button>';
            setTimeout(function() {
                toast.classList.add('toast-hiding');
                setTimeout(function() { toast.remove(); }, 300);
            }, 5000);
            return;
        }
        var m = Math.floor(seconds / 60);
        var s = seconds % 60;
        el.textContent = pad(m) + ':' + pad(s);
        seconds--;
        setTimeout(update, 1000);
    }
    update();
})();
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
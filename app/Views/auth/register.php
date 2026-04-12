<?php
$content = ob_start();
?>

<link rel="stylesheet" href="public/assets/css/register.css?v=<?= time() ?>">

<!-- Toast (register errors) -->
<?php if (isset($errors['register'])): ?>
    <div class="toast-container">
        <div class="custom-toast toast-error show">
            <div class="toast-icon"><i class="fas fa-exclamation-circle"></i></div>
            <div class="toast-body-text"><?= htmlspecialchars($errors['register']) ?></div>
            <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            <div class="toast-progress"></div>
        </div>
    </div>
<?php endif; ?>

<div class="container">
    <div class="row">
        <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">
            <div class="border-0 shadow-lg rounded-lg my-5 register-card">
                <div class="card-body p-4 p-sm-5">

                    <!-- Logo Header -->
                    <div class="logo-header">
                        <div class="d-flex justify-content-between align-items-center text-center mb-3">
                            <img src="public/assets/imgs/brgy.logo.png" alt="Barangay Logo" class="img-fluid"
                                style="width: 80px;">
                            <div class="d-flex flex-column">
                                <small class="text-white">Republic of the Philippines</small>
                                <small class="text-white">City of Tacloban</small>
                                <small class="text-white font-weight-bold">Barangay 36-A</small>
                            </div>
                            <img src="public/assets/imgs/city_logo.png" alt="City Logo" class="img-fluid"
                                style="width: 80px;">
                        </div>
                        <h5 class="text-center mb-0 text-white font-weight-bold">Barangay Profiling System</h5>
                    </div>

                    <!-- Page Title -->
                    <div class="text-center mb-4">
                        <h4 class="card-title-main">Constituent Registration</h4>
                        <p class="card-subtitle">Create your account to access barangay services</p>
                    </div>

                    <!-- Success Message -->
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            <?= $success ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!isset($success)): ?>
                    <!-- Registration Form -->
                    <form action="index.php?controller=auth&action=register" method="post">
                        <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">

                        <!-- Full Name Field -->
                        <div class="mb-3">
                            <label for="fullname" class="form-label">
                                <i class="fas fa-id-card mr-1"></i> Full Name
                            </label>
                            <div class="input-group-icon">
                                <i class="fas fa-id-card"></i>
                                <input type="text"
                                    class="form-control <?= isset($errors['fullname']) ? 'is-invalid' : '' ?>"
                                    id="fullname"
                                    name="fullname"
                                    placeholder="Enter your full name"
                                    value="<?= isset($fullname) ? htmlspecialchars($fullname) : '' ?>"
                                    required>
                            </div>
                            <?php if (isset($errors['fullname'])): ?>
                                <div class="invalid-feedback d-block">
                                    <?= $errors['fullname'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- [EMAIL DISABLED] - uncomment this block when email verification is re-enabled -->
                        <?php /* EMAIL FIELD - re-enable when OTP verification is restored
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope mr-1"></i> Email Address
                            </label>
                            <div class="input-group-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email"
                                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                    id="email"
                                    name="email"
                                    placeholder="Enter your email address"
                                    value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                                    required>
                            </div>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback d-block">
                                    <?= $errors['email'] ?>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Used for password recovery
                            </small>
                        </div>
                        */ ?>

                        <!-- Username Field -->
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user mr-1"></i> Username
                            </label>
                            <div class="input-group-icon">
                                <i class="fas fa-user"></i>
                                <input type="text"
                                    class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                                    id="username"
                                    name="username"
                                    placeholder="Choose a username"
                                    value="<?= isset($username) ? htmlspecialchars($username) : '' ?>"
                                    required>
                            </div>
                            <?php if (isset($errors['username'])): ?>
                                <div class="invalid-feedback d-block">
                                    <?= $errors['username'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Password Field -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock mr-1"></i> Password
                            </label>
                            <div class="input-group-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password"
                                    class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                    id="password"
                                    name="password"
                                    placeholder="Enter your password"
                                    required>
                                <span class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye-slash" id="password-icon"></i>
                                </span>
                            </div>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback d-block">
                                    <?= $errors['password'] ?>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Must be at least 8 characters
                            </small>
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-lock mr-1"></i> Confirm Password
                            </label>
                            <div class="input-group-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password"
                                    class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                                    id="confirm_password"
                                    name="confirm_password"
                                    placeholder="Confirm your password"
                                    required>
                                <span class="password-toggle" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye-slash" id="confirm_password-icon"></i>
                                </span>
                            </div>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback d-block">
                                    <?= $errors['confirm_password'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Honeypot - hidden from real users -->
                        <div style="position:absolute;left:-9999px;" aria-hidden="true">
                            <label for="website">Leave this empty</label>
                            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off" value="">
                        </div>

                        <!-- Info Notice -->
                        <div class="alert alert-info py-2 px-3 mb-3" style="font-size: 0.85rem; border-radius: 8px;">
                            <i class="fas fa-info-circle mr-1"></i>
                            Your account will need admin approval before you can log in.
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-register btn-block">
                                <i class="fas fa-user-plus mr-2"></i> Register
                            </button>
                        </div>

                    </form>
                    <?php endif; ?>

                    <!-- Footer -->
                    <div class="register-footer">
                        <p class="mb-0">
                            Already have an account?
                            <a href="index.php?controller=auth&action=login">
                                Sign In <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="public/assets/js/register.js"></script>
<style>
.toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; }
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
    var toast = document.querySelector('.custom-toast');
    if (toast) {
        setTimeout(function() {
            toast.classList.add('toast-hiding');
            setTimeout(function() { toast.parentElement.remove(); }, 300);
        }, 4000);
    }
})();
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
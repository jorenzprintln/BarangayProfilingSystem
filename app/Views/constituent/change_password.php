<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Set Your Password</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="public/assets/imgs/brgyicon.png">
    <link rel="stylesheet" href="vendor/fontawesome-free-6.7.2-web/css/all.min.css">
    <!-- Reuse the exact same login stylesheet -->
    <link rel="stylesheet" href="public/assets/css/login.css?v=<?= time() ?>">
    <script src="vendor/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <style>
        @font-face {
            font-family: 'Montserrat';
            src: url('public/assets/fonts/Montserrat-Regular.ttf') format('truetype');
        }
        body { font-family: 'Montserrat', sans-serif; }

        /* ── Notice box ── */
        .notice-box {
            display: flex;
            gap: .6rem;
            align-items: flex-start;
            background: #fffbf0;
            border: 1.5px solid #f6d860;
            border-left: 4px solid #f0b429;
            border-radius: 12px;
            padding: .85rem 1rem;
            margin-bottom: 1.25rem;
            font-size: .82rem;
            color: #7a4f00;
            line-height: 1.55;
        }
        .notice-box i {
            color: #f0b429;
            flex-shrink: 0;
            margin-top: 2px;
            font-size: .95rem;
        }

        /* ── Field hint ── */
        .field-hint {
            font-size: .75rem;
            color: #a0aec0;
            margin-top: .3rem;
        }

        /* ── Mismatch error ── */
        .mismatch-error {
            font-size: .78rem;
            color: #c53030;
            margin-top: .3rem;
            display: none;
        }

        /* ── Logout link ── */
        .logout-link {
            text-align: center;
            margin-top: 1.1rem;
            font-size: .82rem;
        }
        .logout-link a {
            color: #718096;
            text-decoration: none;
            font-weight: 600;
            transition: color .2s;
        }
        .logout-link a:hover {
            color: #1a73e8;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php
$errors = $errors ?? [];
$user   = $user   ?? [];
?>

<div class="container">
    <div class="row">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
            <div class="border-0 shadow-lg rounded-lg my-5 login-card">
                <div class="card-body p-4 p-sm-5">

                    <!-- Logo Header — identical to login page -->
                    <div class="logo-header">
                        <div class="d-flex justify-content-between text-center mb-3">
                            <img src="public/assets/imgs/brgy.logo.png" alt="Logo" class="img-fluid"
                                style="width: 96px;">
                            <div class="d-flex flex-column justify-content-center">
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
                        <h4 class="card-title-main">Set Your Password</h4>
                        <p class="card-subtitle">"Your security is our priority."</p>
                    </div>

                    <!-- Notice box -->
                    <div class="notice-box">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            Your account is using a <strong>default password</strong> assigned by the secretary.
                            For your security, please set a personal password now to access the system.
                        </div>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="index.php?controller=constituent&action=saveChangePassword" id="changePasswordForm">
                        <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label">
                                <i class="fas fa-lock mr-1"></i> New Password
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password"
                                    class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>"
                                    id="new_password"
                                    name="new_password"
                                    placeholder="Enter new password"
                                    autocomplete="new-password"
                                    required>
                                <span class="password-toggle" onclick="togglePw('new_password', 'icon-new')">
                                    <i class="fas fa-eye-slash" id="icon-new"></i>
                                </span>
                                <?php if (isset($errors['new_password'])): ?>
                                    <div class="invalid-feedback" style="display:block;">
                                        <?= htmlspecialchars($errors['new_password']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="field-hint">Minimum 8 characters. Cannot be the same as your username.</div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-lock mr-1"></i> Confirm New Password
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password"
                                    class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                                    id="confirm_password"
                                    name="confirm_password"
                                    placeholder="Re-enter new password"
                                    autocomplete="new-password"
                                    required>
                                <span class="password-toggle" onclick="togglePw('confirm_password', 'icon-confirm')">
                                    <i class="fas fa-eye-slash" id="icon-confirm"></i>
                                </span>
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="invalid-feedback" style="display:block;">
                                        <?= htmlspecialchars($errors['confirm_password']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="mismatch-error" id="match-hint">
                                <i class="fas fa-times-circle mr-1"></i>Passwords do not match
                            </div>
                        </div>

                        <!-- Submit — same class as login button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-login w-100">
                                <i class="fas fa-shield-alt mr-2"></i> Set Password &amp; Continue
                            </button>
                        </div>

                    </form>

                    <!-- Logout link -->
                    <div class="logout-link">
                        <a href="index.php?controller=auth&action=logout">
                            <i class="fas fa-sign-out-alt mr-1"></i> Log out and come back later
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePw(fieldId, iconId) {
    var input = document.getElementById(fieldId);
    var icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye-slash';
    }
}

document.getElementById('confirm_password').addEventListener('input', function () {
    var pw   = document.getElementById('new_password').value;
    var hint = document.getElementById('match-hint');
    hint.style.display = (this.value && this.value !== pw) ? 'block' : 'none';
});

document.getElementById('changePasswordForm').addEventListener('submit', function (e) {
    var pw  = document.getElementById('new_password').value;
    var cpw = document.getElementById('confirm_password').value;
    if (pw !== cpw) {
        e.preventDefault();
        document.getElementById('match-hint').style.display = 'block';
        document.getElementById('confirm_password').focus();
    }
});
</script>

</body>
</html>
<?php
$content = ob_start();
?>

<link rel="stylesheet" href="public/assets/css/manage_users.css?v=<?= time() ?>">

<div class="container-fluid px-4 mt-3">

    <!-- Back Button -->
    <a href="index.php?controller=users" class="back-link">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        Back to Users
    </a>

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <svg fill="white" viewBox="0 0 20 20">
                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
            </svg>
            <div>
                <h3 class="font-weight-bold mb-0">Create Official Account</h3>
                <p class="mb-0 mt-1" style="opacity:0.9;font-size:0.9rem;">Fill in the details to create a new system account</p>
            </div>
        </div>
    </div>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger-modern alert-modern" role="alert">
            <strong>Error!</strong> <?= htmlspecialchars($errors['general']) ?>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <div class="form-card-header">
            <svg width="14" height="14" fill="#4361ee" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
            </svg>
            <span class="form-card-header-label">Account Details</span>
        </div>

        <form method="POST" action="index.php?controller=users&action=store" id="createUserForm">
            <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
            <div class="form-card-body">

                <!-- Username -->
                <div class="field-group">
                    <label for="username">
                        Username
                        <span class="req">*</span>
                    </label>
                    <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                        id="username" name="username"
                        value="<?= htmlspecialchars($username ?? '') ?>"
                        placeholder="Enter username" required>
                    <?php if (isset($errors['username'])): ?>
                        <div class="invalid-feedback show"><?= htmlspecialchars($errors['username']) ?></div>
                    <?php endif; ?>
                    <div class="field-hint">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;margin-top:1px">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Minimum 3 characters, used for login
                    </div>
                </div>

                <!-- Full Name -->
                <div class="field-group">
                    <label for="fullname">
                        Full Name
                        <span class="req">*</span>
                    </label>
                    <input type="text" class="form-control <?= isset($errors['fullname']) ? 'is-invalid' : '' ?>"
                        id="fullname" name="fullname"
                        value="<?= htmlspecialchars($fullname ?? '') ?>"
                        placeholder="Enter full name" required>
                    <?php if (isset($errors['fullname'])): ?>
                        <div class="invalid-feedback show"><?= htmlspecialchars($errors['fullname']) ?></div>
                    <?php endif; ?>
                    <div class="field-hint">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;margin-top:1px">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Full name of the official (e.g. Juan D. Cruz)
                    </div>
                </div>

                <hr class="field-separator">

                <!-- Password -->
                <div class="field-group">
                    <label for="password">
                        Password
                        <span class="req">*</span>
                    </label>
                    <div class="password-wrapper">
                        <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                            id="password" name="password"
                            placeholder="Enter password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                            <i class="fas fa-eye" style="display:none"></i>
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback show"><?= htmlspecialchars($errors['password']) ?></div>
                    <?php endif; ?>
                    <div class="field-hint">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;margin-top:1px">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Minimum 8 characters
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="field-group">
                    <label for="confirm_password">
                        Confirm Password
                        <span class="req">*</span>
                    </label>
                    <div class="password-wrapper">
                        <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                            id="confirm_password" name="confirm_password"
                            placeholder="Re-enter password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', this)">
                            <i class="fas fa-eye" style="display:none"></i>
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <div class="invalid-feedback show"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                    <?php endif; ?>
                </div>

            </div>

            <div class="form-card-footer">
                <a href="index.php?controller=users" class="btn-cancel-form">Cancel</a>
                <button type="submit" class="btn-generate">
                    <svg width="15" height="15" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                    </svg>
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePassword(fieldId, btn) {
    var input = document.getElementById(fieldId);
    var eyeOpen = btn.querySelector('.fa-eye');
    var eyeClosed = btn.querySelector('.fa-eye-slash');
    if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.style.display = 'inline-block';
        eyeClosed.style.display = 'none';
    } else {
        input.type = 'password';
        eyeOpen.style.display = 'none';
        eyeClosed.style.display = 'inline-block';
    }
}
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>

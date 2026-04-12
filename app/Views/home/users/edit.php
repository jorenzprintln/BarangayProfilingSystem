<?php
$content = ob_start();
?>

<link rel="stylesheet" href="public/assets/css/manage_users.css?v=<?= time() ?>">

<style>
/* ── Responsive Edit Account ── */
.edit-account-wrap {
    max-width: 580px;
    margin: 0 auto;
    padding: 0 1rem 2rem;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    color: #6b7280;
    font-size: .875rem;
    font-weight: 500;
    text-decoration: none;
    margin-bottom: 1.25rem;
    transition: color .2s;
}
.back-link:hover { color: white; text-decoration: none; }

.page-header {
    margin-bottom: 1.5rem;
}

.form-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 2px 16px rgba(67,97,238,.08), 0 1px 4px rgba(0,0,0,.06);
    overflow: hidden;
}

.form-card-header {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: 1rem 1.5rem;
    background: #f8faff;
    border-bottom: 1px solid #e8edf8;
    font-weight: 600;
    font-size: .9rem;
    color: #374151;
}

.form-card-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.field-group {
    display: flex;
    flex-direction: column;
    gap: .35rem;
}

.field-group label {
    font-size: .85rem;
    font-weight: 600;
    color: #374151;
    margin: 0;
}

.field-group .req {
    color: #ef4444;
    margin-left: 2px;
}

.field-group .form-control {
    border-radius: .6rem;
    border: 1.5px solid #e2e8f0;
    padding: .6rem .85rem;
    font-size: .9rem;
    color: #1e293b;
    transition: border-color .2s, box-shadow .2s;
    width: 100%;
}

.field-group .form-control:focus {
    border-color: #4361ee;
    box-shadow: 0 0 0 3px rgba(67,97,238,.1);
    outline: none;
}

.field-group .form-control.is-invalid {
    border-color: #ef4444;
}

.invalid-feedback.show {
    display: block;
    font-size: .8rem;
    color: #ef4444;
    margin-top: 2px;
}

.field-hint {
    display: flex;
    align-items: flex-start;
    gap: .35rem;
    font-size: .78rem;
    color: #94a3b8;
    margin-top: 2px;
}

.password-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.password-wrapper .form-control {
    padding-right: 2.75rem;
}

.password-toggle {
    position: absolute;
    right: .75rem;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 0;
    font-size: .95rem;
    line-height: 1;
    transition: color .2s;
}
.password-toggle:hover { color: #4361ee; }

.field-separator {
    border: none;
    border-top: 1px dashed #e2e8f0;
    margin: .25rem 0;
}

.password-notice {
    display: flex;
    align-items: center;
    gap: .5rem;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: .6rem;
    padding: .65rem 1rem;
    font-size: .82rem;
    color: #1e40af;
}

.form-card-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .75rem;
    padding: 1rem 1.5rem;
    background: #f8faff;
    border-top: 1px solid #e8edf8;
    flex-wrap: wrap;
}

.btn-cancel-form {
    padding: .6rem 1.25rem;
    border-radius: .6rem;
    background: #f1f5f9;
    color: #64748b;
    font-size: .875rem;
    font-weight: 600;
    text-decoration: none;
    border: 1.5px solid #e2e8f0;
    transition: background .2s, color .2s;
}
.btn-cancel-form:hover {
    background: #e2e8f0;
    color: #374151;
    text-decoration: none;
}

.btn-generate {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .6rem 1.4rem;
    border-radius: .6rem;
    background: linear-gradient(135deg, #4361ee, #3a56d4);
    color: #fff;
    font-size: .875rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(67,97,238,.3);
    transition: transform .15s, box-shadow .15s;
}
.btn-generate:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(67,97,238,.4);
}
.btn-generate:active { transform: translateY(0); }

/* Security alert */
.security-alert {
    border-radius: .75rem;
    border: none;
    border-left: 4px solid #f59e0b;
    background: #fffbeb;
    color: #92400e;
    margin-bottom: 1rem;
    padding: .85rem 1rem;
    font-size: .875rem;
}

/* Responsive tweaks */
@media (max-width: 576px) {
    .edit-account-wrap { padding: 0 .5rem 2rem; }
    .form-card-body { padding: 1.1rem 1rem; }
    .form-card-header { padding: .85rem 1rem; }
    .form-card-footer {
        flex-direction: column-reverse;
        align-items: stretch;
        padding: 1rem;
    }
    .btn-cancel-form,
    .btn-generate {
        width: 100%;
        justify-content: center;
        text-align: center;
    }
}
</style>

<div class="container-fluid px-4 mt-3">
    <div class="edit-account-wrap">

        <!-- Security Alert -->
        <?php if (Session::get('force_change_password')): ?>
        <div class="security-alert alert alert-dismissible" role="alert">
            <strong><i class="fas fa-exclamation-triangle mr-2"></i>Security Alert!</strong>
            You are using the default password. Please change it now before continuing.
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <?php endif; ?>

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
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                </svg>
                <div>
                    <h3 class="font-weight-bold mb-0">Edit Account</h3>
                    <p class="mb-0 mt-1" style="opacity:0.9;font-size:0.9rem;">
                        Update credentials for <strong><?= htmlspecialchars($editUser['username'] ?? '') ?></strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <div class="form-card-header">
                <svg width="14" height="14" fill="#4361ee" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
                </svg>
                <span class="form-card-header-label">Account Details</span>
            </div>

            <form method="POST" action="index.php?controller=users&action=update" id="editUserForm">
                <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars($editUser['id'] ?? '') ?>">

                <div class="form-card-body">

                    <!-- Username -->
                    <div class="field-group">
                        <label for="username">
                            Username <span class="req">*</span>
                        </label>
                        <input type="text"
                            class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                            id="username" name="username"
                            value="<?= htmlspecialchars($editUser['username'] ?? '') ?>"
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
                            Full Name <span class="req">*</span>
                        </label>
                        <input type="text"
                            class="form-control <?= isset($errors['fullname']) ? 'is-invalid' : '' ?>"
                            id="fullname" name="fullname"
                            value="<?= htmlspecialchars($editUser['fullname'] ?? '') ?>"
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

                    <!-- [EMAIL DISABLED] - uncomment when forgot password is re-enabled -->
                    <?php /*
                    <div class="field-group">
                        <label for="email">Email Address</label>
                        <div class="password-wrapper">
                            <input type="email"
                                class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                id="email" name="email"
                                value="<?= htmlspecialchars($editUser['email'] ?? '') ?>"
                                placeholder="e.g. admin@gmail.com">
                        </div>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback show"><?= htmlspecialchars($errors['email']) ?></div>
                        <?php endif; ?>
                        <div class="field-hint">
                            Used for <strong>Forgot Password</strong> recovery. Keep this up to date.
                        </div>
                        <?php if (empty($editUser['email'])): ?>
                            <div class="field-hint" style="color:#e53e3e;margin-top:4px;">
                                No email set — you won't be able to use Forgot Password without this.
                            </div>
                        <?php endif; ?>
                    </div>
                    */ ?>

                    <hr class="field-separator">

                    <!-- Password Notice -->
                    <div class="password-notice">
                        <svg width="16" height="16" fill="#4361ee" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span>Leave password fields blank to keep the current password</span>
                    </div>

                    <!-- New Password -->
                    <div class="field-group">
                        <label for="password">New Password</label>
                        <div class="password-wrapper">
                            <input type="password"
                                class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                id="password" name="password"
                                placeholder="Enter new password (optional)">
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
                            Minimum 8 characters. Leave blank to keep current password.
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="field-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <div class="password-wrapper">
                            <input type="password"
                                class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                                id="confirm_password" name="confirm_password"
                                placeholder="Re-enter new password">
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
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                        Update Account
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
function togglePassword(fieldId, btn) {
    var input    = document.getElementById(fieldId);
    var eyeOpen  = btn.querySelector('.fa-eye');
    var eyeClosed = btn.querySelector('.fa-eye-slash');
    if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.style.display   = 'inline-block';
        eyeClosed.style.display = 'none';
    } else {
        input.type = 'password';
        eyeOpen.style.display   = 'none';
        eyeClosed.style.display = 'inline-block';
    }
}
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
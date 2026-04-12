<?php
$content = ob_start();
?>

<style>
*, *::before, *::after { box-sizing: border-box; }

/* ── Match login.css background exactly ── */
.recovery-wrap {
    min-height: 100vh;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0f2027 0%, #203a43 40%, #2c5364 100%);
    padding: 1rem;
    position: relative;
    overflow: hidden;
}

/* Floating circles — same as login.css */
.recovery-wrap::before,
.recovery-wrap::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    opacity: 0.06;
    background: #fff;
    z-index: 0;
    pointer-events: none;
}
.recovery-wrap::before { width: 400px; height: 400px; top: -100px; right: -80px; }
.recovery-wrap::after  { width: 300px; height: 300px; bottom: -60px; left: -60px; }

/* ── Card — match login card ── */
.recovery-card {
    background: rgba(255, 255, 255, 0.97);
    border: none;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255,255,255,0.05);
    width: 100%;
    max-width: 440px;
    position: relative;
    z-index: 1;
    backdrop-filter: blur(10px);
    animation: cardFadeIn 0.5s ease-out;
    overflow: hidden;
}

@keyframes cardFadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Header — match login logo-header ── */
.recovery-header {
    background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
    padding: 18px 20px 14px;
    position: relative;
    overflow: hidden;
}

.recovery-header::after {
    content: '';
    position: absolute;
    top: -50%; right: -30%;
    width: 200px; height: 200px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
    pointer-events: none;
}

.recovery-header img {
    width: 56px;
    height: auto;
    filter: drop-shadow(0 2px 8px rgba(0,0,0,0.2));
}

.recovery-header h5 {
    color: white;
    font-size: 0.9rem;
    font-weight: 700;
    margin: 0;
    letter-spacing: 0.5px;
    text-shadow: 0 1px 3px rgba(0,0,0,0.15);
}

.recovery-header small {
    font-size: 0.65rem;
    opacity: 0.85;
    line-height: 1.4;
}

/* ── Body ── */
.recovery-body {
    padding: 1.4rem 1.75rem 1.6rem;
}

.recovery-title {
    color: #1a202c;
    font-weight: 800;
    font-size: 1.4rem;
    margin-bottom: .2rem;
    letter-spacing: -0.3px;
    text-align: center;
}

.recovery-subtitle {
    color: #718096;
    font-size: 0.82rem;
    font-style: italic;
    text-align: center;
    margin-bottom: 1.1rem;
}

/* ── Step Indicator ── */
.step-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    margin-bottom: 1.1rem;
}

.step {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
    font-weight: 700;
    flex-shrink: 0;
}

.step.active   { background: #1a73e8; color: white; }
.step.done     { background: #22c55e; color: white; }
.step.inactive { background: #e2e8f0; color: #94a3b8; }

.step-line {
    flex: 1;
    height: 2px;
    background: #e2e8f0;
    max-width: 50px;
}
.step-line.done { background: #22c55e; }

/* ── Alerts ── */
.alert-error {
    background: #fff5f5;
    border-left: 4px solid #fc8181;
    border-radius: 12px;
    padding: .6rem 1rem;
    color: #c53030;
    font-size: .82rem;
    margin-bottom: .9rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}

.alert-success-box {
    background: #f0fff4;
    border-left: 4px solid #68d391;
    border-radius: 12px;
    padding: .6rem 1rem;
    color: #276749;
    font-size: .82rem;
    margin-bottom: .9rem;
    display: flex;
    align-items: center;
    gap: .5rem;
    flex-wrap: wrap;
}

/* ── Field Groups ── */
.field-group { margin-bottom: .85rem; }

.field-group label {
    display: block;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 6px;
    font-size: 0.85rem;
    letter-spacing: 0.2px;
}

.field-group .form-control {
    border-radius: 12px;
    padding: 12px 44px 12px 16px;
    border: 2px solid #e2e8f0;
    transition: all 0.25s ease;
    background: #f8fafc;
    font-size: 0.92rem;
    color: #1a202c;
    width: 100%;
}

.field-group .form-control::placeholder {
    color: #a0aec0;
    font-size: 0.88rem;
}

.field-group .form-control:focus {
    border-color: #1a73e8;
    box-shadow: 0 0 0 4px rgba(26, 115, 232, 0.1);
    background: #fff;
    outline: none;
}

.pw-wrapper { position: relative; }

.pw-toggle {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #a0aec0;
    cursor: pointer;
    padding: 0;
    font-size: 0.95rem;
    transition: color 0.2s;
}
.pw-toggle:hover { color: #1a73e8; }

/* ── Button — match btn-login ── */
.btn-recover {
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    margin-top: .35rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
}

.btn-recover::after {
    content: '';
    position: absolute;
    top: 50%; left: 50%;
    width: 0; height: 0;
    background: rgba(255,255,255,0.15);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.5s, height 0.5s;
}
.btn-recover:hover::after { width: 300px; height: 300px; }
.btn-recover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(26, 115, 232, 0.4);
}
.btn-recover:active { transform: translateY(0); }

/* ── Back link ── */
.back-to-login {
    display: block;
    text-align: center;
    margin-top: .85rem;
    font-size: 0.85rem;
    color: #718096;
    text-decoration: none;
    font-weight: 500;
    transition: color .2s;
}
.back-to-login:hover { color: #1a73e8; text-decoration: none; }

/* ── Responsive ── */
@media (max-width: 576px) {
    .recovery-wrap { padding: 0.75rem; }
    .recovery-card { border-radius: 16px; }
    .recovery-body { padding: 1rem 1.1rem 1.3rem; }
    .recovery-header { padding: 13px 14px 11px; }
    .recovery-header img { width: 44px !important; }
    .recovery-header small { font-size: 0.6rem; }
    .recovery-header h5 { font-size: 0.8rem; }
    .recovery-title { font-size: 1.2rem; }
    .field-group .form-control { padding: 11px 40px 11px 14px; font-size: 0.88rem; }
    .btn-recover { padding: 12px; font-size: 0.88rem; }
}

@media (max-width: 360px) {
    .recovery-header img { width: 38px !important; }
    .recovery-title { font-size: 1.05rem; }
}
</style>

<div class="recovery-wrap">
    <div class="recovery-card">

        <!-- Header — matches login logo-header layout -->
        <div class="recovery-header">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <img src="public/assets/imgs/brgy.logo.png" alt="Barangay Logo">
                <div class="text-center flex-grow-1">
                    <div><small class="text-white">Republic of the Philippines</small></div>
                    <div><small class="text-white">City of Tacloban</small></div>
                    <div><small class="text-white font-weight-bold">Barangay 36-A</small></div>
                </div>
                <img src="public/assets/imgs/city_logo.png" alt="City Logo">
            </div>
            <h5 class="text-center mb-0">Barangay Profiling System</h5>
        </div>

        <!-- Body -->
        <div class="recovery-body">

            <h4 class="recovery-title">
                <i class="fas fa-shield-alt mr-1" style="color:#1a73e8;"></i> Account Recovery
            </h4>
            <p class="recovery-subtitle">Enter your secret recovery key and set a new password.</p>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step <?= !isset($step2) ? 'active' : 'done' ?>">
                    <?= isset($step2) ? '<i class="fas fa-check"></i>' : '1' ?>
                </div>
                <div class="step-line <?= isset($step2) ? 'done' : '' ?>"></div>
                <div class="step <?= isset($step2) ? 'active' : 'inactive' ?>">2</div>
            </div>

            <!-- Alerts -->
            <?php if (isset($error)): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert-success-box">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                    <a href="index.php?controller=auth&action=login"
                       style="margin-left:.4rem;color:#276749;font-weight:700;white-space:nowrap;">
                        Log in now →
                    </a>
                </div>
            <?php endif; ?>

            <?php if (!isset($success)): ?>

                <?php if (!isset($step2)): ?>
                <!-- Step 1: Enter Secret Key -->
                <form method="POST" action="index.php?controller=auth&action=adminRecovery">
                    <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                    <input type="hidden" name="step" value="1">

                    <div class="field-group">
                        <label for="recovery_key">
                            <i class="fas fa-key mr-1"></i> Secret Recovery Key
                        </label>
                        <div class="pw-wrapper">
                            <input type="password" class="form-control" id="recovery_key"
                                name="recovery_key" placeholder="Enter your secret recovery key" required>
                            <button type="button" class="pw-toggle" onclick="togglePw('recovery_key', this)">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-recover">
                        <i class="fas fa-arrow-right"></i> Continue
                    </button>
                </form>

                <?php else: ?>
                <!-- Step 2: Set New Password -->
                <form method="POST" action="index.php?controller=auth&action=adminRecovery">
                    <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                    <input type="hidden" name="step" value="2">
                    <input type="hidden" name="recovery_key" value="<?= htmlspecialchars($_SESSION['recovery_verified_key'] ?? '') ?>">

                    <div class="field-group">
                        <label for="new_password">
                            <i class="fas fa-lock mr-1"></i> New Password
                        </label>
                        <div class="pw-wrapper">
                            <input type="password" class="form-control" id="new_password"
                                name="new_password" placeholder="Enter new password" required minlength="8">
                            <button type="button" class="pw-toggle" onclick="togglePw('new_password', this)">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        <small style="color:#a0aec0;font-size:.75rem;">Minimum 8 characters</small>
                    </div>

                    <div class="field-group">
                        <label for="confirm_password">
                            <i class="fas fa-lock mr-1"></i> Confirm New Password
                        </label>
                        <div class="pw-wrapper">
                            <input type="password" class="form-control" id="confirm_password"
                                name="confirm_password" placeholder="Re-enter new password" required>
                            <button type="button" class="pw-toggle" onclick="togglePw('confirm_password', this)">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        <div id="pwMismatch" style="display:none;color:#c53030;font-size:.78rem;margin-top:.25rem;">
                            <i class="fas fa-exclamation-circle mr-1"></i>Passwords do not match.
                        </div>
                    </div>

                    <button type="submit" class="btn-recover">
                        <i class="fas fa-check"></i> Reset Password
                    </button>
                </form>
                <?php endif; ?>

            <?php endif; ?>

            <a href="index.php?controller=auth&action=login" class="back-to-login">
                <i class="fas fa-arrow-left mr-1"></i> Back to Login
            </a>

        </div>
    </div>
</div>

<script>
function togglePw(fieldId, btn) {
    var input = document.getElementById(fieldId);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye-slash';
    }
}

var confirmPw = document.getElementById('confirm_password');
var newPw     = document.getElementById('new_password');
if (confirmPw && newPw) {
    confirmPw.addEventListener('input', function() {
        document.getElementById('pwMismatch').style.display =
            this.value !== newPw.value ? 'block' : 'none';
    });
}
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
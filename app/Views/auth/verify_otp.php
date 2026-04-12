<?php $content = ob_start(); ?>

<link rel="stylesheet" href="public/assets/css/register.css?v=<?= time() ?>">

<div class="container">
    <div class="row">
        <div class="col-sm-10 col-md-6 col-lg-5 mx-auto">
            <div class="border-0 shadow-lg rounded-lg my-5 register-card">
                <div class="card-body p-4 p-sm-5">

                    <div class="logo-header">
                        <div class="d-flex justify-content-between align-items-center text-center mb-3">
                            <img src="public/assets/imgs/brgy.logo.png" alt="Barangay Logo" class="img-fluid" style="width:80px;">
                            <div class="d-flex flex-column">
                                <small class="text-white">Republic of the Philippines</small>
                                <small class="text-white">City of Tacloban</small>
                                <small class="text-white font-weight-bold">Barangay 36-A</small>
                            </div>
                            <img src="public/assets/imgs/city_logo.png" alt="City Logo" class="img-fluid" style="width:80px;">
                        </div>
                        <h5 class="text-center mb-0 text-white font-weight-bold">Barangay Profiling System</h5>
                    </div>

                    <div class="text-center mb-4 mt-3">
                        <div style="font-size:2.5rem;color:#1d4ed8;"><i class="fas fa-envelope-open-text"></i></div>
                        <h4 class="card-title-main mt-2">Verify Your Email</h4>
                        <p class="card-subtitle">
                            We sent a 6-digit OTP to<br>
                            <strong><?= htmlspecialchars($_SESSION['pending_registration']['email'] ?? '') ?></strong>
                        </p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <?php if (isset($info)): ?>
                        <div class="alert alert-info py-2"><?= htmlspecialchars($info) ?></div>
                    <?php endif; ?>

                    <form action="index.php?controller=auth&action=verifyOtp" method="POST">
                        <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                        <div class="mb-4">
                            <label class="form-label">Enter OTP</label>
                            <input type="text"
                                name="otp"
                                class="form-control text-center"
                                style="font-size:1.6rem;letter-spacing:10px;font-weight:700;"
                                maxlength="6"
                                placeholder="······"
                                autocomplete="one-time-code"
                                required>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-register btn-block">
                                <i class="fas fa-check-circle mr-2"></i> Verify OTP
                            </button>
                        </div>
                    </form>

                    <!-- Resend -->
                    <div class="text-center">
                        <span class="text-muted" style="font-size:0.88rem;">Didn't receive it?</span>
                        <a href="index.php?controller=auth&action=resendOtp" style="font-size:0.88rem;">
                            Resend OTP
                        </a>
                    </div>

                    <!-- Countdown -->
                    <div class="text-center mt-2 text-muted" style="font-size:0.82rem;">
                        OTP expires in <span id="countdown" style="font-weight:700;color:#1d4ed8;">10:00</span>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    let seconds = 600;
    const el = document.getElementById('countdown');
    const interval = setInterval(function () {
        seconds--;
        if (seconds <= 0) {
            clearInterval(interval);
            el.textContent = 'Expired';
            el.style.color = '#ef4444';
            return;
        }
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        el.textContent = m + ':' + String(s).padStart(2, '0');
    }, 1000);

    // Loading state on OTP submit
    const form = document.querySelector('form[action*="action=verifyOtp"]');
    const btn = form ? form.querySelector('button[type="submit"]') : null;
    if (form && btn) {
        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Verifying...';
        });
    }
})();
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
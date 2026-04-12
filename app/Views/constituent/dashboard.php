<?php
$content = ob_start();
?>

<link rel="stylesheet" href="public/assets/css/constituent_dashboard.css?v=<?= time() ?>">

<?php if (!empty($toast_success)): ?>
<div class="toast-container">
    <div class="custom-toast show">
        <div class="toast-icon"><i class="fas fa-check-circle"></i></div>
        <div class="toast-body-text"><?= htmlspecialchars($toast_success) ?></div>
        <button class="toast-close" onclick="this.closest('.toast-container').remove()">&times;</button>
        <div class="toast-progress"></div>
    </div>
</div>
<script>
(function() {
    var toast = document.querySelector('.custom-toast');
    if (!toast) return;
    setTimeout(function() {
        toast.classList.add('toast-hiding');
        setTimeout(function() { toast.closest('.toast-container').remove(); }, 300);
    }, 4000);
})();
</script>
<?php endif; ?>
    <div class="container-fluid cd-page-wrap" style="padding-left: 2rem; padding-right: 0.75rem;">

    <!-- Hero Banner -->
    <div class="cd-hero">
        <div class="cd-hero-inner">
            <div class="cd-hero-left">
                <div class="cd-hero-greeting">Welcome back</div>
                <?php
                    $displayName = $user['fullname'] ?: $user['username'];

                    if (!empty($constituent)) {
                        $parts = array_filter([
                            $constituent['first_name'] ?? '',
                            $constituent['middle_name'] ? strtoupper(substr($constituent['middle_name'], 0, 1)) . '.' : '',
                            $constituent['last_name'] ?? '',
                            $constituent['suffix'] ?? '',
                        ]);
                        $built = implode(' ', $parts);
                        if ($built !== '') {
                            $displayName = htmlspecialchars($built);
                        }
                    }
                    ?>
                    <div class="cd-hero-name"><?= $displayName ?>!</div>
                <p class="cd-hero-sub">Manage your profile and request barangay documents below.</p>
            </div>
            <img src="public/assets/imgs/brgy.logo.png" alt="Barangay Logo" class="cd-hero-logo">
        </div>
    </div>

    <!-- Quick Actions -->
    <p class="cd-section-label">Quick Access</p>
    <div class="cd-actions-grid">

        <a href="index.php?controller=constituent&action=profile" class="cd-action-card cd-action-card-1">
            <div class="cd-action-icon cd-icon-blue">
                <i class="fas fa-user-edit"></i>
            </div>
            <div class="cd-action-body">
                <div class="cd-action-title">My Profile</div>
                <p class="cd-action-desc">View and update your personal information</p>
            </div>
            <div class="cd-action-footer">
                <div class="cd-action-arrow"><i class="fas fa-chevron-right"></i></div>
            </div>
        </a>

        <a href="index.php?controller=constituent&action=requestDocument" class="cd-action-card cd-action-card-2">
            <div class="cd-action-icon cd-icon-green">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="cd-action-body">
                <div class="cd-action-title">Request Document</div>
                <p class="cd-action-desc">Request certificates and clearances</p>
            </div>
            <div class="cd-action-footer">
                <div class="cd-action-arrow"><i class="fas fa-chevron-right"></i></div>
            </div>
        </a>

        <a href="index.php?controller=constituent&action=myRequests" class="cd-action-card cd-action-card-3">
            <div class="cd-action-icon cd-icon-orange">
                <i class="fas fa-clock"></i>
            </div>
            <div class="cd-action-body">
                <div class="cd-action-title">My Requests</div>
                <p class="cd-action-desc">Track and view your document requests</p>
            </div>
            <div class="cd-action-footer">
                <div class="cd-action-arrow"><i class="fas fa-chevron-right"></i></div>
            </div>
        </a>

    </div>

    <!-- Notice -->
    <div class="cd-notice">
        <i class="fas fa-info-circle cd-notice-icon"></i>
        <p class="cd-notice-text">
            <strong>Need help?</strong>
            Visit the Barangay 36-A hall or contact the barangay secretary for assistance with document requests and other concerns.
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>

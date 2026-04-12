<?php
$_displayFullname = trim($_SESSION['fullname'] ?? '');
if ($_displayFullname === '') {
    $_displayFullname = $_SESSION['username'] ?? '';
}

$hasProfileStatusUpdate = false;
try {
    if (!empty($_SESSION['user_id'])) {
        $sidebarDb = (new Database())->connect();
        $latestProfileStatusStmt = $sidebarDb->prepare(
            "SELECT id, status, seen_at
             FROM constituent_profile_requests
             WHERE user_id = :user_id
             ORDER BY id DESC
             LIMIT 1"
        );
        $latestProfileStatusStmt->bindValue(':user_id', (int)$_SESSION['user_id'], PDO::PARAM_INT);
        $latestProfileStatusStmt->execute();
        $latestRequest = $latestProfileStatusStmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $latestStatus = strtolower((string)($latestRequest['status'] ?? ''));
        if (in_array($latestStatus, ['approved', 'rejected'], true)) {
            $hasProfileStatusUpdate = empty($latestRequest['seen_at']);
        }
    }
} catch (Exception $e) {
    $hasProfileStatusUpdate = false;
}

$hasDocumentStatusUpdate = false;
try {
    if (!empty($_SESSION['user_id'])) {
        $sidebarDb = (new Database())->connect();
        $user = $sidebarDb->prepare("SELECT username FROM users WHERE id = :id LIMIT 1");
        $user->execute([':id' => (int)$_SESSION['user_id']]);
        $userRow = $user->fetch(PDO::FETCH_ASSOC);
        $username = $userRow['username'] ?? '';

        if ($username !== '') {
            $docStmt = $sidebarDb->prepare(
            "SELECT COUNT(*) FROM transactions
            WHERE requested_by = :username
            AND UPPER(generated_by) NOT IN ('PENDING')
            AND generated_by IS NOT NULL
            AND generated_by != ''
            AND seen_at IS NULL
            AND transaction IN (
                'Barangay Certificate',
                'Barangay Indigency',
                'Certificate of Good Moral',
                'Barangay Certificate for Business',
                'Certificate of Unemployment',
                'Certificate of Solo Parent',
                'Barangay Certificate for OFW'
            )"
        );
            $docStmt->execute([':username' => $username]);
            $hasDocumentStatusUpdate = (int)$docStmt->fetchColumn() > 0;
        }
    }
} catch (Exception $e) {
    $hasDocumentStatusUpdate = false;
}

$hasVehicleStatusUpdate = false;
try {
    if (!empty($_SESSION['user_id'])) {
        $sidebarDb = (new Database())->connect();
        $vehStmt = $sidebarDb->prepare("
            SELECT COUNT(*) FROM vehicle_requests
            WHERE user_id = :uid
              AND status IN ('approved', 'rejected')
              AND seen_at IS NULL
        ");
        $vehStmt->execute([':uid' => (int)$_SESSION['user_id']]);
        $hasVehicleStatusUpdate = (int)$vehStmt->fetchColumn() > 0;
    }
} catch (Exception $e) {
    $hasVehicleStatusUpdate = false;
}

$currentController = $_GET['controller'] ?? '';
$currentAction = $_GET['action'] ?? '';
$currentTab = strtolower((string)($_GET['tab'] ?? 'profile'));
$isMyRequestsSectionActive = $currentController === 'constituent' && $currentAction === 'myRequests';
$isProfileRequestsActive = $isMyRequestsSectionActive && $currentTab === 'profile';
$isDocumentRequestsActive = $isMyRequestsSectionActive && $currentTab === 'document';
$isVehicleRequestsActive = $isMyRequestsSectionActive && $currentTab === 'vehicle';
?>

<div class="d-flex flex-column flex-shrink-0 bg-gradient h-100">
    <!-- Header -->
    <div class="sidebar-header p-3 text-center border-bottom">
        <a href="index.php?controller=constituent" class="text-decoration-none">
            <div class="d-flex align-items-center justify-content-center mb-1">
                <img src="public/assets/imgs/brgy.logo.png" alt="Barangay Logo" class="sidebar-logo">
            </div>
            <h6 class="mb-0 fw-bold text-primary" style="font-size:0.9rem;"><?= APP_NAME ?></h6>
            <small class="text-muted" style="font-size:0.72rem;">Brgy. 36-A - Constituent Portal</small>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-grow-1 py-2 px-2">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a href="index.php?controller=constituent"
                    class="nav-link d-flex align-items-center rounded-3 <?= ($title ?? '') === 'My Dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt nav-icon" style="font-size:1rem;width:20px;text-align:center;margin-right:12px;opacity:0.8;"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?controller=constituent&action=profile"
                    class="nav-link d-flex align-items-center rounded-3 <?= ($title ?? '') === 'My Profile' ? 'active' : '' ?>">
                    <i class="fas fa-user nav-icon" style="font-size:1rem;width:20px;text-align:center;margin-right:12px;opacity:0.8;"></i>
                    <span>My Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?controller=constituent&action=requestDocument"
                    class="nav-link d-flex align-items-center rounded-3 <?= ($title ?? '') === 'Request Document' ? 'active' : '' ?>">
                    <i class="fas fa-file-signature nav-icon" style="font-size:1rem;width:20px;text-align:center;margin-right:12px;opacity:0.8;"></i>
                    <span>Request Document</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?controller=constituent&action=requestVehicle"
                    class="nav-link d-flex align-items-center rounded-3 <?= ($title ?? '') === 'Request Vehicle Registration' ? 'active' : '' ?>">
                    <i class="fas fa-car nav-icon" style="font-size:1rem;width:20px;text-align:center;margin-right:12px;opacity:0.8;"></i>
                    <span>Register Vehicle</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#myRequestsMenu"
                    data-toggle="collapse"
                    role="button"
                    onclick="return false;"
                    aria-expanded="<?= $isMyRequestsSectionActive ? 'true' : 'false' ?>"
                    aria-controls="myRequestsMenu"
                    class="nav-link request-toggle d-flex align-items-center rounded-3 <?= $isMyRequestsSectionActive ? 'active' : '' ?>">
                    <i class="fas fa-inbox nav-icon" style="font-size:1rem;width:20px;text-align:center;margin-right:12px;opacity:0.8;"></i>
                    <span class="request-label-wrap">
                        <span class="request-label">My Requests</span>
                        <?php if ($hasProfileStatusUpdate || $hasDocumentStatusUpdate || $hasVehicleStatusUpdate): ?>
                            <span class="request-dot" title="You have updated request statuses"></span>
                        <?php endif; ?>
                    </span>
                    <i class="fas fa-chevron-down request-chevron"></i>
                </a>

                <div class="collapse submenu-group <?= $isMyRequestsSectionActive ? 'show' : '' ?>" id="myRequestsMenu">
                    <a href="index.php?controller=constituent&action=myRequests&tab=profile"
                        class="nav-sublink <?= $isProfileRequestsActive ? 'active' : '' ?>">
                        <span>Profile Requests</span>
                        <?php if ($hasProfileStatusUpdate): ?>
                            <span class="pending-badge-sm request-badge">1</span>
                        <?php endif; ?>
                    </a>
                    <a href="index.php?controller=constituent&action=myRequests&tab=document"
                        class="nav-sublink <?= $isDocumentRequestsActive ? 'active' : '' ?>">
                        <span>Document Requests</span>
                        <?php if ($hasDocumentStatusUpdate): ?>
                            <span class="pending-badge-sm request-badge">1</span>
                        <?php endif; ?>
                    </a>

                    <a href="index.php?controller=constituent&action=myRequests&tab=vehicle"
                        class="nav-sublink <?= $isVehicleRequestsActive ? 'active' : '' ?>">
                        <span>Vehicle Requests</span>
                        <?php if ($hasVehicleStatusUpdate): ?>
                            <span class="pending-badge-sm request-badge" style="margin-left:auto;background:#2563eb;">!</span>
                        <?php endif; ?>
                    </a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Bottom Account Menu -->
    <div class="sidebar-footer border-top p-2">
        <?php if (isset($_SESSION['username'])): ?>
            <div class="account-dropdown mx-auto" id="accountDropdown" style="max-width: 230px;">
                <button class="btn account-toggle w-100 d-flex align-items-center" type="button" id="accountMenuBtn" aria-haspopup="true" aria-expanded="false">
                    <img src="public/assets/imgs/brgy.logo.png" alt="Barangay Logo" class="account-avatar">
                    <div class="account-meta text-left">
                        <div class="account-username"><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="account-brgy"><?= htmlspecialchars($_displayFullname, ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <i class="fas fa-chevron-down account-caret ml-auto"></i>
                </button>

                <div class="account-menu" aria-labelledby="accountMenuBtn">
                    <a class="dropdown-item account-item" href="index.php?controller=constituent&action=accountSettings">
                        <i class="fas fa-cog mr-2"></i>
                        Account Settings
                    </a>
                    <button type="button" class="dropdown-item account-item logout-item" id="logoutBtn">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Log out
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Modern Sidebar Styles */
.sidebar {
    background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    min-width: 260px;
    overflow: visible;
}

.sidebar-header {
    background: white;
    flex-shrink: 0;
}

.sidebar-header a {
    text-decoration: none !important;
}

.sidebar-header a:hover {
    text-decoration: none !important;
}

.sidebar-header a:hover * {
    text-decoration: none !important;
}

.sidebar-logo {
    width: 42px;
    height: 42px;
    transition: transform 0.3s ease;
}

.sidebar-logo:hover {
    transform: scale(1.1);
}

/* Section Label */
.sidebar-section-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #9ca3af;
    padding: 0.9rem 1.25rem 0.45rem;
    margin-top: 0.25rem;
}

/* Navigation Links */
.nav-link {
    color: #495057;
    font-weight: 500;
    padding: 0.95rem 1.4rem;
    margin: 0.2rem 0.5rem;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
    white-space: nowrap;
    font-size: 1rem;
}

.nav-link:hover {
    background-color: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
    border-left-color: #0d6efd;
    transform: translateX(5px);
}

.nav-link.active {
    background-color: #0d6efd;
    color: white;
    border-left-color: #0a58ca;
    box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3);
}

.nav-link.active:hover {
    background-color: #0b5ed7;
    transform: translateX(0);
}

.nav-icon {
    width: 24px;
    height: 24px;
    margin-right: 14px;
    opacity: 0.8;
    flex-shrink: 0;
}

.nav-link.active .nav-icon {
    opacity: 1;
    filter: brightness(0) invert(1);
}

.nav-link.active i.nav-icon {
    filter: none;
    color: white;
}

.request-toggle:hover {
    transform: translateX(0);
}

.request-label {
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.request-label-wrap {
    position: relative;
    display: inline-flex;
    min-width: 0;
    padding-right: 0.6rem;
}

.request-dot {
    position: absolute;
    top: -4px;
    right: -2px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #f59e0b;
    box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
    animation: requestDotPulse 1.4s ease-in-out infinite;
}

.request-chevron {
    margin-left: auto;
    font-size: 0.7rem;
    transition: transform 0.2s ease;
}

.request-toggle[aria-expanded='true'] .request-chevron {
    transform: rotate(180deg);
}

.submenu-group {
    margin: 0.2rem 0.6rem 0.35rem 2.25rem;
    padding: 0.25rem;
    border-radius: 0.65rem;
    background: rgba(148, 163, 184, 0.16);
    overflow: hidden;
}

.nav-sublink {
    display: flex;
    align-items: center;
    margin: 0.25rem 0.4rem;
    padding: 0.65rem 0.85rem;
    border-radius: 0.5rem;
    color: #4b5563;
    font-size: 0.88rem;
    text-decoration: none;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.nav-sublink:hover {
    background: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
    text-decoration: none;
}

.nav-sublink.active {
    background: #dbeafe;
    color: #1d4ed8;
    font-weight: 600;
}

.request-badge {
    margin-left: auto;
    background: #2563eb;
}

@keyframes requestDotPulse {
    0%,
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
    }
    50% {
        transform: scale(1.28);
        box-shadow: 0 0 0 6px rgba(245, 158, 11, 0.14);
    }
}

@keyframes badgePulse {
    0%,
    100% { opacity: 1; }
    50% { opacity: 0.6; }
}

.pending-badge-sm {
    color: #fff;
    font-size: 0.62rem;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    line-height: 18px;
    text-align: center;
    border-radius: 9px;
    padding: 0 5px;
    animation: badgePulse 2s ease-in-out infinite;
}

.sidebar-footer {
    flex-shrink: 0;
    overflow: visible;
}

/* Bottom account dropdown */
.account-dropdown {
    position: relative;
}

.account-toggle {
    font-family: 'Montserrat', sans-serif;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 0.55rem 0.6rem;
    transition: all 0.2s ease;
}

.account-toggle:hover,
.account-toggle:focus {
    background: #f1f5f9;
    border-color: #cbd5e1;
    box-shadow: none;
}

.account-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #dbeafe;
    flex-shrink: 0;
}

.account-meta {
    margin-left: 0.55rem;
    min-width: 0;
    flex: 1;
    overflow: hidden;
}

.account-username {
    font-size: 0.82rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1.15;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 140px;
}

.account-brgy {
    font-size: 0.68rem;
    color: #64748b;
    line-height: 1.1;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 140px;
}

.account-caret {
    font-size: 0.65rem;
    color: #6b7280;
    transition: transform 0.2s ease;
}

.account-menu {
    font-family: 'Montserrat', sans-serif;
    display: none;
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 100%;
    min-width: 230px;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 0.35rem;
    background: #fff;
    box-shadow: 0 12px 26px rgba(15, 23, 42, 0.16);
    z-index: 1200;
}

.account-dropdown.open-upward .account-menu {
    top: auto;
    bottom: calc(100% + 8px);
}
.account-dropdown.open .account-menu {
    display: block;
}

.account-dropdown.open .account-caret {
    transform: rotate(180deg);
}

.account-item {
    display: flex;
    align-items: center;
    border-radius: 10px;
    padding: 0.5rem 0.65rem;
    font-size: 0.86rem;
    font-weight: 600;
    color: #334155;
}

.account-item:hover {
    background: #f8fafc;
    color: #0f172a;
}

.logout-item {
    color: #dc2626;
    cursor: pointer;
}

.logout-item:hover {
    background: #fef2f2;
    color: #b91c1c;
}

/* Mobile Responsive */
@media (max-width: 992px) {
    .sidebar {
        width: 250px !important;
        min-width: 250px;
    }

    .nav-link {
        padding: 0.55rem 0.85rem;
        font-size: 0.82rem;
    }

    .sidebar-section-label {
        font-size: 0.6rem;
        padding: 0.6rem 1rem 0.3rem;
    }
}

@media (max-width: 768px) {
    .sidebar-header {
        padding: 1rem !important;
    }

    .sidebar-logo {
        width: 36px;
        height: 36px;
    }

    .nav-link {
        padding: 0.5rem 0.85rem;
        margin: 0 0.2rem;
    }
}

@media (max-width: 576px) {
    .nav-link span {
        font-size: 0.8rem;
    }

    .nav-icon {
        width: 18px;
        height: 18px;
        margin-right: 10px;
    }
}
</style>

<script>
(function () {
    var dropdown = document.getElementById('accountDropdown');
    var button = document.getElementById('accountMenuBtn');
    if (!dropdown || !button) return;

    function shouldOpenUpward() {
        var rect = button.getBoundingClientRect();
        var menuHeight = 100;
        var spaceBelow = window.innerHeight - rect.bottom;
        return spaceBelow < menuHeight + 16;
    }

    button.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var isOpen = dropdown.classList.contains('open');
        if (isOpen) {
            dropdown.classList.remove('open');
            dropdown.classList.remove('open-upward');
            button.setAttribute('aria-expanded', 'false');
        } else {
            dropdown.classList.remove('open-upward');
            if (shouldOpenUpward()) {
                dropdown.classList.add('open-upward');
            }
            dropdown.classList.add('open');
            button.setAttribute('aria-expanded', 'true');
        }
    });

    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
            dropdown.classList.remove('open-upward');
            button.setAttribute('aria-expanded', 'false');
        }
    });
})();
</script>

<?php
    // Get pending approval count for sidebar badge
    $pendingCount = 0;
    $pendingProfileCount = 0;
    try {
        $sidebarDb = (new Database())->connect();
        $pendingStmt = $sidebarDb->query("SELECT COUNT(*) FROM users WHERE status = 'pending' AND deleted_at IS NULL");
        $pendingCount = (int) $pendingStmt->fetchColumn();
    } catch (Exception $e) {
        $pendingCount = 0;
    }

    try {
        $sidebarDb = (new Database())->connect();
        $pendingProfileStmt = $sidebarDb->query("SELECT COUNT(*) FROM constituent_profile_requests WHERE status = 'pending'");
        $pendingProfileCount = (int) $pendingProfileStmt->fetchColumn();
    } catch (Exception $e) {
        $pendingProfileCount = 0;
    }

    $pendingDocumentCount = 0;
    try {
        $sidebarDb = (new Database())->connect();
        $pendingDocumentStmt = $sidebarDb->query(
            "SELECT COUNT(*) FROM transactions
            WHERE UPPER(generated_by) = 'PENDING'
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
        $pendingDocumentCount = (int) $pendingDocumentStmt->fetchColumn();
    } catch (Exception $e) {
        $pendingDocumentCount = 0;
    }

    $pendingVehicleRequestCount = 0;
    try {
        $sidebarDb = (new Database())->connect();
        $vStmt = $sidebarDb->query("SELECT COUNT(*) FROM vehicle_requests WHERE status = 'pending'");
        $pendingVehicleRequestCount = (int) $vStmt->fetchColumn();
    } catch (Exception $e) {
        $pendingVehicleRequestCount = 0;
    }

    $isConstituentRequestsActive = in_array($title ?? '', ['Profile Requests', 'Document Requests', 'Vehicle Requests'], true);
    $pendingRequestsTotal = $pendingProfileCount + $pendingDocumentCount + $pendingVehicleRequestCount;
?>

<div class="d-flex flex-column flex-shrink-0 bg-gradient h-100">
    <!-- Header -->
    <div class="sidebar-header p-3 text-center border-bottom">
        <a href="index.php" class="text-decoration-none">
            <div class="d-flex align-items-center justify-content-center mb-1">
                <img src="public/assets/imgs/brgy.logo.png" alt="Barangay Logo" class="sidebar-logo">
            </div>
            <h6 class="mb-0 fw-bold text-primary" style="font-size:0.9rem;"><?= APP_NAME ?></h6>
            <small class="text-muted" style="font-size:0.72rem;">Brgy. 36-A</small>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-grow-1 py-2 px-2">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a href="index.php?controller=dashboard&action=index"
                    class="nav-link d-flex align-items-center rounded-3 <?= $title === 'Dashboard' ? 'active' : '' ?>">
                    <img src="public/assets/icons/dashboard.icon.png" alt="Dashboard" class="nav-icon">
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?controller=constituents"
                    class="nav-link d-flex align-items-center rounded-3 <?= $title === 'Constituents' ? 'active' : '' ?>">
                    <img src="public/assets/icons/constituents.icon.png" alt="Constituents" class="nav-icon">
                    <span>Constituents</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?controller=households"
                    class="nav-link d-flex align-items-center rounded-3 <?= $title === 'Households' ? 'active' : '' ?>">
                    <img src="public/assets/icons/household.icon.png" alt="Households" class="nav-icon">
                    <span>Households</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?controller=officials"
                    class="nav-link d-flex align-items-center rounded-3 <?= $title === 'Barangay Officials' ? 'active' : '' ?>">
                    <img src="public/assets/icons/officials.icon.png" alt="Officials" class="nav-icon">
                    <span>Barangay Officials</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?controller=home&action=forms"
                    class="nav-link d-flex align-items-center rounded-3 <?= $title === 'Forms' ? 'active' : '' ?>">
                    <img src="public/assets/icons/forms.icon.png" alt="Forms" class="nav-icon">
                    <span>Forms</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?controller=vehicles"
                    class="nav-link d-flex align-items-center rounded-3 <?= $title === 'Vehicles' ? 'active' : '' ?>">
                    <i class="fas fa-car nav-icon" style="font-size:1rem;width:21px;text-align:center;opacity:0.8;margin-right:12px;"></i>
                    <span>Vehicles</span>
                </a>
            </li>
        </ul>

        <!-- Additional Settings -->
        <div class="sidebar-section-label">Additional Settings</div>
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a href="#constituentRequestsMenu"
                    data-toggle="collapse"
                    role="button"
                    aria-expanded="<?= $isConstituentRequestsActive ? 'true' : 'false' ?>"
                    aria-controls="constituentRequestsMenu"
                    class="nav-link request-toggle d-flex align-items-center rounded-3 <?= $isConstituentRequestsActive ? 'active' : '' ?>">
                    <i class="fas fa-inbox nav-icon" style="font-size:1rem;width:20px;text-align:center;margin-right:12px;opacity:0.8;"></i>
                    <span class="request-label-wrap">
                        <span class="request-label">Constituent Requests</span>
                        <?php if ($pendingRequestsTotal > 0): ?>
                            <span class="request-dot" title="Pending constituent requests"></span>
                        <?php endif; ?>
                    </span>
                    <i class="fas fa-chevron-down" style="margin-left:8px;font-size:.7rem;flex-shrink:0;"></i>
                </a>

                <div class="collapse submenu-group <?= $isConstituentRequestsActive ? 'show' : '' ?>" id="constituentRequestsMenu">
                    <a href="index.php?controller=constituentRequests&action=profileRequests"
                        class="nav-sublink <?= ($title ?? '') === 'Profile Requests' ? 'active' : '' ?>">
                        <span>Profile Requests</span>
                        <?php if ($pendingProfileCount > 0): ?>
                            <span class="pending-badge-sm request-badge" style="margin-left:auto;background:#2563eb;"><?= $pendingProfileCount ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="index.php?controller=constituentRequests&action=documentRequests"
                        class="nav-sublink <?= ($title ?? '') === 'Document Requests' ? 'active' : '' ?>">
                        <span>Document Requests</span>
                        <?php if ($pendingDocumentCount > 0): ?>
                            <span class="pending-badge-sm request-badge" style="margin-left:auto;background:#2563eb;"><?= $pendingDocumentCount ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="index.php?controller=vehicles&action=vehicleRequests"
                        class="nav-sublink <?= ($title ?? '') === 'Vehicle Requests' ? 'active' : '' ?>">
                        <span>Vehicle Requests</span>
                        <?php if ($pendingVehicleRequestCount > 0): ?>
                            <span class="pending-badge-sm request-badge" style="margin-left:auto;background:#2563eb;"><?= $pendingVehicleRequestCount ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </li>

            <li class="nav-item">
                <a href="index.php?controller=users&tab=constituent" class="nav-link d-flex align-items-center rounded-3 <?= in_array($title, ['Manage Accounts', 'Manage Constituent Accounts', 'Archived Accounts']) ? 'active' : '' ?>">
                    <i class="fas fa-users-cog nav-icon" style="font-size:1rem;width:20px;text-align:center;margin-right:12px;opacity:0.8;"></i>
                    <span>Manage Accounts</span>
                    <?php if ($pendingCount > 0): ?>
                        <span class="pending-badge-sm" style="margin-left:auto;"><?= $pendingCount ?></span>
                    <?php endif; ?>
                </a>
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
                        <div class="account-brgy">Barangay 36-A</div>
                    </div>
                    <i class="fas fa-chevron-down account-caret ml-auto"></i>
                </button>

                <div class="account-menu" aria-labelledby="accountMenuBtn">
                    <a class="dropdown-item account-item" href="index.php?controller=users&action=edit&id=<?= (int)($_SESSION['user_id'] ?? 0) ?>">
                        <i class="fas fa-cog mr-2"></i>
                        Settings
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
    display: flex;
    flex-direction: column;
}

/* Keep expanding dropdown menus inside sidebar body instead of overlapping footer */
    .sidebar nav.flex-grow-1 {
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        padding-bottom: 0.5rem;
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
    padding: 0.8rem 1.15rem;
    margin: 0 0.35rem;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
    white-space: nowrap;
    font-size: 0.92rem;
}

.nav-link:hover {
    background-color: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
    border-left-color: #0d6efd;
    transform: none;
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
    width: 21px;
    height: 21px;
    margin-right: 12px;
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

/* Prevent this row from shifting outside the sidebar on hover */
.request-toggle:hover {
    transform: none;
}

.request-toggle {
    font-size: 0.86rem;
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

.request-badge {
    font-size: 0.66rem;
    line-height: 1;
    padding: 0.28rem 0.4rem;
    flex-shrink: 0;
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
    flex-shrink: 0;
    animation: requestDotPulse 1.4s ease-in-out infinite;
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

.nav-sublink {
    display: flex;
    align-items: center;
    margin: 0.2rem 0.35rem;
    padding: 0.45rem 0.65rem;
    border-radius: 0.5rem;
    color: #4b5563;
    font-size: 0.79rem;
    text-decoration: none;
    transition: all 0.2s ease;
    white-space: nowrap;
    overflow: hidden;
}

.submenu-group {
    margin: 0.2rem 0.6rem 0.35rem 2.25rem;
    padding: 0.25rem;
    border-radius: 0.65rem;
    background: rgba(148, 163, 184, 0.16);
    overflow: hidden;
}

#constituentRequestsMenu {
    position: static;
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

.sidebar-footer {
    flex-shrink: 0;
    overflow: visible;
    background: #f8fafc;
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
}

.account-username {
    font-size: 0.88rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1.15;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 120px;
}

.account-brgy {
    font-size: 0.72rem;
    color: #64748b;
    line-height: 1.1;
    margin-top: 2px;
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

@keyframes badgePulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

.pending-badge-sm {
    background: #f59e0b;
    color: #fff;
    font-size: 0.62rem;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    line-height: 18px;
    text-align: center;
    border-radius: 9px;
    padding: 0 5px;
    margin-left: auto;
    animation: badgePulse 2s ease-in-out infinite;
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
            // Closing — clean up both classes
            dropdown.classList.remove('open');
            dropdown.classList.remove('open-upward');
            button.setAttribute('aria-expanded', 'false');
        } else {
            // Opening — decide direction fresh each time
            dropdown.classList.remove('open-upward'); // reset first
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
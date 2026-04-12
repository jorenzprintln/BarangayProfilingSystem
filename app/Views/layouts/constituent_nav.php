<nav class="navbar navbar-expand-lg constituent-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php?controller=constituent">
            <img src="public/assets/imgs/brgy.logo.png" alt="Logo" class="navbar-logo mr-2">
            <span class="brand-text"><?= APP_NAME ?></span>
        </a>

        <div class="d-flex align-items-center d-lg-none gap-mobile">
            <!-- Mobile: show avatar + logout before hamburger -->
            <div class="nav-user-avatar">
                <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
            </div>
            <button type="button" class="btn btn-sm btn-mobile-logout ml-1" id="logoutBtnMobile">
                <i class="fas fa-sign-out-alt"></i>
            </button>
            <button class="navbar-toggler ml-1" type="button" data-toggle="collapse" data-target="#constituentNavbar"
                    aria-controls="constituentNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="constituentNavbar">
            <ul class="navbar-nav ml-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link <?= ($title ?? '') === 'My Dashboard' ? 'active' : '' ?>"
                       href="index.php?controller=constituent">
                        <i class="fas fa-home mr-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($title ?? '') === 'My Profile' ? 'active' : '' ?>"
                       href="index.php?controller=constituent&action=profile">
                        <i class="fas fa-user mr-1"></i> My Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($title ?? '') === 'My Requests' ? 'active' : '' ?>"
                       href="index.php?controller=constituent&action=myRequests">
                        <i class="fas fa-inbox mr-1"></i> My Requests
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($title ?? '') === 'Request Document' ? 'active' : '' ?>"
                       href="index.php?controller=constituent&action=requestDocument">
                        <i class="fas fa-file-alt mr-1"></i> Request Document
                    </a>
                </li>
                <!-- Desktop: user info -->
                <li class="nav-item ml-lg-3 d-none d-lg-flex align-items-center">
                    <div class="nav-user-info d-flex align-items-center">
                        <div class="nav-user-avatar">
                            <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                        </div>
                        <span class="nav-username ml-2"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
                        <button type="button" class="btn btn-sm btn-outline-danger ml-2" id="logoutBtn">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
/* ── Constituent Navbar ── */
.constituent-navbar {
    background: #ffffff;
    box-shadow: 0 2px 12px rgba(0,0,0,0.09);
    padding: 0.5rem 0;
    position: sticky;
    top: 0;
    z-index: 1040;
}

.navbar-logo {
    width: 36px;
    height: 36px;
    object-fit: contain;
    flex-shrink: 0;
}

.brand-text {
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    color: #1a202c;
    font-size: 0.95rem;
    letter-spacing: -0.01em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 180px;
}

.constituent-navbar .nav-link {
    color: #4a5568;
    font-weight: 600;
    font-size: 0.875rem;
    padding: 0.5rem 0.875rem;
    border-radius: 0.5rem;
    transition: color 0.2s, background 0.2s;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.constituent-navbar .nav-link:hover { color: #4361ee; background: #eef2ff; }
.constituent-navbar .nav-link.active { color: #4361ee; background: #eef2ff; }

.constituent-navbar .navbar-toggler {
    border: 1.5px solid #e2e8f0;
    padding: 0.38rem 0.6rem;
    color: #4a5568;
    font-size: 0.9rem;
    border-radius: 8px;
    background: none;
}

.constituent-navbar .navbar-toggler:focus { outline: none; box-shadow: none; }

.nav-user-info { gap: 0.5rem; }

.nav-user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4361ee, #3651d4);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 0.8rem;
    flex-shrink: 0;
}

.nav-username {
    font-weight: 600;
    color: #2d3748;
    font-size: 0.85rem;
}

.btn-mobile-logout {
    background: none;
    border: 1.5px solid #fca5a5;
    color: #ef4444;
    border-radius: 8px;
    padding: 0.3rem 0.55rem;
    font-size: 0.85rem;
    transition: background 0.2s, color 0.2s;
}

.btn-mobile-logout:hover { background: #fef2f2; color: #dc2626; }

.gap-mobile { gap: 0.25rem; }

.constituent-main-content {
    min-height: calc(100vh - 58px);
    background: #f4f6fb;
}

/* ── Collapsed mobile menu ── */
@media (max-width: 991.98px) {
    .brand-text { max-width: 140px; font-size: 0.88rem; }

    .constituent-navbar .navbar-collapse {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        padding: 0.75rem 1rem 1rem;
        margin-top: 0.75rem;
        border: 1px solid #e4e7ec;
    }

    .constituent-navbar .nav-item { margin-bottom: 0.15rem; }

    .constituent-navbar .nav-link {
        font-size: 0.92rem;
        padding: 0.6rem 0.875rem;
    }
}

@media (max-width: 480px) {
    .brand-text { max-width: 110px; font-size: 0.82rem; }
    .navbar-logo { width: 30px; height: 30px; }
}
</style>

<script>
// Wire up mobile logout button to same modal as desktop button
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        var mobileBtn = document.getElementById('logoutBtnMobile');
        if (mobileBtn) {
            mobileBtn.addEventListener('click', function() {
                var modal = document.getElementById('logoutModal');
                if (modal) { $(modal).modal('show'); }
            });
        }
    });
})();
</script>

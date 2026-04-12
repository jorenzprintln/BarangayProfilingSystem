<?php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - <?= $title ?? 'Welcome' ?></title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="public/assets/imgs/brgyicon.png">
    <link rel="stylesheet" href="public/assets/css/custom.css">
    <link rel="stylesheet" href="vendor/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <script src="vendor/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <style>
        @font-face {
            font-family: 'Montserrat';
            src: url('public/assets/fonts/Montserrat-Regular.ttf') format('truetype');
        }

        body {
            font-family: 'Montserrat', sans-serif;
            overflow-x: hidden;
            background-color: #f5f6fa;
        }

        .sidebar {
            height: 100vh;
            overflow-y: auto;
            position: fixed;
            z-index: 1000;
            transition: all 0.3s ease;
            top: 0;
            left: 0;
        }

        .main-content {
            margin-left: 16.6667%;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            padding: 20px;
            width: 83.3333%;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: 250px !important;
                max-width: 86vw;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding-top: 62px;
            }

            .sidebar-toggle {
                display: flex !important;
            }

            body.sidebar-open {
                overflow: hidden;
            }

            .sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.45);
                z-index: 990;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.2s ease;
            }

            body.sidebar-open .sidebar-backdrop {
                opacity: 1;
                visibility: visible;
            }
        }

        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 14px;
            left: 14px;
            z-index: 1050;
            border-radius: 10px;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.32);
            width: 40px;
            height: 40px;
            padding: 0;
            display: none;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 991.98px) {
            body.sidebar-open .sidebar-toggle {
                left: calc(min(250px, 86vw) - 46px);
                top: 12px;
                background-color: transparent !important;
                border: none !important;
                box-shadow: none !important;
                color: #1f2937;
            }

            body.sidebar-open .sidebar-toggle:hover {
                background-color: transparent !important;
                color: #0f172a;
            }
        }

        @media (max-width: 575.98px) {
            .main-content {
                padding: 12px;
                padding-top: 58px;
            }

            .sidebar-toggle {
                top: 10px;
                left: 10px;
            }

            body.sidebar-open .sidebar-toggle {
                left: calc(min(250px, 86vw) - 44px);
                top: 10px;
            }
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .card {
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }


        /* ... your existing sidebar/main-content styles unchanged ... */

        /* ── Shared Page Styles ── */
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3651d4;
            --danger-color: #ef476f;
            --success-color: #06d6a0;
            --warning-color: #ffd166;
            --card-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.08);
            --card-shadow-hover: 0 0.5rem 1rem rgba(0, 0, 0, 0.12);
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3651d4 100%);
            color: white;
            padding: 2rem;
            margin: 0 0 2rem 0;
            border-radius: 0.5rem;
            box-shadow: var(--card-shadow);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: white;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.5rem;
            color: #4a5568;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: var(--card-shadow);
            font-family: 'Montserrat', sans-serif;
        }

        .btn-back:hover {
            background: #2d3748;
            color: white;
            border-color: #2d3748;
            text-decoration: none;
            transform: translateX(-2px);
        }

        .action-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .info-card {
            background: white;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            border: none;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .info-card:hover { box-shadow: var(--card-shadow-hover); }

        .info-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 1.75rem;
            border-bottom: 2px solid #e9ecef;
            background: #fafbfc;
        }

        .info-card-header-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .info-card-header h5 {
            margin: 0;
            color: #2d3748;
            font-weight: 700;
            font-size: 1rem;
        }

        .info-card-body { padding: 1.5rem 1.75rem; }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .modern-table thead th {
            background: #f8f9fa;
            color: #4a5568;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.875rem 1rem;
            border-bottom: 2px solid #e9ecef;
            white-space: nowrap;
        }

        .modern-table tbody td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #f0f2f5;
            color: #4a5568;
            vertical-align: middle;
        }

        .modern-table tbody tr:last-child td { border-bottom: none; }
        .modern-table tbody tr:hover td { background: #f8f9ff; }

        .modern-table .empty-row td {
            text-align: center;
            color: #a0aec0;
            padding: 2.5rem;
            font-style: italic;
        }

        .btn-add {
            background: linear-gradient(135deg, var(--primary-color), #3651d4);
            color: white;
            border: none;
            padding: 0.45rem 1.25rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            box-shadow: 0 2px 4px rgba(67,97,238,0.3);
            white-space: nowrap;
            flex-shrink: 0;
            cursor: pointer;
        }

        .btn-add:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(67,97,238,0.4);
            color: white;
            text-decoration: none;
        }

        .btn-view {
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
            border: none;
            padding: 0.375rem 0.875rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            white-space: nowrap;
        }

        .btn-view:hover {
            background: var(--primary-color);
            color: white;
            text-decoration: none;
        }

        .search-input {
            border: 1.5px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.45rem 0.875rem;
            font-size: 0.8rem;
            transition: all 0.2s;
            height: auto;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.18rem rgba(67,97,238,0.15);
            outline: none;
        }

        .badge-head {
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.25rem 0.6rem;
            border-radius: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-member {
            background: #f0f2f5;
            color: #718096;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
            border-radius: 1rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
        }

        .info-item { display: flex; flex-direction: column; gap: 0.25rem; }

        .info-item .info-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #a0aec0;
        }

        .info-item .info-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: #2d3748;
        }

        .info-item .info-value.not-specified {
            color: #cbd5e0;
            font-style: italic;
            font-weight: 400;
        }

        .info-divider {
            border: none;
            border-top: 1px dashed #e2e8f0;
            margin: 1.25rem 0;
        }

        .family-members-list { list-style: none; padding: 0; margin: 0; }

        .family-members-list li {
            font-size: 0.8rem;
            color: #718096;
            padding: 0.1rem 0;
        }

        .family-members-list li::before { content: '• '; color: #cbd5e0; }

        input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary-color);
            cursor: pointer;
        }
        .btn-delete {
            background: rgba(239, 71, 111, 0.1);
            color: var(--danger-color);
            border: none;
            padding: 0.375rem 0.875rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            white-space: nowrap;
            cursor: pointer;
    }

    .btn-delete:hover {
        background: var(--danger-color);
        color: white;
        text-decoration: none;
    }
    #logoutModal .modal-dialog {
        max-width: 360px;
        width: calc(100% - 2rem);
        margin: 1rem auto;
    }

    .logout-modal-content {
        border: none;
        border-radius: 24px;
        padding: 2rem 1.75rem 1.75rem;
        text-align: center;
        position: relative;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
        overflow: hidden;
        background: #fff;
    }

    .logout-modal-content::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ef4444, #f97316);
        border-radius: 24px 24px 0 0;
    }

    /* Close button */
    .logout-modal-close {
        position: absolute;
        top: 14px; right: 14px;
        width: 32px; height: 32px;
        border-radius: 50%;
        border: none;
        background: #f1f5f9;
        color: #94a3b8;
        font-size: 0.8rem;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        line-height: 1;
    }

    .logout-modal-close:hover {
        background: #e2e8f0;
        color: #475569;
    }

    /* Icon */
    .logout-modal-icon {
        position: relative;
        width: 72px; height: 72px;
        margin: 0 auto 1.25rem;
        display: flex; align-items: center; justify-content: center;
    }

    .logout-icon-ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: linear-gradient(135deg, #fef2f2, #fff7ed);
        border: 2px solid #fecaca;
        animation: logoutRingPulse 2s ease-in-out infinite;
    }

    .logout-brgy-logo {
        position: relative;
        width: 42px;
        height: 42px;
        object-fit: contain;
        z-index: 1;
        filter: drop-shadow(0 2px 6px rgba(0,0,0,0.12));
    }

    @keyframes logoutRingPulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.06); opacity: 0.85; }
    }

    /* Text */
    .logout-modal-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0.4rem;
        letter-spacing: -0.02em;
    }

    .logout-modal-sub {
        font-size: 0.82rem;
        color: #64748b;
        line-height: 1.55;
        margin: 0 0 1.5rem;
    }

    /* Actions */
    .logout-modal-actions {
        display: flex;
        gap: 0.75rem;
        align-items: stretch;
    }

    .logout-btn-cancel {
        flex: 1;
        padding: 0.7rem 1rem;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        font-size: 0.875rem;
        font-weight: 600;
        font-family: 'Montserrat', sans-serif;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .logout-btn-cancel:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #1e293b;
    }

    .logout-btn-confirm {
        width: 100%;
        padding: 0.7rem 1rem;
        border-radius: 12px;
        border: none;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        font-size: 0.875rem;
        font-weight: 700;
        font-family: 'Montserrat', sans-serif;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
    }

    .logout-btn-confirm:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.45);
        transform: translateY(-1px);
    }

    /* Mobile */
    @media (max-width: 480px) {
        #logoutModal .modal-dialog {
            width: calc(100% - 1.5rem);
            margin: auto;
        }

        .logout-modal-content {
            padding: 1.75rem 1.25rem 1.5rem;
            border-radius: 20px;
        }

        .logout-modal-title { font-size: 1.05rem; }
        .logout-modal-sub { font-size: 0.8rem; }
    }
    </style>
</head>

<body>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <?php if (Session::isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin'): ?>
                <button class="btn btn-primary sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="col-lg-2 col-md-3 bg-light p-0 sidebar" id="sidebar">
                    <?php require_once 'sidebar.php'; ?>
                </div>

                <div class="col-lg-10 col-md-9 main-content" id="mainContent">
                    <div class="container-fluid px-4 py-3">
                        <?= $content ?>
                    </div>
                </div>
            <?php elseif (Session::isLoggedIn() && ($_SESSION['role'] ?? '') === 'constituent'): ?>
                <button class="btn btn-primary sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="col-lg-2 col-md-3 bg-light p-0 sidebar" id="sidebar">
                    <?php require_once VIEW_PATH . 'layouts/constituent_sidebar.php'; ?>
                </div>

                <div class="col-lg-10 col-md-9 main-content" id="mainContent">
                    <div class="container-fluid px-4 py-3">
                        <?= $content ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-12">
                    <div class="container-fluid px-4 py-3">
                        <?= $content ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Logout Modal - Placed at root level, outside all containers -->
    <?php if (Session::isLoggedIn()): ?>
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content logout-modal-content">
                <!-- Icon -->
                <div class="logout-modal-icon">
                    <div class="logout-icon-ring"></div>
                    <img src="public/assets/imgs/brgy.logo.png" alt="Barangay Logo" class="logout-brgy-logo">
                </div>

                <!-- Text -->
                <div class="logout-modal-body">
                    <h5 class="logout-modal-title">Leaving so soon?</h5>
                    <p class="logout-modal-sub">You'll need to sign in again to access your account.</p>
                </div>

                <!-- Actions -->
                <div class="logout-modal-actions">
                    <button type="button" class="logout-btn-cancel" data-dismiss="modal">
                        Stay
                    </button>
                    <form action="index.php?controller=auth&action=logout" method="post" style="flex:1;">
                        <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                        <button type="submit" class="logout-btn-confirm">
                            <i class="fas fa-sign-out-alt"></i> Log out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?> 

    <script>
    $(document).ready(function() {
        function syncSidebarToggleIcon() {
            var $icon = $('#sidebarToggle').find('i').first();
            if (!$icon.length) return;
            if ($('body').hasClass('sidebar-open')) {
                $icon.removeClass('fa-bars').addClass('fa-times');
            } else {
                $icon.removeClass('fa-times').addClass('fa-bars');
            }
        }

        if (typeof disableBootstrapSelect === 'undefined' || !disableBootstrapSelect) {
            $('.selectpicker').not('.role-select').not('[data-no-selectpicker]').selectpicker();
        }

        $('#sidebarToggle').click(function() {
            $('#sidebar').toggleClass('show');
            $('body').toggleClass('sidebar-open');
            syncSidebarToggleIcon();
        });

        $('#sidebarBackdrop').click(function() {
            $('#sidebar').removeClass('show');
            $('body').removeClass('sidebar-open');
            syncSidebarToggleIcon();
        });

        $(document).click(function(e) {
            if ($(window).width() < 992) {
                if (!$(e.target).closest('#sidebar, #sidebarToggle').length) {
                    $('#sidebar').removeClass('show');
                    $('body').removeClass('sidebar-open');
                    syncSidebarToggleIcon();
                }
            }
        });

        // Close sidebar after selecting a nav item on mobile
        $('#sidebar').on('click', 'a.nav-link', function() {
            if ($(window).width() < 992) {
                $('#sidebar').removeClass('show');
                $('body').removeClass('sidebar-open');
                syncSidebarToggleIcon();
            }
        });

        $(window).on('resize', function() {
            if ($(window).width() >= 992) {
                $('#sidebar').removeClass('show');
                $('body').removeClass('sidebar-open');
            }
            syncSidebarToggleIcon();
        });

        $('#sidebar').click(function(e) {
            // Allow Bootstrap data-toggle events (collapse, modal) to propagate
            if (!$(e.target).closest('[data-toggle]').length) {
                e.stopPropagation();
            }
        });

        // Logout button handler
        $('#logoutBtn').click(function() {
            $('#logoutModal').modal('show');
        });

        syncSidebarToggleIcon();
    });

    function adjustMainContentHeight() {
        const windowHeight = $(window).height();
        $('#mainContent').css('min-height', windowHeight + 'px');
    }

    $(window).on('load resize', adjustMainContentHeight);
    adjustMainContentHeight();

    </script>
</body>

</html>
<?php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - <?= $title ?? 'Welcome' ?></title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
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
            overflow-x: hidden; /* Prevent horizontal scrolling */
        }

        .sidebar {
            height: 100vh;
            overflow-y: auto;
            position: fixed;
            z-index: 1000; /* Ensure sidebar stays above content */
            transition: all 0.3s ease;
        }

        .main-content {
            margin-left: 16.6667%;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            padding: 20px;
            width: 83.3333%;
        }

        /* Mobile sidebar handling */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: 250px !important;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .sidebar-toggle {
                display: block !important;
            }
        }

        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1050;
        }

        /* Better table responsiveness */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Card improvements */
        .card {
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <?php if (Session::isLoggedIn()): ?>
                <!-- Sidebar Toggle Button (Mobile) -->
                <button class="btn btn-primary sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Sidebar -->
                <div class="col-lg-2 col-md-3 bg-light p-0 sidebar" id="sidebar">
                    <?php require_once 'sidebar.php'; ?>
                </div>

                <!-- Main Content -->
                <div class="col-lg-10 col-md-9 main-content" id="mainContent">
                    <div class="container-fluid px-4 py-3">
                        <?= $content ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Full width when not logged in -->
                <div class="col-12">
                    <div class="container-fluid px-4 py-3">
                        <?= $content ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();

            // Mobile sidebar toggle
            $('#sidebarToggle').click(function() {
                $('#sidebar').toggleClass('show');
                $('body').toggleClass('sidebar-open');
            });

            // Close sidebar when clicking outside on mobile
            $(document).click(function(e) {
                if ($(window).width() < 992) {
                    if (!$(e.target).closest('#sidebar, #sidebarToggle').length) {
                        $('#sidebar').removeClass('show');
                        $('body').removeClass('sidebar-open');
                    }
                }
            });

            // Prevent closing when clicking inside sidebar
            $('#sidebar').click(function(e) {
                e.stopPropagation();
            });
        });

        // Adjust main content height dynamically
        function adjustMainContentHeight() {
            const windowHeight = $(window).height();
            $('#mainContent').css('min-height', windowHeight + 'px');
        }

        // Run on load and resize
        $(window).on('load resize', adjustMainContentHeight);
        adjustMainContentHeight();
    </script>
</body>

</html>
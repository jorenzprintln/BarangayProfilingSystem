<?php

?>

<div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="height: 100vh;">
    <a href="index.php"
        class="d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none text-dark">
        <span class="fs-4"><?= APP_NAME ?></span>
    </a>

    <div class="d-flex justify-content-center align-items-center mb-3">
        <img src="public/assets/imgs/brgy.logo.png" alt="Icon" style="width: 32px; height: 32px; margin-right: 8px;">
        <h5 class="text-center mb-0">Brgy. 36-A</h5>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="index.php?controller=home"
                class="nav-link d-flex align-items-center <?= $title === 'Dashboard' ? 'active' : 'link-dark text-dark' ?>">
                <img src="public/assets/icons/dashboard.icon.png" alt="Icon"
                    style="width: 24px; height: 24px; margin-right: 8px;">
                Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="index.php?controller=constituents"
                class="nav-link d-flex align-items-center <?= $title === 'Constituents' ? 'active' : 'link-dark text-dark' ?>">
                <img src="public/assets/icons/constituents.icon.png" alt="Icon"
                    style="width: 24px; height: 24px; margin-right: 8px;">
                Constituents
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="index.php?controller=households"
                class="nav-link d-flex align-items-center <?= $title === 'Households' ? 'active' : 'link-dark text-dark' ?>">
                <img src="public/assets/icons/household.icon.png" alt="Icon"
                    style="width: 24px; height: 24px; margin-right: 8px;">
                Households
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="index.php?controller=officials"
                class="nav-link d-flex align-items-center <?= $title === 'Barangay Officials' ? 'active' : 'link-dark text-dark' ?>">
                <img src="public/assets/icons/officials.icon.png" alt="Icon"
                    style="width: 24px; height: 24px; margin-right: 8px;">
                Barangay Officials
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="index.php?controller=home&action=forms"
                class="nav-link d-flex align-items-center <?= $title === 'Forms' ? 'active' : 'link-dark text-dark' ?>">
                <img src="public/assets/icons/forms.icon.png" alt="Icon"
                    style="width: 24px; height: 24px; margin-right: 8px;">
                Forms
            </a>
        </li>
        <!-- <li class="nav-item mb-2">
            <a href="index.php?controller=officials"
                class="nav-link d-flex align-items-center <?= $title === 'Transactions' ? 'active' : 'link-dark text-dark' ?>">
                <img src="public/assets/icons/transactions.icon.png" alt="Icon"
                    style="width: 24px; height: 24px; margin-right: 8px;">
                Transactions
            </a>
        </li>
        <li class="nav-item">
            <a href="index.php?controller=officials"
                class="nav-link d-flex align-items-center <?= $title === 'Archives' ? 'active' : 'link-dark text-dark' ?>">
                <img src="public/assets/icons/archive.icon.png" alt="Icon"
                    style="width: 24px; height: 24px; margin-right: 8px;">
                Archive
            </a>
        </li> -->
    </ul>
    <hr>
    <div class="d-flex flex-column justify-content-center align-items-center">
        <?php if (isset($_SESSION['username'])): ?>
            <p>You are logged in as <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <form action="index.php?controller=auth&action=logout" method="post">
            <button type="submit" class="btn btn-danger d-flex justify-content-center align-items-center"> <img
                    src="public/assets/icons/logout.icon.png" alt="Logout Icon"
                    style="width: 24px; height: 24px; margin-right: 8px;">
                Logout</button>
        </form>
    </div>
</div>
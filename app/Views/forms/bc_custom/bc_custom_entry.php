<?php
$content = ob_start();
?>

<div class="container-fluid px-4 mt-4">
    <div class="d-flex align-items-center mb-3">
        <!-- Header Container -->
        <a href="index.php?controller=home&action=forms" class="mr-2">
            <img src="public/assets/icons/back.icon.png" alt="Back" style="width: 32px; height: 32px;">
        </a>
        <h3 class="font-weight-bold mb-0">Download Custom Barangay Certificate</h3>
    </div>

    <div class="shadow">
        <div class="card-body">
            <form action="index.php?controller=forms&action=processBcCustomEntry" target="_blank" method="POST">
                <!-- Requesting Party -->
                <small class="text-muted">The entry for requesting party and purpose is required for transcations tracking.</small>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="requesting_party" class="form-label">Requesting Party:</label>
                        <input type="text" class="form-control" id="requesting_party" name="requesting_party" required>
                    </div>
                </div>

                <!-- purpose -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="purpose" class="form-label">Purpose:</label>
                        <textarea class="form-control" id="purpose" name="purpose" rows="3" required></textarea>
                    </div>
                </div>

                <!-- submit button -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-lg">Generate Certificate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
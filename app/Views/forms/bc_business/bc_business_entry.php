<?php
$content = ob_start();
?>
<div class="container-fluid px-4 mt-4">
    <div class="d-flex align-items-center mb-3">
        <!-- Header Container -->
        <a href="index.php?controller=home&action=forms" class="mr-2">
            <img src="public/assets/icons/back.icon.png" alt="Back" style="width: 32px; height: 32px;">
        </a>
        <h3 class="font-weight-bold mb-0">Barangay Certificate for Business</h3>
    </div>
    <form id="coaForm" method="post" action="index.php?controller=forms&action=processBcbEntry" class="needs-validation"
        novalidate>
        <div class="container-fluid px-4 mt-4">
            <div class="mb-3">
                <label for="bname" class="form-label font-weight-bold">Name of Business:</label>
                <input type="text" id="bname" name="bname" class="form-control" required
                    placeholder="Aling Puring Store">
                <div class="invalid-feedback">Please enter a name of business.</div>
            </div>
            <div class="mb-3">
                <label for="tob" class="form-label font-weight-bold">Type of Business:</label>
                <input type="text" id="tob" name="tob" class="form-control" required
                    placeholder="Sari-sari Store, Restaurant, etc.">
                <div class="invalid-feedback">Please enter type of business.</div>
            </div>
            <div class="mb-3">
                <label for="bo" class="form-label font-weight-bold">Owner of Business:</label>
                <input type="text" id="bo" name="bo" class="form-control" required placeholder="MR. JUAN DELA CRUZ">
                <div class="invalid-feedback">Please enter a business owner.</div>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label font-weight-bold">Business Location (Block & Lot):</label>
                <input type="text" id="location" name="location" class="form-control" required
                    placeholder="Block 4, Lot 1">
                <div class="invalid-feedback">Please enter a location.</div>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg">SUBMIT</button>
        </div>
    </form>
</div>

<script>
    // Form validation script
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('coaForm');

        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            } else {
                // If form is valid, show confirmation
                if (!confirm('Are you sure you want to submit this form?')) {
                    event.preventDefault();
                } else {
                    // Set target to _blank only if confirmed
                    form.setAttribute('target', '_blank');
                }
            }

            form.classList.add('was-validated');
        });
    });
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
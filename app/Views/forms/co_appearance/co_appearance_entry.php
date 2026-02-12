<?php
$content = ob_start();
?>
<div class="container-fluid px-4 mt-4">
    <div class="d-flex align-items-center mb-3">
        <!-- Header Container -->
        <a href="index.php?controller=home&action=forms" class="mr-2">
            <img src="public/assets/icons/back.icon.png" alt="Back" style="width: 32px; height: 32px;">
        </a>
        <h3 class="font-weight-bold mb-0">Certificate of Appearance Data</h3>
    </div>
    <form id="coaForm" method="post" action="index.php?controller=forms&action=processCoaEntry" class="needs-validation"
        novalidate>
        <div class="container-fluid px-4 mt-4">
            <div class="mb-3">
                <label for="name" class="form-label font-weight-bold">Name:</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="MR. JUAN DELA CRUZ">
                <div class="invalid-feedback">Please enter a name.</div>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label font-weight-bold">From:</label>
                <input type="text" id="location" name="location" class="form-control" required
                    placeholder="CITY MAYOR OFFICE, BARANGAY HALL, etc.">
                <div class="invalid-feedback">Please enter a location.</div>
            </div>
            <div class="mb-3">
                <label for="reason" class="form-label font-weight-bold">Reason:</label>
                <textarea id="reason" name="reason" class="form-control" rows="3" required
                    placeholder="INSPECTION"></textarea>
                <div class="invalid-feedback">Please enter a reason.</div>
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
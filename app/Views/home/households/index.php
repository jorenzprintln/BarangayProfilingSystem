<?php
$content = ob_start();
?>

<div class="container-fluid px-4 mt-3">

    <!-- Flash Messages -->
    <?php if (Session::hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= Session::getFlash('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (Session::hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= Session::getFlash('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div>
        <h3 class="font-weight-bold"><?= $title ?></h3>
        <div class="d-flex justify-content-end align-items-center mb-3">
            <div class="w-25 mr-3">
                <input type="text" id="search-input" class="form-control" placeholder="Search households...">
            </div>
            <div>
                <button type="button" class="btn btn-primary" data-toggle="modal"
                    data-target="#createHouseholdModal">Add Household</button>
            </div>
        </div>
    </div>

    <div style="height: 480px; overflow-y: auto;" id="households-table">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Household Number</th>
                    <th>Head of Household</th>
                    <th>No. of Families</th>
                    <th>No. of Members</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($households)): ?>
                    <?php foreach ($households as $household): ?>
                        <tr>
                            <td><?= htmlspecialchars($household['household_number'] ?? '') ?></td>
                            <td><?= htmlspecialchars($household['full_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($household['total_families'] ?? '0') ?></td>
                            <td><?= htmlspecialchars($household['total_members'] ?? '0') ?></td>
                            <td>
                                <a href="index.php?controller=households&action=view&household_id=<?= htmlspecialchars($household['id'] ?? '') ?>"
                                    class="btn btn-primary btn-sm">View</a>
                                <?php if ((int) ($household['total_members'] ?? 0) === 0): ?>
                                    <a href="javascript:void(0)"
                                        onclick="confirmDelete(<?= htmlspecialchars($household['id'] ?? '') ?>)"
                                        class="btn btn-danger btn-sm ml-2">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No households found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog"
        aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this household? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>
</div>

            <!-- Create Household Modal -->
            <div class="modal fade" id="createHouseholdModal" tabindex="-1" role="dialog"
                aria-labelledby="createHouseholdModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createHouseholdModalLabel">Create Household</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php if (isset($formSubmitted) && isset($error)): ?>
                                <div class="alert alert-danger">
                                    <?= htmlspecialchars($error) ?>
                                </div>
                <?php endif; ?>

                <form id="createHouseholdForm" action="index.php?controller=households&action=store" method="POST">
                    <div class="form-group">
                        <label for="household_number">Household Number*</label>
                        <input type="text" class="form-control" id="household_number" name="household_number" required
                            value="<?= htmlspecialchars($formData['household_number'] ?? '') ?>" maxlength="24">
                        <small id="household_number_error" class="text-danger d-none">Household number must contain numbers only and not exceed 24 digits.</small>
                    </div>

                    <!-- Hidden location fields -->
                    <input type="hidden" id="region" name="region" value="REGION VIII" required>
                    <input type="hidden" id="province" name="province" value="LEYTE" required>
                    <input type="hidden" id="city_municipality" name="city_municipality" value="TACLOBAN CITY" required>
                    <input type="hidden" id="zip_code" name="zip_code"
                        value="<?= htmlspecialchars($formData['zip_code'] ?? '6500') ?>" required>
                    <input type="hidden" id="barangay_code" name="barangay_code"
                        value="<?= isset($formData['barangay_code']) ? htmlspecialchars($formData['barangay_code']) : '36-A' ?>"
                        required>
                    <input type="hidden" id="barangay_name" name="barangay_name"
                        value="<?= htmlspecialchars($formData['barangay_name'] ?? 'IMELDA VILLAGE') ?>" required>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="zone">Purok</label>
                                <input type="text" class="form-control" id="zone" name="zone"
                                    value="<?= htmlspecialchars($formData['purok'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="street_name">Street Name</label>
                                <input type="text" class="form-control" id="street_name" name="street_name"
                                    value="<?= htmlspecialchars($formData['street_name'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="block_no">Block No</label>
                                <input type="text" class="form-control" id="block_no" name="block_no"
                                    value="<?= htmlspecialchars($formData['block_number'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="lot_no">Lot No</label>
                                <input type="text" class="form-control" id="lot_no" name="lot_no"
                                    value="<?= htmlspecialchars($formData['lot_number'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="house_bldg_no">House No</label>
                                <input type="text" class="form-control" id="house_bldg_no" name="house_bldg_no"
                                    value="<?= htmlspecialchars($formData['house_building_number'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="unit_no">Unit No</label>
                                <input type="text" class="form-control" id="unit_no" name="unit_no"
                                    value="<?= htmlspecialchars($formData['unit_number'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="submitHouseholdForm">Create Household</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search-input');
        const submitHouseholdFormBtn = document.getElementById('submitHouseholdForm');
        const householdNumberInput = document.getElementById('household_number');
        const householdNumberError = document.getElementById('household_number_error');

        // Validate household number in real-time
        if (householdNumberInput) {
            // Validation function
            function validateHouseholdNumber() {
                const value = householdNumberInput.value;
                const isValid = /^\d*$/.test(value) && value.length <= 24 && value.length > 0;
                
                // Update UI based on validation
                if (!isValid) {
                    householdNumberInput.classList.add('is-invalid');
                    householdNumberError.classList.remove('d-none');
                    if (submitHouseholdFormBtn) {
                        submitHouseholdFormBtn.disabled = true;
                    }
                } else {
                    householdNumberInput.classList.remove('is-invalid');
                    householdNumberInput.classList.add('is-valid');
                    householdNumberError.classList.add('d-none');
                    if (submitHouseholdFormBtn) {
                        submitHouseholdFormBtn.disabled = false;
                    }
                }
                
                return isValid;
            }
            
            // Run validation on input
            householdNumberInput.addEventListener('input', validateHouseholdNumber);
            
            // Initial validation on page load
            validateHouseholdNumber();
        }

        // Check for errors after form submission
        <?php if (isset($formSubmitted)): ?>
            <?php if (isset($error)): ?>
                $('#createHouseholdModal').modal('show');
            <?php endif; ?>
        <?php endif; ?>

        // Check if the URL has the open_modal parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('open_modal')) {
            $('#createHouseholdModal').modal('show');
        }

        // Submit form when button is clicked
        if (submitHouseholdFormBtn) {
            submitHouseholdFormBtn.addEventListener('click', function () {
                // Validate before submission
                if (householdNumberInput && validateHouseholdNumber()) {
                    document.getElementById('createHouseholdForm').submit();
                }
            });
        }

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('#households-table tbody tr');

            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();

                // Show row if it contains the search term, hide otherwise
                row.style.display = rowText.includes(searchTerm) ? '' : 'none';
            });
        }

        // Add event listener
        searchInput.addEventListener('input', filterTable);

        // Auto-dismiss flash messages after 5 seconds
        const flashMessages = document.querySelectorAll('.alert');
        if (flashMessages.length > 0) {
            setTimeout(function () {
                flashMessages.forEach(function (message) {
                    // Use Bootstrap's fade functionality if available
                    if (message.classList.contains('fade')) {
                        message.classList.remove('show');
                        // Remove element after fade completes
                        setTimeout(function () {
                            if (message.parentNode) {
                                message.parentNode.removeChild(message);
                            }
                        }, 150);
                    } else {
                        // Direct removal if no fade support
                        if (message.parentNode) {
                            message.parentNode.removeChild(message);
                        }
                    }
                });
            }, 5000); // 5 seconds
        }
    });

    function confirmDelete(householdId) {
        // Set the delete button's href
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        confirmDeleteBtn.href = 'index.php?controller=households&action=delete&household_id=' + householdId;

        // Show the modal
        $('#deleteConfirmModal').modal('show');
    }
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
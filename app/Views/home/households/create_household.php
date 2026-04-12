<?php
$content = ob_start();
?>
<div class="container-fluid px-4 mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="shadow-sm border">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <a href="index.php?controller=households&action=index" class="mr-2">
                            <img src="public/assets/icons/back.icon.png" alt="Back" style="width: 32px; height: 32px;">
                        </a>
                        <h3 class="font-weight-bold mb-0">Create Household</h3>
                    </div>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="index.php?controller=households&action=store" method="POST">
                        <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                        <div class="form-group">
                            <label for="household_number">Household Number</label>
                            <input type="text" class="form-control" id="household_number" name="household_number"
                                value="<?= htmlspecialchars($formData['household_number'] ?? '') ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="region">Region</label>
                                    <select class="form-control" id="region" name="region" required>
                                        <option value="REGION VIII" <?= isset($formData['region']) && $formData['region'] === 'REGION VIII' ? 'selected' : '' ?>>REGION VIII</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="province">Province</label>
                                    <select class="form-control" id="province" name="province" required>
                                        <option value="LEYTE" <?= isset($formData['province']) && $formData['province'] === 'LEYTE' ? 'selected' : '' ?>>LEYTE</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="city_municipality">City/Municipality</label>
                                    <select class="form-control" id="city_municipality" name="city_municipality" required>
                                        <option value="TACLOBAN CITY" <?= isset($formData['city_municipality']) && $formData['city_municipality'] === 'TACLOBAN CITY' ? 'selected' : '' ?>>TACLOBAN CITY</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="zip_code">Zip Code</label>
                                    <input type="text" class="form-control" id="zip_code" name="zip_code"
                                        value="<?= htmlspecialchars($formData['zip_code'] ?? '6500') ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="barangay_code">Barangay Code</label>
                                    <select class="form-control" id="barangay_code" name="barangay_code" required>
                                        <option value="36-A" <?= isset($formData['barangay_code']) && $formData['barangay_code'] === '36-A' ? 'selected' : '' ?>>36-A</option>
                                        <option value="36-B" <?= isset($formData['barangay_code']) && $formData['barangay_code'] === '36-B' ? 'selected' : '' ?>>36-B</option>
                                        <option value="37" <?= isset($formData['barangay_code']) && $formData['barangay_code'] === '37' ? 'selected' : '' ?>>37</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="barangay_name">Barangay Name</label>
                                    <input type="text" class="form-control" id="barangay_name" name="barangay_name"
                                        value="<?= htmlspecialchars($formData['barangay_name'] ?? 'IMELDA VILLAGE') ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="zone">Purok</label>
                                    <input type="text" class="form-control" id="zone" name="zone"
                                        value="<?= htmlspecialchars($formData['zone'] ?? '') ?>">
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
                                        value="<?= htmlspecialchars($formData['block_no'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="lot_no">Lot No</label>
                                    <input type="text" class="form-control" id="lot_no" name="lot_no"
                                        value="<?= htmlspecialchars($formData['lot_no'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="house_bldg_no">House No</label>
                                    <input type="text" class="form-control" id="house_bldg_no" name="house_bldg_no"
                                        value="<?= htmlspecialchars($formData['house_bldg_no'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="unit_no">Unit No</label>
                                    <input type="text" class="form-control" id="unit_no" name="unit_no"
                                        value="<?= htmlspecialchars($formData['unit_no'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mt-4">Create Household</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
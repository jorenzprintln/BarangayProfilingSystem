<?php
ob_start();

$old   = $old   ?? [];
$title = $title ?? 'Request Vehicle Registration';

$vehicleTypes = [
    'Sedan', 'SUV', 'Van / Minivan', 'Pickup Truck', 'Motorcycle',
    'Tricycle', 'Jeepney', 'Bus', 'Truck', 'E-Bike', 'Other',
];

$fuelTypes = [
    'Gasoline (Unleaded)', 'Gasoline (Premium)', 'Diesel',
    'LPG', 'Electric', 'Hybrid', 'Other',
];

$transmissionTypes = ['Automatic', 'Manual', 'CVT', 'Semi-Automatic'];

$v = function(string $key, $default = '') use ($old) {
    return htmlspecialchars($old[$key] ?? $default, ENT_QUOTES, 'UTF-8');
};
?>

<link rel="stylesheet" href="public/assets/css/constituents_index.css">
<link rel="stylesheet" href="public/assets/css/bc_general_entry.css">

<div class="container-fluid px-4 mt-3">
    <div class="content-wrapper">

        <?php if (Session::hasFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> <?= Session::getFlash('error') ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Back Button -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.5rem;">
            <a href="index.php?controller=constituent" class="btn-back">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
                </svg>
                Back to Dashboard
            </a>
            <a href="index.php?controller=constituent&action=myVehicles" class="btn-view-vehicles">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                View My Registered Vehicles
            </a>
        </div>

        <!-- Page Header -->
        <div class="page-header" style="background:linear-gradient(135deg,#4361ee 0%,#3651d4 60%,#2c46d4 100%);">
            <div class="page-title">
                <svg fill="white" viewBox="0 0 24 24" width="28" height="28">
                    <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.01 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                    <circle cx="7.5" cy="14.5" r="1.5"/>
                    <circle cx="16.5" cy="14.5" r="1.5"/>
                </svg>
                <div>
                    <h3 class="font-weight-bold mb-0">Request Vehicle Registration</h3>
                    <p class="mb-0 mt-1" style="opacity:0.9;font-size:0.9rem;">
                        Fill in your vehicle details. The barangay secretary will review your request.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="index.php?controller=constituent&action=submitVehicleRequest">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <!-- Section 1: Vehicle Identification -->
            <div class="vehicle-form-card">
                <div class="vehicle-form-section-header">
                    <i class="fas fa-id-card"></i> Vehicle Identification
                </div>
                <div class="vehicle-form-body">
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label-modern">Plate Number</label>
                            <input type="text" name="plate_number"
                                class="form-control form-control-modern plate-input"
                                value="<?= $v('plate_number') ?>"
                                placeholder="e.g. ABC 1234"
                                maxlength="20">
                            <small class="text-muted">Leave blank if not yet assigned</small>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label-modern">OR Number (Official Receipt)</label>
                            <input type="text" name="or_number"
                                class="form-control form-control-modern"
                                value="<?= $v('or_number') ?>"
                                placeholder="e.g. 12345678"
                                maxlength="30">
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label-modern">CR Number (Certificate of Registration)</label>
                            <input type="text" name="cr_number"
                                class="form-control form-control-modern"
                                value="<?= $v('cr_number') ?>"
                                placeholder="e.g. 98765432"
                                maxlength="30">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Vehicle Details -->
            <div class="vehicle-form-card">
                <div class="vehicle-form-section-header">
                    <i class="fas fa-car"></i> Vehicle Details
                </div>
                <div class="vehicle-form-body">

                    <!-- Row 1: Type / Make / Model -->
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label-modern">Vehicle Type <span class="text-danger">*</span></label>
                            <select name="vehicle_type" class="form-control form-control-modern" required>
                                <option value="">— Select Type —</option>
                                <?php foreach ($vehicleTypes as $type): ?>
                                    <option value="<?= htmlspecialchars($type) ?>"
                                        <?= ($old['vehicle_type'] ?? '') === $type ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label-modern">Make (Brand) <span class="text-danger">*</span></label>
                            <input type="text" name="make"
                                class="form-control form-control-modern"
                                value="<?= $v('make') ?>"
                                placeholder="e.g. Toyota, Honda, Yamaha"
                                maxlength="60" required>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label-modern">Model</label>
                            <input type="text" name="model"
                                class="form-control form-control-modern"
                                value="<?= $v('model') ?>"
                                placeholder="e.g. Vios, Civic, Mio"
                                maxlength="60">
                        </div>
                    </div>

                    <!-- Row 2: Year / Color / Fuel / Transmission -->
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label-modern">Year <span class="text-danger">*</span></label>
                            <input type="number" name="year"
                                class="form-control form-control-modern"
                                value="<?= $v('year') ?>"
                                placeholder="e.g. 2020"
                                min="1900" max="<?= date('Y') + 1 ?>" required>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label-modern">Color <span class="text-danger">*</span></label>
                            <input type="text" name="color"
                                class="form-control form-control-modern"
                                value="<?= $v('color') ?>"
                                placeholder="e.g. White, Black, Red"
                                maxlength="40" required>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label-modern">Fuel Type</label>
                            <select name="fuel_type" class="form-control form-control-modern">
                                <option value="">— Select —</option>
                                <?php foreach ($fuelTypes as $fuel): ?>
                                    <option value="<?= htmlspecialchars($fuel) ?>"
                                        <?= ($old['fuel_type'] ?? '') === $fuel ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($fuel) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label-modern">Transmission</label>
                            <select name="transmission" class="form-control form-control-modern">
                                <option value="">— Select —</option>
                                <?php foreach ($transmissionTypes as $trans): ?>
                                    <option value="<?= htmlspecialchars($trans) ?>"
                                        <?= ($old['transmission'] ?? '') === $trans ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($trans) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: Engine / Chassis / Vehicle Use -->
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label-modern">Engine Number</label>
                            <input type="text" name="engine_number"
                                class="form-control form-control-modern"
                                value="<?= $v('engine_number') ?>"
                                placeholder="e.g. 1NZ-FE1234567"
                                maxlength="60">
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label-modern">Chassis / VIN Number</label>
                            <input type="text" name="chassis_number"
                                class="form-control form-control-modern"
                                value="<?= $v('chassis_number') ?>"
                                placeholder="e.g. JTDBT923X71234567"
                                maxlength="60">
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label-modern">Vehicle Use <span class="text-danger">*</span></label>
                            <select name="vehicle_use" class="form-control form-control-modern" required>
                                <?php foreach (['Private', 'Public', 'For Hire'] as $u): ?>
                                    <option value="<?= $u ?>"
                                        <?= ($old['vehicle_use'] ?? 'Private') === $u ? 'selected' : '' ?>>
                                        <?= $u ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Section 3: Engine & Chassis -->

            <!-- Section 4: Notes -->
            <div class="vehicle-form-card">
                <div class="vehicle-form-section-header">
                    <i class="fas fa-sticky-note"></i> Additional Notes
                </div>
                <div class="vehicle-form-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label-modern">Notes / Remarks</label>
                            <textarea name="notes" class="form-control form-control-modern" rows="3"
                                placeholder="Any additional information about this vehicle..."
                                maxlength="500"><?= $v('notes') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end mb-5" style="gap:.75rem;">
                <button type="submit" class="btn btn-primary-modern btn-modern" style="padding:.6rem 1.8rem;">
                    <i class="fas fa-paper-plane mr-1"></i> Submit Request
                </button>
            </div>

        </form>
    </div>
</div>

<style>
.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.1rem;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    color: #374151;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.btn-back:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #1e293b;
    text-decoration: none;
}
.vehicle-form-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    margin-bottom: 1.25rem;
    overflow: visible;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
}
.vehicle-form-section-header {
    background: linear-gradient(90deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e5e7eb;
    padding: .75rem 1.25rem;
    font-size: .82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #475569;
    display: flex;
    align-items: center;
    gap: .5rem;
    border-radius: 12px 12px 0 0;
}
.vehicle-form-body { padding: 1.25rem 1.25rem .25rem; }
.form-label-modern {
    font-size: .8rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: .3rem;
    display: block;
}
.plate-input {
    font-family: 'Courier New', Courier, monospace;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    font-size: .95rem;
}
.vehicle-use-toggle { display: flex; gap: .6rem; flex-wrap: wrap; }
.vehicle-use-option { flex: 0 0 auto; width: 220px; cursor: pointer; margin: 0; }
.vehicle-use-option input[type="radio"] { display: none; }
.vehicle-use-card {
    display: flex;
    align-items: center;
    gap: .65rem;
    padding: .55rem .9rem;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
    transition: border-color .15s, background .15s;
    color: #6b7280;
    width: 100%;
    cursor: pointer;
}
.vehicle-use-option input:checked + .vehicle-use-card {
    border-color: #4361ee;
    background: #eff3ff;
    color: #3651d4;
}
.vehicle-use-card:hover { border-color: #a5b4fc; background: #f5f7ff; }
.vehicle-use-info { display: flex; flex-direction: column; gap: 1px; }
.vehicle-use-label { font-size: .85rem; font-weight: 600; color: #374151; line-height: 1.2; }
.vehicle-use-option input:checked + .vehicle-use-card .vehicle-use-label { color: #3651d4; }
.vehicle-use-desc { font-size: .75rem; color: #9ca3af; line-height: 1.2; }
.btn-view-vehicles {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.1rem;
    background: #4361ee;
    border: 1.5px solid #3651d4;
    border-radius: 8px;
    color: #fff;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(67,97,238,0.18);
}
.btn-view-vehicles:hover {
    background: #3651d4;
    border-color: #2c46d4;
    color: #fff;
    text-decoration: none;
}
@media (max-width: 576px) {
    .vehicle-form-body { padding: 1rem 0.85rem 0.25rem; }
    .vehicle-form-section-header { padding: 0.65rem 0.85rem; font-size: 0.75rem; }
    .page-header { padding: 1rem !important; }
    .page-header h3 { font-size: 1rem !important; }
    .d-flex.justify-content-end { justify-content: stretch !important; }
    .d-flex.justify-content-end button { width: 100%; justify-content: center; }
    @media (max-width: 576px) {
    .btn-back,
    .btn-view-vehicles {
        flex: 1 1 auto;
        justify-content: center;
        font-size: 0.78rem;
        padding: 0.45rem 0.75rem;
    }
}
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var plateInput = document.querySelector('.plate-input');
    if (plateInput) {
        plateInput.addEventListener('input', function () {
            var pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });
    }
});
</script>

<?php
$content = ob_get_clean();
require_once('app/Views/layouts/main.php');
?>
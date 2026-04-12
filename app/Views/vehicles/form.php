<?php
ob_start();

// ── Pull values from controller ──
$vehicle      = $vehicle      ?? [];
$constituents = $constituents ?? [];
$isEdit       = !empty($vehicle['id']);
$pageTitle    = $isEdit ? 'Edit Vehicle' : 'Register Vehicle';

// Field helpers
$v = function(string $key, $default = '') use ($vehicle) {
    return htmlspecialchars($vehicle[$key] ?? $default, ENT_QUOTES, 'UTF-8');
};

$vehicleTypes = [
    'Sedan', 'SUV', 'Van / Minivan', 'Pickup Truck', 'Motorcycle',
    'Tricycle', 'Jeepney', 'Bus', 'Truck', 'E-Bike', 'Other',
];

$fuelTypes = [
    'Gasoline (Unleaded)',
    'Gasoline (Premium)',
    'Diesel',
    'LPG',
    'Electric',
    'Hybrid',
    'Other',
];

$transmissionTypes = ['Automatic', 'Manual', 'CVT', 'Semi-Automatic'];

$formAction = $isEdit
    ? 'index.php?controller=vehicles&action=update'
    : 'index.php?controller=vehicles&action=store';
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
        <div style="display:flex; margin-bottom:1.5rem;">
            <a href="index.php?controller=vehicles" class="btn-back">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
                </svg>
                Back to Vehicle Registry
            </a>
        </div>

        <!-- Page Header -->
        <div class="page-header" style="background:linear-gradient(135deg,#4361ee 0%,#3651d4 60%,#2c46d4 100%);">
            <div class="page-title">
                <svg fill="white" viewBox="0 0 24 24" width="28" height="28">
                    <?php if ($isEdit): ?>
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a1 1 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    <?php else: ?>
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                        <circle cx="7.5" cy="14.5" r="1.5"/>
                        <circle cx="16.5" cy="14.5" r="1.5"/>
                    <?php endif; ?>
                </svg>
                <div>
                    <h3 class="font-weight-bold mb-0"><?= $pageTitle ?></h3>
                    <p class="mb-0 mt-1" style="opacity:0.9;font-size:0.9rem;">
                        <?= $isEdit ? 'Update vehicle information and owner details' : 'Fill in the details to register a new vehicle' ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="<?= $formAction ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int)$vehicle['id'] ?>">
            <?php endif; ?>
            <input type="hidden" name="owner_type" value="constituent">

            <!-- ── Section 1: Vehicle Identification ── -->
            <div class="vehicle-form-card">
                <div class="vehicle-form-section-header">
                    <i class="fas fa-id-card"></i>
                    Vehicle Identification
                </div>
                <div class="vehicle-form-body">
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label-modern">Plate Number <span class="text-danger">*</span></label>
                            <input type="text" name="plate_number"
                                class="form-control form-control-modern plate-input"
                                value="<?= $v('plate_number') ?>"
                                placeholder="e.g. ABC 1234"
                                maxlength="20"
                                required>
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

            <!-- ── Section 2: Vehicle Details ── -->
            <div class="vehicle-form-card">
                <div class="vehicle-form-section-header">
                    <i class="fas fa-car"></i>
                    Vehicle Details
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
                                        <?= ($vehicle['vehicle_type'] ?? '') === $type ? 'selected' : '' ?>>
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
                                maxlength="60"
                                required>
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

                    <!-- Row 2: Year / Color / Fuel Type / Transmission -->
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label-modern">Year <span class="text-danger">*</span></label>
                            <input type="number" name="year"
                                class="form-control form-control-modern"
                                value="<?= $v('year') ?>"
                                placeholder="e.g. 2020"
                                min="1900" max="<?= date('Y') + 1 ?>"
                                required>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label-modern">Color <span class="text-danger">*</span></label>
                            <input type="text" name="color"
                                class="form-control form-control-modern"
                                value="<?= $v('color') ?>"
                                placeholder="e.g. White, Black, Red"
                                maxlength="40"
                                required>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label-modern">Fuel Type</label>
                            <select name="fuel_type" class="form-control form-control-modern">
                                <option value="">— Select —</option>
                                <?php foreach ($fuelTypes as $fuel): ?>
                                    <option value="<?= htmlspecialchars($fuel) ?>"
                                        <?= ($vehicle['fuel_type'] ?? '') === $fuel ? 'selected' : '' ?>>
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
                                        <?= ($vehicle['transmission'] ?? '') === $trans ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($trans) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: Engine / Chassis -->
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label-modern">Engine Number</label>
                            <input type="text" name="engine_number"
                                class="form-control form-control-modern"
                                value="<?= $v('engine_number') ?>"
                                placeholder="e.g. 1NZ-FE1234567"
                                maxlength="60">
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label-modern">Chassis / VIN Number</label>
                            <input type="text" name="chassis_number"
                                class="form-control form-control-modern"
                                value="<?= $v('chassis_number') ?>"
                                placeholder="e.g. JTDBT923X71234567"
                                maxlength="60">
                        </div>
                    </div>

                    <!-- Row 4: Vehicle Use (full width) -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label-modern">Vehicle Use <span class="text-danger">*</span></label>
                            <div class="vehicle-use-toggle">
                                <label class="vehicle-use-option">
                                    <input type="radio" name="vehicle_use" value="Private"
                                        <?= (($vehicle['vehicle_use'] ?? 'Private') === 'Private') ? 'checked' : '' ?> required>
                                    <span class="vehicle-use-card">
                                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                                            <circle cx="7.5" cy="14.5" r="1.5"/>
                                            <circle cx="16.5" cy="14.5" r="1.5"/>
                                        </svg>
                                        <span class="vehicle-use-info">
                                            <span class="vehicle-use-label">Private</span>
                                            <span class="vehicle-use-desc">Personal use only</span>
                                        </span>
                                    </span>
                                </label>
                                <label class="vehicle-use-option">
                                    <input type="radio" name="vehicle_use" value="Public"
                                        <?= (($vehicle['vehicle_use'] ?? '') === 'Public') ? 'checked' : '' ?>>
                                    <span class="vehicle-use-card">
                                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M4 16c0 .88.39 1.67 1 2.22V20c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h8v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1.78c.61-.55 1-1.34 1-2.22V6c0-3.5-3.58-4-8-4s-8 .5-8 4v10zm3.5 1c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm9 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm1.5-6H6V6h12v5z"/>
                                        </svg>
                                        <span class="vehicle-use-info">
                                            <span class="vehicle-use-label">Public Transport</span>
                                            <span class="vehicle-use-desc">For hire / passenger</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ── Section 3: Owner ── -->
            <div class="vehicle-form-card">
                <div class="vehicle-form-section-header">
                    <i class="fas fa-user"></i>
                    Constituent Owner
                </div>
                <div class="vehicle-form-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label-modern">
                                Select Constituent Owner <span class="text-danger">*</span>
                            </label>
                            <select class="form-control selectpicker"
                                name="owner_constituent_id"
                                id="owner_constituent_id"
                                data-live-search="true"
                                data-size="10"
                                title="Search constituent by name..."
                                required>
                                <?php foreach ($constituents as $c): ?>
                                    <option value="<?= (int)$c['id'] ?>"
                                        <?= ((int)($vehicle['owner_constituent_id'] ?? 0) === (int)$c['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(
                                            $c['last_name'] . ', ' .
                                            $c['first_name'] . ' ' .
                                            ($c['middle_name'] ?? '') . ' ' .
                                            ($c['suffix'] ?? '')
                                        ) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="field-hint" style="display:flex;align-items:flex-start;gap:.35rem;margin-top:.4rem;font-size:.78rem;color:#6b7280;">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;margin-top:1px;">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Only registered barangay constituents can be linked as vehicle owners.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Section 4: Notes ── -->
            <div class="vehicle-form-card">
                <div class="vehicle-form-section-header">
                    <i class="fas fa-sticky-note"></i>
                    Additional Notes
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

            <!-- ── Form Actions ── -->
            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="btn btn-primary-modern btn-modern" style="padding:.6rem 1.8rem;">
                    <i class="fas fa-<?= $isEdit ? 'save' : 'plus' ?> mr-1"></i>
                    <?= $isEdit ? 'Update Vehicle' : 'Register Vehicle' ?>
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

.vehicle-form-section-header:first-child {
    border-radius: 12px 12px 0 0;
}

.bootstrap-select .dropdown-menu {
    z-index: 9999 !important;
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
}

.vehicle-form-body {
    padding: 1.25rem 1.25rem .25rem;
}

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

/* ── Vehicle Use Toggle ── */
.vehicle-use-toggle {
    display: flex;
    gap: .6rem;
}

.vehicle-use-option {
    flex: 0 0 auto;
    width: 220px;
    cursor: pointer;
    margin: 0;
}

.vehicle-use-option input[type="radio"] {
    display: none;
}

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

.vehicle-use-card:hover {
    border-color: #a5b4fc;
    background: #f5f7ff;
}

.vehicle-use-info {
    display: flex;
    flex-direction: column;
    gap: 1px;
}

.vehicle-use-label {
    font-size: .85rem;
    font-weight: 600;
    color: #374151;
    line-height: 1.2;
}

.vehicle-use-option input:checked + .vehicle-use-card .vehicle-use-label {
    color: #3651d4;
}

.vehicle-use-desc {
    font-size: .75rem;
    color: #9ca3af;
    line-height: 1.2;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Plate input: force uppercase ──
    var plateInput = document.querySelector('.plate-input');
    if (plateInput) {
        plateInput.addEventListener('input', function () {
            var pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });
    }

    // ── Disable submit until changes are made (edit mode only) ──
    var isEdit = <?= $isEdit ? 'true' : 'false' ?>;
    if (!isEdit) return;

    var form       = document.querySelector('form');
    var submitBtn  = document.querySelector('button[type="submit"]');

    // Snapshot original values on load
    function getFormSnapshot() {
        var snapshot = {};
        form.querySelectorAll('input, select, textarea').forEach(function (el) {
            if (el.type === 'radio') {
                if (el.checked) snapshot[el.name] = el.value;
            } else if (el.type !== 'hidden') {
                snapshot[el.name] = el.value;
            }
        });
        return JSON.stringify(snapshot);
    }

    var originalSnapshot = getFormSnapshot();

    // Start disabled
    submitBtn.disabled = true;
    submitBtn.style.opacity = '0.5';
    submitBtn.style.cursor  = 'not-allowed';

    // Check on any change
    function checkForChanges() {
        var currentSnapshot = getFormSnapshot();
        var hasChanges = currentSnapshot !== originalSnapshot;
        submitBtn.disabled      = !hasChanges;
        submitBtn.style.opacity = hasChanges ? '1'           : '0.5';
        submitBtn.style.cursor  = hasChanges ? 'pointer'     : 'not-allowed';
    }

    form.addEventListener('input',  checkForChanges);
    form.addEventListener('change', checkForChanges);
});
</script>

<script src="public/assets/js/bc_general_entry.js?v=<?= filemtime('public/assets/js/bc_general_entry.js') ?>"></script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
<?php
ob_start();
$title = $title ?? 'Vehicle Details';

$plateNumber  = $vehicle['plate_number']   ?? '';
$make         = $vehicle['make']           ?? '';
$model        = $vehicle['model']          ?? '';
$year         = $vehicle['year']           ?? '';
$vehicleType  = $vehicle['vehicle_type']   ?? '';
$vehicleUse   = $vehicle['vehicle_use']    ?? 'Private';
$color        = $vehicle['color']          ?? '';
$fuelType     = $vehicle['fuel_type']      ?? '';
$transmission = $vehicle['transmission']   ?? '';
$engineNumber = $vehicle['engine_number']  ?? '';
$chassisNum   = $vehicle['chassis_number'] ?? '';
$orNumber     = $vehicle['or_number']      ?? '';
$crNumber     = $vehicle['cr_number']      ?? '';
$ownerName    = $vehicle['owner_name']     ?? '';
$notes        = $vehicle['notes']          ?? '';

$vehicleTypes = [
    'Sedan', 'SUV', 'Van / Minivan', 'Pickup Truck', 'Motorcycle',
    'Tricycle', 'Jeepney', 'Bus', 'Truck', 'E-Bike', 'Other',
];
$fuelTypes = [
    'Gasoline (Unleaded)', 'Gasoline (Premium)', 'Diesel',
    'LPG', 'Electric', 'Hybrid', 'Other',
];
$transmissionTypes = ['Automatic', 'Manual', 'CVT', 'Semi-Automatic'];
$vehicleUseOptions = ['Private', 'Public Transport', 'For Hire', 'Government'];
?>

<link rel="stylesheet" href="public/assets/css/constituents_index.css">
<link rel="stylesheet" href="public/assets/css/bc_general_entry.css">

<style>
/* ── Notices ── */
.cv-edit-notice,
.cv-pending-notice {
    border-radius: 10px;
    padding: 0.75rem 1.1rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.82rem;
    font-weight: 500;
}
.cv-edit-notice {
    background: linear-gradient(90deg, #fffbeb, #fefce8);
    border: 1.5px solid #fde68a;
    color: #92400e;
}
.cv-edit-notice svg { flex-shrink: 0; color: #d97706; }
.cv-pending-notice {
    background: linear-gradient(90deg, #eff6ff, #dbeafe);
    border: 1.5px solid #93c5fd;
    color: #1e40af;
}
.cv-pending-notice svg { flex-shrink: 0; color: #3b82f6; }

/* ── Card style (matches register vehicle) ── */
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

/* ── Changed field highlight ── */
.form-control-modern.changed {
    border-color: #f59e0b !important;
    background: #fffbeb !important;
    box-shadow: 0 0 0 3px rgba(245,158,11,0.12) !important;
}

/* ── Plate mono ── */
.plate-input {
    font-family: 'Courier New', Courier, monospace;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    font-size: .95rem;
}

/* ── Owner banner ── */
.owner-banner {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: linear-gradient(90deg,#f8fafc,#f1f5f9);
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: .85rem 1.1rem;
}
.owner-avatar {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg,#4361ee,#3651d4);
    color: #fff;
    font-size: 1.2rem;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.owner-info { display: flex; flex-direction: column; gap: 2px; }
.owner-label { font-size: .72rem; font-weight: 700; color: #a0aec0; text-transform: uppercase; letter-spacing: .06em; }
.owner-name  { font-size: .95rem; font-weight: 700; color: #1e293b; }

/* ── Back button ── */
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

/* ── Page header ── */
.page-header {
    background: linear-gradient(135deg,#4361ee 0%,#3651d4 60%,#2c46d4 100%);
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    color: #fff;
}
.page-title {
    display: flex;
    align-items: center;
    gap: .85rem;
}
.page-title h3 { font-size: 1.15rem; font-weight: 700; margin: 0; }
.page-title p  { margin: 0; opacity: .9; font-size: .88rem; }

/* ── Submit row ── */
.submit-section {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    margin-bottom: 2.5rem;
    gap: .75rem;
    flex-wrap: wrap;
}
.changes-hint {
    font-size: .78rem;
    color: #9ca3af;
    display: flex;
    align-items: center;
    gap: .4rem;
}
.changes-hint.has-changes {
    color: #d97706;
    font-weight: 600;
}
.btn-submit-edit {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.6rem 1.8rem;
    background: linear-gradient(135deg, #4361ee, #3651d4);
    color: white;
    border: none;
    border-radius: 9px;
    font-size: 0.875rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 3px 10px rgba(67,97,238,0.3);
    font-family: inherit;
}
.btn-submit-edit:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(67,97,238,0.4);
}
.btn-submit-edit:disabled {
    background: #e5e7eb;
    color: #9ca3af;
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
}

@media (max-width: 576px) {
    .submit-section { justify-content: stretch; flex-direction: column; }
    .btn-submit-edit { width: 100%; justify-content: center; }
    .vehicle-form-body { padding: 1rem 0.85rem 0.25rem; }
    .vehicle-form-section-header { font-size: 0.75rem; padding: 0.65rem 0.85rem; }
}
</style>

<div class="container-fluid px-4 mt-3">
<div class="content-wrapper">

    <!-- ── Action Bar ── -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.5rem;">
        <a href="index.php?controller=constituent&action=myVehicles" class="btn-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Back to My Vehicles
        </a>
    </div>

    <!-- ── Notices ── -->
    <?php
    $db = (new Database())->connect();
    $pendingStmt = $db->prepare("
        SELECT id FROM vehicle_requests
        WHERE vehicle_id = :vid AND request_type = 'edit' AND status = 'pending'
        LIMIT 1
    ");
    $pendingStmt->execute([':vid' => (int)$vehicle['id']]);
    $hasPendingEdit = (bool)$pendingStmt->fetch();
    ?>

    <?php if ($hasPendingEdit): ?>
    <div class="cv-pending-notice">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        You have a <strong>&nbsp;pending edit request&nbsp;</strong> for this vehicle. You cannot submit another until it is reviewed.
    </div>
    <?php else: ?>
    <?php endif; ?>

    <!-- ── Page Header ── -->
    <div class="page-header">
        <div class="page-title">
            <svg fill="white" viewBox="0 0 24 24" width="28" height="28">
                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                <circle cx="7.5" cy="14.5" r="1.5"/>
                <circle cx="16.5" cy="14.5" r="1.5"/>
            </svg>
            <div>
                <h3>Edit Vehicle Record</h3>
                <p>Update your vehicle details. Changes will be reviewed by the barangay secretary before approval.</p>
            </div>
        </div>
    </div>

    <!-- ── Form ── -->
    <form method="POST" action="index.php?controller=constituent&action=submitVehicleEditRequest" id="editVehicleForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= Session::generateCsrfToken() ?>">
        <input type="hidden" name="vehicle_id" value="<?= (int)$vehicle['id'] ?>">

        <!-- Section 1: Vehicle Identification -->
        <div class="vehicle-form-card">
            <div class="vehicle-form-section-header">
                <i class="fas fa-id-card"></i> Vehicle Identification
            </div>
            <div class="vehicle-form-body">
                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <label class="form-label-modern">Plate Number</label>
                        <input type="text"
                            name="plate_number"
                            class="form-control form-control-modern plate-input"
                            value="<?= htmlspecialchars($plateNumber) ?>"
                            data-original="<?= htmlspecialchars($plateNumber) ?>"
                            placeholder="e.g. ABC 1234"
                            maxlength="20"
                            <?= $hasPendingEdit ? 'disabled' : '' ?>>
                        <small class="text-muted">Leave blank if not yet assigned</small>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label class="form-label-modern">OR Number (Official Receipt)</label>
                        <input type="text"
                            name="or_number"
                            class="form-control form-control-modern"
                            value="<?= htmlspecialchars($orNumber) ?>"
                            data-original="<?= htmlspecialchars($orNumber) ?>"
                            placeholder="e.g. 12345678"
                            maxlength="30"
                            <?= $hasPendingEdit ? 'disabled' : '' ?>>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label class="form-label-modern">CR Number (Certificate of Registration)</label>
                        <input type="text"
                            name="cr_number"
                            class="form-control form-control-modern"
                            value="<?= htmlspecialchars($crNumber) ?>"
                            data-original="<?= htmlspecialchars($crNumber) ?>"
                            placeholder="e.g. 98765432"
                            maxlength="30"
                            <?= $hasPendingEdit ? 'disabled' : '' ?>>
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
                        <select name="vehicle_type"
                            class="form-control form-control-modern"
                            data-original="<?= htmlspecialchars($vehicleType) ?>"
                            required
                            <?= $hasPendingEdit ? 'disabled' : '' ?>>
                            <option value="">— Select Type —</option>
                            <?php foreach ($vehicleTypes as $t): ?>
                                <option value="<?= htmlspecialchars($t) ?>" <?= $vehicleType === $t ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label class="form-label-modern">Make (Brand) <span class="text-danger">*</span></label>
                        <input type="text"
                            name="make"
                            class="form-control form-control-modern"
                            value="<?= htmlspecialchars($make) ?>"
                            data-original="<?= htmlspecialchars($make) ?>"
                            placeholder="e.g. Toyota, Honda, Yamaha"
                            maxlength="60" required
                            <?= $hasPendingEdit ? 'disabled' : '' ?>>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label class="form-label-modern">Model</label>
                        <input type="text"
                            name="model"
                            class="form-control form-control-modern"
                            value="<?= htmlspecialchars($model) ?>"
                            data-original="<?= htmlspecialchars($model) ?>"
                            placeholder="e.g. Vios, Civic, Mio"
                            maxlength="60"
                            <?= $hasPendingEdit ? 'disabled' : '' ?>>
                    </div>
                </div>

                <!-- Row 2: Year / Color / Fuel / Transmission -->
                <div class="row">
                    <div class="col-12 col-md-3 mb-3">
                        <label class="form-label-modern">Year <span class="text-danger">*</span></label>
                        <input type="number"
                            name="year"
                            class="form-control form-control-modern"
                            value="<?= htmlspecialchars($year) ?>"
                            data-original="<?= htmlspecialchars($year) ?>"
                            placeholder="e.g. 2020"
                            min="1900" max="<?= date('Y') + 1 ?>" required
                            <?= $hasPendingEdit ? 'disabled' : '' ?>>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <label class="form-label-modern">Color <span class="text-danger">*</span></label>
                        <input type="text"
                            name="color"
                            class="form-control form-control-modern"
                            value="<?= htmlspecialchars($color) ?>"
                            data-original="<?= htmlspecialchars($color) ?>"
                            placeholder="e.g. White, Black, Red"
                            maxlength="40" required
                            <?= $hasPendingEdit ? 'disabled' : '' ?>>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <label class="form-label-modern">Fuel Type</label>
                        <select name="fuel_type"
                            class="form-control form-control-modern"
                            data-original="<?= htmlspecialchars($fuelType) ?>"
                            <?= $hasPendingEdit ? 'disabled' : '' ?>>
                            <option value="">— Select —</option>
                            <?php foreach ($fuelTypes as $f): ?>
                                <option value="<?= htmlspecialchars($f) ?>" <?= $fuelType === $f ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($f) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <label class="form-label-modern">Transmission</label>
                        <select name="transmission"
                            class="form-control form-control-modern"
                            data-original="<?= htmlspecialchars($transmission) ?>"
                            <?= $hasPendingEdit ? 'disabled' : '' ?>>
                            <option value="">— Select —</option>
                            <?php foreach ($transmissionTypes as $tr): ?>
                                <option value="<?= htmlspecialchars($tr) ?>" <?= $transmission === $tr ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tr) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Row 3: Engine / Chassis / Vehicle Use -->
                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <label class="form-label-modern">Engine Number</label>
                        <input type="text"
                            name="engine_number"
                            class="form-control form-control-modern plate-input"
                            value="<?= htmlspecialchars($engineNumber) ?>"
                            data-original="<?= htmlspecialchars($engineNumber) ?>"
                            placeholder="e.g. 1NZ-FE1234567"
                            maxlength="60"
                            <?= $hasPendingEdit ? 'disabled' : '' ?>>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label class="form-label-modern">Chassis / VIN Number</label>
                        <input type="text"
                            name="chassis_number"
                            class="form-control form-control-modern plate-input"
                            value="<?= htmlspecialchars($chassisNum) ?>"
                            data-original="<?= htmlspecialchars($chassisNum) ?>"
                            placeholder="e.g. JTDBT923X71234567"
                            maxlength="60"
                            <?= $hasPendingEdit ? 'disabled' : '' ?>>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label class="form-label-modern">Vehicle Use <span class="text-danger">*</span></label>
                        <select name="vehicle_use"
                            class="form-control form-control-modern"
                            data-original="<?= htmlspecialchars($vehicleUse) ?>"
                            required
                            <?= $hasPendingEdit ? 'disabled' : '' ?>>
                            <?php foreach ($vehicleUseOptions as $u): ?>
                                <option value="<?= $u ?>" <?= $vehicleUse === $u ? 'selected' : '' ?>><?= $u ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

            </div>
        </div>

        <!-- Section 3: Registered Owner -->
        <div class="vehicle-form-card">
            <div class="vehicle-form-section-header">
                <i class="fas fa-user"></i> Registered Owner
            </div>
            <div class="vehicle-form-body" style="padding-bottom:1.25rem;">
                <div class="owner-banner">
                    <div class="owner-avatar"><?= strtoupper(substr($ownerName, 0, 1) ?: '?') ?></div>
                    <div class="owner-info">
                        <span class="owner-label">Registered Owner</span>
                        <span class="owner-name"><?= htmlspecialchars($ownerName ?: '—') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Notes -->
        <div class="vehicle-form-card">
            <div class="vehicle-form-section-header">
                <i class="fas fa-sticky-note"></i> Additional Notes
            </div>
            <div class="vehicle-form-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label-modern">Notes / Remarks</label>
                        <textarea name="notes"
                            class="form-control form-control-modern"
                            rows="3"
                            data-original="<?= htmlspecialchars($notes) ?>"
                            placeholder="Any additional information about this vehicle..."
                            maxlength="500"
                            <?= $hasPendingEdit ? 'disabled' : '' ?>><?= htmlspecialchars($notes) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Submit ── -->
        <?php if (!$hasPendingEdit): ?>
        <div class="submit-section">
            <button type="submit" class="btn-submit-edit" id="submitEditBtn" disabled>
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Submit Edit Request
            </button>
        </div>
        <?php endif; ?>

    </form>

</div>
</div>

<script>
(function () {
    var form  = document.getElementById('editVehicleForm');
    var btn   = document.getElementById('submitEditBtn');
    var hint  = document.getElementById('changesHint');

    if (!form || !btn) return;

    // target every field that has a data-original attribute
    var fields = form.querySelectorAll('[data-original]');

    function checkChanges() {
        var changed = false;

        fields.forEach(function (field) {
            var original = (field.dataset.original || '').trim();
            var current  = field.value.trim();
            if (current !== original) {
                changed = true;
                field.classList.add('changed');
            } else {
                field.classList.remove('changed');
            }
        });

        btn.disabled = !changed;

        if (hint) {
            if (changed) {
                hint.classList.add('has-changes');
                hint.innerHTML =
                    '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>' +
                    '</svg> You have unsaved changes — submit to send for review';
            } else {
                hint.classList.remove('has-changes');
                hint.innerHTML =
                    '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' +
                    '</svg> Make changes to enable the submit button';
            }
        }
    }

    fields.forEach(function (field) {
        field.addEventListener('input',  checkChanges);
        field.addEventListener('change', checkChanges);
    });

    // Auto-uppercase plate number
    var plateField = form.querySelector('.plate-input[name="plate_number"]');
    if (plateField) {
        plateField.addEventListener('input', function () {
            var pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });
    }
})();
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
<?php
ob_start();
$title    = $title    ?? 'My Registered Vehicles';
$vehicles = $vehicles ?? [];

function mvDisplay($v, $fallback = '—') {
    $str = trim((string)$v);
    return $str !== '' ? htmlspecialchars($str) : $fallback;
}
?>

<link rel="stylesheet" href="public/assets/css/vehicle_view.css">
<link rel="stylesheet" href="public/assets/css/constituent_my_requests.css?v=<?= time() ?>">

<style>
/* ── My Vehicles List Page ── */
.mv-header {
    background: linear-gradient(135deg, #4361ee 0%, #3651d4 60%, #2c46d4 100%);
    border-radius: 0.75rem;
    padding: 1.75rem 2rem;
    margin-bottom: 1.75rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(67,97,238,0.25);
}

.mv-header::after {
    content: '';
    position: absolute;
    right: -30px; top: -30px;
    width: 180px; height: 180px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
    pointer-events: none;
}

.mv-header-icon {
    width: 52px; height: 52px;
    background: rgba(255,255,255,0.18);
    border: 2px solid rgba(255,255,255,0.35);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    color: white;
}

.mv-header-text h3 {
    margin: 0 0 0.2rem;
    font-size: 1.3rem;
    font-weight: 800;
    color: white;
    letter-spacing: -0.01em;
}

.mv-header-text p {
    margin: 0;
    font-size: 0.85rem;
    color: rgba(255,255,255,0.8);
}

/* ── Top Bar ── */
.mv-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    gap: 0.75rem;
    flex-wrap: wrap;
}

/* ── Vehicle Cards Grid ── */
.mv-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
}

.mv-card {
    background: #fff;
    border-radius: 14px;
    border: 1.5px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    overflow: hidden;
    transition: box-shadow 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
}

.mv-card:hover {
    box-shadow: 0 6px 20px rgba(67,97,238,0.13);
    transform: translateY(-2px);
    border-color: #c5d0f8;
    text-decoration: none;
    color: inherit;
}

.mv-card-header {
    background: linear-gradient(135deg, #4361ee 0%, #3651d4 100%);
    padding: 1.1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.9rem;
}

.mv-card-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    border: 2px solid rgba(255,255,255,0.35);
    display: flex; align-items: center; justify-content: center;
    color: white;
    flex-shrink: 0;
}

.mv-card-plate {
    font-family: 'Courier New', Courier, monospace;
    font-size: 1.1rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    color: white;
    line-height: 1.1;
}

.mv-card-sub {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.75);
    margin-top: 0.15rem;
    font-weight: 500;
}

.mv-card-body {
    padding: 1.1rem 1.25rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
}

.mv-card-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.mv-meta-item {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.mv-meta-label {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: #a0aec0;
}

.mv-meta-value {
    font-size: 0.85rem;
    font-weight: 600;
    color: #2d3748;
}

.mv-color-cell {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.mv-color-swatch {
    width: 12px; height: 12px;
    border-radius: 50%;
    border: 1.5px solid rgba(0,0,0,0.12);
    flex-shrink: 0;
}

.mv-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.18rem 0.55rem;
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}

.mv-badge-private  { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
.mv-badge-public   { background: #fef9c3; color: #854d0e; border: 1px solid #fde68a; }
.mv-badge-forhire  { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
.mv-badge-government { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }

.mv-card-footer {
    padding: 0.75rem 1.25rem;
    border-top: 1px solid #f0f2f5;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.5rem;
}

.mv-btn-view {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.4rem 0.9rem;
    background: #eef2ff;
    color: #4361ee;
    border: 1px solid #c5d0f8;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.18s ease;
    font-family: inherit;
}

.mv-btn-view:hover {
    background: #4361ee;
    color: white;
    border-color: #4361ee;
    text-decoration: none;
}

/* ── Empty State ── */
.mv-empty {
    text-align: center;
    padding: 4rem 2rem;
    color: #a0aec0;
    background: #fff;
    border-radius: 14px;
    border: 1.5px dashed #e2e8f0;
}

.mv-empty svg {
    opacity: 0.35;
    margin-bottom: 1rem;
}

.mv-empty h5 {
    font-size: 1rem;
    font-weight: 700;
    color: #4a5568;
    margin-bottom: 0.35rem;
}

.mv-empty p {
    font-size: 0.85rem;
    margin-bottom: 1.25rem;
}

/* ── Register Button ── */
.btn-register-vehicle {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.55rem 1.25rem;
    background: linear-gradient(135deg, #4361ee, #3651d4);
    color: white;
    border-radius: 9px;
    font-size: 0.85rem;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 3px 10px rgba(67,97,238,0.28);
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    font-family: inherit;
}

.btn-register-vehicle:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(67,97,238,0.38);
    color: white;
    text-decoration: none;
}

@media (max-width: 576px) {
    .mv-grid { grid-template-columns: 1fr; }
    .mv-header { padding: 1.25rem; }
    .mv-topbar { flex-direction: column; align-items: stretch; }
    .mv-topbar .btn-back { justify-content: center; }
    .mv-topbar .btn-register-vehicle { justify-content: center; }
    .mv-card-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.65rem;
    }
    .mv-card-row .mv-meta-item[style*="align-items:flex-end"] {
        align-items: flex-start !important;
    }
    .mv-card-footer { justify-content: stretch; }
    .mv-btn-view { width: 100%; justify-content: center; }
    .mv-card-plate { font-size: 0.95rem; }
}

</style>

<div class="container-fluid px-4 mt-3">
<div class="content-wrapper">

    <?php if (Session::hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <strong>Success!</strong> <?= Session::getFlash('success') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (Session::hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <strong>Error!</strong> <?= Session::getFlash('error') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <!-- ── Top Bar ── -->
    <div class="mv-topbar">
        <a href="index.php?controller=constituent&action=requestVehicle" class="btn-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Back to Register Vehicle
        </a>
        <a href="index.php?controller=constituent&action=requestVehicle" class="btn-register-vehicle">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Register a Vehicle
        </a>
    </div>

    <!-- ── Page Header ── -->
    <div class="mv-header">
        <div class="mv-header-icon">
            <svg width="26" height="26" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                <circle cx="7.5" cy="14.5" r="1.5"/>
                <circle cx="16.5" cy="14.5" r="1.5"/>
            </svg>
        </div>
        <div class="mv-header-text">
            <h3>My Registered Vehicles</h3>
            <p>All vehicles registered to your name in the barangay registry.</p>
        </div>
    </div>

    <!-- ── Vehicle Cards ── -->
    <?php if (!empty($vehicles)): ?>
        <div class="mv-grid">
            <?php foreach ($vehicles as $v): ?>
                <?php
                    $plate      = trim($v['plate_number'] ?? '');
                    $make       = trim($v['make']         ?? '');
                    $model      = trim($v['model']        ?? '');
                    $year       = trim($v['year']         ?? '');
                    $type       = trim($v['vehicle_type'] ?? '');
                    $use        = trim($v['vehicle_use']  ?? 'Private');
                    $color      = trim($v['color']        ?? '');
                    $colorHex   = trim($v['color_hex']    ?? '#9ca3af');
                    $fuelType   = trim($v['fuel_type']    ?? '');
                    $trans      = trim($v['transmission'] ?? '');

                    $useSlug    = strtolower(str_replace([' ', '/'], ['', ''], $use));
                    $badgeClass = match($useSlug) {
                        'publictransport' => 'mv-badge-public',
                        'forhire'         => 'mv-badge-forhire',
                        'government'      => 'mv-badge-government',
                        default           => 'mv-badge-private',
                    };

                    $displayName = $plate !== '' ? $plate : 'No Plate';
                    $makeLine    = trim("$make $model");
                    if ($year) $makeLine .= " · $year";
                ?>
                <div class="mv-card">
                    <div class="mv-card-header">
                        <div class="mv-card-avatar">
                            <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                                <circle cx="7.5" cy="14.5" r="1.5"/><circle cx="16.5" cy="14.5" r="1.5"/>
                            </svg>
                        </div>
                        <div>
                            <div class="mv-card-plate"><?= htmlspecialchars($plate !== '' ? $plate : 'NO PLATE') ?></div>
                            <div class="mv-card-sub"><?= htmlspecialchars($makeLine) ?></div>
                        </div>
                    </div>

                    <div class="mv-card-body">
                        <div class="mv-card-row">
                            <div class="mv-meta-item">
                                <span class="mv-meta-label">Type</span>
                                <span class="mv-meta-value"><?= mvDisplay($type) ?></span>
                            </div>
                            <div class="mv-meta-item" style="align-items:flex-end;">
                                <span class="mv-meta-label">Use</span>
                                <span class="mv-badge <?= $badgeClass ?>"><?= htmlspecialchars($use) ?></span>
                            </div>
                        </div>

                        <div class="mv-card-row">
                            <div class="mv-meta-item">
                                <span class="mv-meta-label">Color</span>
                                <span class="mv-meta-value">
                                    <span class="mv-color-cell">
                                        <span class="mv-color-swatch" style="background:<?= htmlspecialchars($colorHex, ENT_QUOTES) ?>"></span>
                                        <?= mvDisplay($color) ?>
                                    </span>
                                </span>
                            </div>
                            <div class="mv-meta-item" style="align-items:flex-end;">
                                <span class="mv-meta-label">Fuel</span>
                                <span class="mv-meta-value"><?= mvDisplay($fuelType) ?></span>
                            </div>
                        </div>

                        <?php if ($trans !== ''): ?>
                        <div class="mv-meta-item">
                            <span class="mv-meta-label">Transmission</span>
                            <span class="mv-meta-value"><?= mvDisplay($trans) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="mv-card-footer">
                        <a href="index.php?controller=constituent&action=myVehicleView&id=<?= (int)$v['id'] ?>" class="mv-btn-view">
                            <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                            View Details
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="mv-empty">
            <svg width="64" height="64" fill="#9ca3af" viewBox="0 0 24 24">
                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                <circle cx="7.5" cy="14.5" r="1.5"/><circle cx="16.5" cy="14.5" r="1.5"/>
            </svg>
            <h5>No registered vehicles yet</h5>
            <p>Vehicles approved by the barangay secretary will appear here.</p>
            <a href="index.php?controller=constituent&action=requestVehicle" class="btn-register-vehicle">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Register a Vehicle
            </a>
        </div>
    <?php endif; ?>

</div>
</div>

<?php
$content = ob_get_clean();
require_once('app/Views/layouts/main.php');
?>
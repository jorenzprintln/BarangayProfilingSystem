<?php
ob_start();
$title = $title ?? 'Vehicle Details';

$plateNumber  = $vehicle['plate_number']  ?? '';
$make         = $vehicle['make']          ?? '';
$model        = $vehicle['model']         ?? '';
$year         = $vehicle['year']          ?? '';
$vehicleType  = $vehicle['vehicle_type']  ?? '';
$vehicleUse   = $vehicle['vehicle_use']   ?? 'Private';
$color        = $vehicle['color']         ?? '';
$colorHex     = $vehicle['color_hex']     ?? '#9ca3af';
$fuelType     = $vehicle['fuel_type']     ?? '';
$transmission = $vehicle['transmission']  ?? '';
$ownerName    = $vehicle['owner_name']    ?? '';
$notes        = $vehicle['notes']         ?? '';

function vDisplay($v, $fallback = '—') {
    $str = trim((string)$v);
    return $str !== '' ? htmlspecialchars($str) : $fallback;
}
?>

<link rel="stylesheet" href="public/assets/css/vehicle_view.css">

<div class="container-fluid px-4 mt-3">
<div class="content-wrapper">

    <!-- ── Action Bar ── -->
    <div class="action-bar">
        <a href="index.php?controller=vehicles" class="btn-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Back to Vehicles
        </a>
        <div class="action-group">
            <a href="index.php?controller=vehicles&action=edit&id=<?= (int)$vehicle['id'] ?>" class="btn-action-top edit-btn">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Vehicle
            </a>
        </div>
    </div>

    <!-- ── Vehicle Hero ── -->
    <div class="profile-hero">
        <div class="hero-glow"></div>
        <div class="hero-pattern"></div>
        <div class="hero-inner">
            <!-- Car icon avatar -->
            <div class="avatar-wrap">
                <div class="avatar-circle">
                    <svg width="32" height="32" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                        <circle cx="7.5" cy="14.5" r="1.5"/>
                        <circle cx="16.5" cy="14.5" r="1.5"/>
                    </svg>
                </div>
                <div class="avatar-ring"></div>
            </div>

            <div class="hero-text">
                <p class="hero-eyebrow">Vehicle Record</p>
                <h2 class="hero-name"><?= vDisplay($plateNumber) ?></h2>
                <p class="hero-meta">
                    <?= vDisplay($make) ?><?= $model ? ' ' . htmlspecialchars($model) : '' ?>
                    <?= $year ? '&ensp;·&ensp;' . htmlspecialchars($year) : '' ?>
                    <?= $vehicleType ? '&ensp;·&ensp;' . htmlspecialchars($vehicleType) : '' ?>
                </p>
                <div class="hero-badges">
                    <span class="hero-badge badge-use-<?= strtolower($vehicleUse) ?>">
                        <span class="badge-dot"></span>
                        <?= htmlspecialchars($vehicleUse) ?>
                    </span>
                    <?php if (!empty($fuelType)): ?>
                        <span class="hero-badge badge-fuel"><?= htmlspecialchars($fuelType) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Vehicle Details ── -->
    <div class="info-card" style="--delay:.05s">
        <div class="info-card-header">
            <div class="card-title-group">
                <div class="card-icon-wrap">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                        <circle cx="7.5" cy="14.5" r="1.5"/><circle cx="16.5" cy="14.5" r="1.5"/>
                    </svg>
                </div>
                <h5>Vehicle Details</h5>
            </div>
        </div>
        <div class="info-card-body">
            <div class="info-grid">

                <div class="info-item">
                    <span class="info-label">Plate Number</span>
                    <span class="info-value plate-mono"><?= vDisplay($plateNumber) ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Make</span>
                    <span class="info-value"><?= vDisplay($make) ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Model</span>
                    <span class="info-value <?= empty($model) ? 'empty' : '' ?>">
                        <?= vDisplay($model) ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Year</span>
                    <span class="info-value <?= empty($year) ? 'empty' : '' ?>">
                        <?= vDisplay($year) ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Vehicle Type</span>
                    <span class="info-value">
                        <span class="tag-chip"><?= vDisplay($vehicleType) ?></span>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Vehicle Use</span>
                    <span class="info-value">
                        <span class="badge-status badge-use-inline-<?= strtolower($vehicleUse) ?>">
                            <?= htmlspecialchars($vehicleUse) ?>
                        </span>
                    </span>
                </div>

            </div>

            <div class="section-divider">
                <span class="section-divider-label">Specifications</span>
            </div>

            <div class="info-grid">

                <div class="info-item">
                    <span class="info-label">Color</span>
                    <span class="info-value">
                        <div class="color-cell">
                            <span class="color-swatch" style="background:<?= htmlspecialchars($colorHex, ENT_QUOTES) ?>;"></span>
                            <?= vDisplay($color) ?>
                        </div>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Fuel Type</span>
                    <span class="info-value <?= empty($fuelType) ? 'empty' : '' ?>">
                        <?= vDisplay($fuelType) ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Transmission</span>
                    <span class="info-value <?= empty($transmission) ? 'empty' : '' ?>">
                        <?= vDisplay($transmission) ?>
                    </span>
                </div>

            </div>
        </div>
    </div>

    <!-- ── Owner Information ── -->
    <div class="info-card" style="--delay:.1s">
        <div class="info-card-header">
            <div class="card-title-group">
                <div class="card-icon-wrap">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h5>Owner Information</h5>
            </div>
        </div>
        <div class="info-card-body">
            <div class="owner-banner">
                <div class="owner-avatar">
                    <?= strtoupper(substr($ownerName, 0, 1) ?: '?') ?>
                </div>
                <div class="owner-details">
                    <span class="owner-label">Registered Owner</span>
                    <span class="owner-name"><?= vDisplay($ownerName) ?></span>
                </div>
                <?php if (!empty($vehicle['owner_id'])): ?>
                <a href="index.php?controller=constituents&action=view&id=<?= (int)$vehicle['owner_id'] ?>"
                   class="btn-view-owner">
                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                    </svg>
                    View Profile
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── Notes ── -->
    <?php if (!empty($notes)): ?>
    <div class="info-card" style="--delay:.15s">
        <div class="info-card-header">
            <div class="card-title-group">
                <div class="card-icon-wrap">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h5>Notes</h5>
            </div>
        </div>
        <div class="info-card-body">
            <p class="notes-body"><?= htmlspecialchars($notes) ?></p>
        </div>
    </div>
    <?php endif; ?>

</div>
</div>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
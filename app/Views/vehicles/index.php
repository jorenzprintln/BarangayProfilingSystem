<?php
ob_start();

// ── Pull values passed from controller ──
$filters      = $filters      ?? [];
$currentPage  = $currentPage  ?? 1;
$totalPages   = $totalPages   ?? 1;
$totalRecords = $totalRecords ?? 0;
$perPage      = $perPage      ?? 10;

$search          = $filters['search']        ?? '';
$filterType      = $filters['vehicle_type']  ?? '';
$filterFuel      = $filters['fuel_type']     ?? '';
$filterColor     = $filters['color']         ?? '';

// ── Vehicle types ──
$vehicleTypes = [
    'Sedan', 'SUV', 'Van / Minivan', 'Pickup Truck', 'Motorcycle',
    'Tricycle', 'Jeepney', 'E-Bike', 'Bus', 'Truck', 'Other',
];

// ── Fuel types ──
$fuelTypes = [
    'Gasoline (Unleaded)', 'Gasoline (Premium)', 'Diesel',
    'LPG', 'Electric', 'Hybrid', 'Other',
];

// ── Active filters check ──
$activeFilters = array_filter([$search, $filterType, $filterFuel, $filterColor], fn($v) => $v !== '');

// ── Base query string ──
$queryBase = 'index.php?controller=vehicles'
    . '&search='       . urlencode($search)
    . '&vehicle_type=' . urlencode($filterType)
    . '&fuel_type='    . urlencode($filterFuel)
    . '&color='        . urlencode($filterColor);
?>

<link rel="stylesheet" href="public/assets/css/constituents_index.css">
<link rel="stylesheet" href="public/assets/css/vehicles_index.css">

<div class="container-fluid px-4 mt-3">
    <div class="content-wrapper">

        <?php if (Session::hasFlash('success')): ?>
            <div id="success-alert" class="alert alert-success-modern alert-dismissible fade show" role="alert">
                <strong>Success!</strong> <?= Session::getFlash('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (Session::hasFlash('error')): ?>
            <div id="error-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> <?= Session::getFlash('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="page-header vehicles-header">
            <div class="page-title">
                <svg fill="white" viewBox="0 0 24 24" width="28" height="28">
                    <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                    <circle cx="7.5" cy="14.5" r="1.5"/>
                    <circle cx="16.5" cy="14.5" r="1.5"/>
                </svg>
                <div>
                    <h3 class="font-weight-bold mb-0">Vehicle Registry</h3>
                    <p class="mb-0 mt-1" style="opacity:0.9;font-size:0.9rem;">Manage and view all registered constituent vehicles</p>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div class="controls-container">
            <form method="GET" action="index.php" id="filter-form">
                <input type="hidden" name="controller" value="vehicles">
                <input type="hidden" name="page" value="1">

                <div class="row align-items-center mb-3">
                    <div class="col-12 col-sm-auto mb-2 mb-sm-0">
                        <a href="index.php?controller=vehicles&action=archivedVehicles" class="btn-archive-custom">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                                <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Archived Vehicles
                        </a>
                    </div>
                    <div class="col-12 col-sm mb-2 mb-sm-0">
                        <div class="search-wrapper">
                            <svg class="search-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                            </svg>
                            <input type="text" id="search-input" name="search"
                                value="<?= htmlspecialchars($search) ?>"
                                class="form-control form-control-modern"
                                placeholder="Search by plate number, make, or owner name...">
                        </div>
                    </div>

                    <div class="col-12 col-sm-auto">
                        <a href="index.php?controller=vehicles&action=add" class="btn btn-primary-modern btn-modern w-100">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                            </svg>
                            Register Vehicle
                        </a>
                    </div>
                </div>

                <!-- Row 2: Filters -->
                <div class="row align-items-end">

                    <div class="col-12 col-sm-6 col-lg-3 mb-2">
                        <label style="font-size:.78rem;font-weight:600;color:#374151;margin-bottom:.3rem;display:block;">Vehicle Type</label>
                        <select name="vehicle_type" id="filter-type" class="form-control form-control-modern">
                            <option value="">All Types</option>
                            <?php foreach ($vehicleTypes as $type): ?>
                                <option value="<?= htmlspecialchars($type) ?>" <?= $filterType === $type ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($type) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3 mb-2">
                        <label style="font-size:.78rem;font-weight:600;color:#374151;margin-bottom:.3rem;display:block;">Fuel Type</label>
                        <select name="fuel_type" id="filter-fuel" class="form-control form-control-modern">
                            <option value="">All Fuel Types</option>
                            <?php foreach ($fuelTypes as $fuel): ?>
                                <option value="<?= htmlspecialchars($fuel) ?>" <?= $filterFuel === $fuel ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($fuel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3 mb-2">
                        <label style="font-size:.78rem;font-weight:600;color:#374151;margin-bottom:.3rem;display:block;">Color</label>
                        <input type="text" name="color" id="filter-color"
                            value="<?= htmlspecialchars($filterColor) ?>"
                            class="form-control form-control-modern"
                            placeholder="e.g. White, Red...">
                    </div>

                    <div class="col-12 col-sm-6 col-lg-2 mb-2 align-self-end">
                        <label style="font-size:.78rem;font-weight:600;color:transparent;margin-bottom:.3rem;display:block;">.</label>
                        <div style="display:flex;gap:.4rem;">
                            <button type="submit" class="btn btn-primary-modern btn-modern" style="flex:1;">
                                <svg width="15" height="15" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V15a1 1 0 01-.553.894l-4 2A1 1 0 017 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
                                </svg>
                                Apply
                            </button>
                            <?php if (!empty($activeFilters)): ?>
                                <a href="index.php?controller=vehicles" class="btn btn-modern" style="background:#f3f4f6;color:#374151;border:1px solid #d1d5db;flex-shrink:0;" title="Clear all filters">
                                    <svg width="15" height="15" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <!-- Active Filter Tags -->
                <?php if (!empty($activeFilters)): ?>
                <div class="row mt-2">
                    <div class="col-12">
                        <div style="display:flex;flex-wrap:wrap;gap:.4rem;align-items:center;">
                            <span style="font-size:.78rem;color:#6b7280;font-weight:600;">Active filters:</span>
                            <?php if ($search !== ''): ?>
                                <span style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;border-radius:999px;padding:.15rem .65rem;font-size:.78rem;">
                                    Search: "<?= htmlspecialchars($search) ?>"
                                </span>
                            <?php endif; ?>
                            <?php if ($filterType !== ''): ?>
                                <span style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;border-radius:999px;padding:.15rem .65rem;font-size:.78rem;">
                                    Type: <?= htmlspecialchars($filterType) ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($filterFuel !== ''): ?>
                                <span style="background:#fdf4ff;color:#7e22ce;border:1px solid #e9d5ff;border-radius:999px;padding:.15rem .65rem;font-size:.78rem;">
                                    Fuel: <?= htmlspecialchars($filterFuel) ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($filterColor !== ''): ?>
                                <span style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;border-radius:999px;padding:.15rem .65rem;font-size:.78rem;">
                                    Color: <?= htmlspecialchars($filterColor) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </form>
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th style="width:15%;">Plate No.</th>
                            <th style="width:20%;">Make / Model</th>
                            <th style="width:12%;">Type</th>
                            <th style="width:8%;">Year</th>
                            <th style="width:10%;">Color</th>
                            <th style="width:20%;">Owner</th>
                            <th style="width:15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($vehicles)): ?>
                            <?php foreach ($vehicles as $vehicle): ?>
                                <tr>
                                    <td>
                                        <span class="plate-badge">
                                            <?= htmlspecialchars($vehicle['plate_number'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($vehicle['make'] ?? '—', ENT_QUOTES, 'UTF-8') ?></strong>
                                        <?php if (!empty($vehicle['model'])): ?>
                                            <br><span style="font-size:.82rem;color:#6b7280;"><?= htmlspecialchars($vehicle['model'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="vehicle-type-badge">
                                            <?= htmlspecialchars($vehicle['vehicle_type'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <br>
                                        <span class="vehicle-use-badge vehicle-use-<?= strtolower($vehicle['vehicle_use'] ?? 'private') ?>">
                                            <?php if (($vehicle['vehicle_use'] ?? 'Private') === 'Public'): ?>
                                                <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24" style="display:inline-block;vertical-align:middle;">
                                                    <path d="M4 16c0 .88.39 1.67 1 2.22V20c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h8v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1.78c.61-.55 1-1.34 1-2.22V6c0-3.5-3.58-4-8-4s-8 .5-8 4v10zm3.5 1c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm9 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm1.5-6H6V6h12v5z"/>
                                                </svg>
                                                Public Transport
                                            <?php else: ?>
                                                <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24" style="display:inline-block;vertical-align:middle;">
                                                    <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99z"/>
                                                </svg>
                                                Private
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-weight:600;color:#374151;">
                                            <?= htmlspecialchars($vehicle['year'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:.4rem;">
                                            <span class="color-dot" style="background:<?= htmlspecialchars($vehicle['color_hex'] ?? '#9ca3af', ENT_QUOTES, 'UTF-8') ?>;"></span>
                                            <span style="font-size:.85rem;color:#374151;">
                                                <?= htmlspecialchars($vehicle['color'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($vehicle['owner_id']) && !empty($vehicle['owner_name'])): ?>
                                            <a href="index.php?controller=constituents&action=view&id=<?= (int)$vehicle['owner_id'] ?>"
                                                class="owner-link">
                                                <i class="fas fa-user" style="font-size:.75rem;"></i>
                                                <?= htmlspecialchars($vehicle['owner_name'], ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size:.85rem;">— Unlinked —</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-btn-group">
                                            <a href="index.php?controller=vehicles&action=view&id=<?= (int)$vehicle['id'] ?>"
                                                class="btn btn-action btn-view">
                                                <i class="fas fa-eye mr-1"></i> View
                                            </a>
                                            <a href="index.php?controller=vehicles&action=edit&id=<?= (int)$vehicle['id'] ?>"
                                                class="btn btn-action btn-edit">
                                                <i class="fas fa-pencil-alt mr-1"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-action btn-archive"
                                                data-toggle="modal"
                                                data-target="#archiveModal"
                                                data-vehicle-id="<?= (int)$vehicle['id'] ?>">
                                                <i class="fas fa-archive mr-1"></i> Archive
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <svg fill="currentColor" viewBox="0 0 24 24" width="48" height="48">
                                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                                        <circle cx="7.5" cy="14.5" r="1.5"/>
                                        <circle cx="16.5" cy="14.5" r="1.5"/>
                                    </svg>
                                    <h5>No vehicles found</h5>
                                    <p>Try adjusting your filters or <a href="index.php?controller=vehicles&action=add">register a new vehicle</a></p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-row">
                <div class="pagination-info">
                    Showing <?= count($vehicles ?? []) ?> of <?= $totalRecords ?> vehicle<?= $totalRecords !== 1 ? 's' : '' ?>
                    <?php if (!empty($activeFilters)): ?>
                        <span style="color:#6b7280;font-size:.8rem;">(filtered)</span>
                    <?php endif; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                <div class="pagination-controls">
                    <a class="page-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>"
                        href="<?= $queryBase ?>&page=1">&#171;&#171;</a>
                    <a class="page-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>"
                        href="<?= $queryBase ?>&page=<?= max(1, $currentPage - 1) ?>">&#171;</a>
                    <?php
                    $startPage = max(1, $currentPage - 2);
                    $endPage   = min($totalPages, $currentPage + 2);
                    if ($endPage - $startPage + 1 < 5) {
                        if ($startPage == 1) $endPage = min($totalPages, $startPage + 4);
                        elseif ($endPage == $totalPages) $startPage = max(1, $endPage - 4);
                    }
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <a class="page-btn <?= $i == $currentPage ? 'active' : '' ?>"
                            href="<?= $queryBase ?>&page=<?= $i ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <a class="page-btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>"
                        href="<?= $queryBase ?>&page=<?= min($totalPages, $currentPage + 1) ?>">&#187;</a>
                    <a class="page-btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>"
                        href="<?= $queryBase ?>&page=<?= $totalPages ?>">&#187;&#187;</a>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<!-- Archive Confirmation Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <svg width="20" height="20" fill="white" viewBox="0 0 20 20" style="display:inline-block;margin-right:.5rem;">
                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                        <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Confirm Archive
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to archive this vehicle?</p>
                <div class="alert alert-warning mb-0" role="alert">
                    <strong>Note:</strong> The vehicle will be archived and can be restored later if needed.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="#" id="archiveConfirmBtn" class="btn btn-danger">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="display:inline-block;margin-right:.25rem;">
                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                        <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Archive
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.vehicles-header {
    background: linear-gradient(135deg, #4361ee 0%, #3651d4 60%, #2c46d4 100%) !important;
}

.plate-badge {
    display: inline-block;
    background: #1e293b;
    color: #f1f5f9;
    font-family: 'Courier New', Courier, monospace;
    font-size: .82rem;
    font-weight: 700;
    letter-spacing: .08em;
    padding: .22rem .6rem;
    border-radius: 5px;
    border: 2px solid #334155;
    white-space: nowrap;
}

.vehicle-type-badge {
    display: inline-block;
    background: #e0f2fe;
    color: #0369a1;
    font-size: .75rem;
    font-weight: 600;
    padding: .2rem .55rem;
    border-radius: 999px;
    border: 1px solid #bae6fd;
    white-space: nowrap;
}

.color-dot {
    display: inline-block;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 1.5px solid rgba(0,0,0,.15);
    flex-shrink: 0;
}

.owner-link {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    color: #1d4ed8;
    font-size: .85rem;
    font-weight: 600;
    text-decoration: none;
    transition: color .15s;
}

.owner-link:hover {
    color: #1e40af;
    text-decoration: underline;
}
.vehicle-use-badge {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    font-size: .68rem;
    font-weight: 600;
    padding: .15rem .45rem;
    border-radius: 999px;
    margin-top: .25rem;
    white-space: nowrap;
}

.vehicle-use-private {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
}

.vehicle-use-public {
    background: #fef9c3;
    color: #854d0e;
    border: 1px solid #fde68a;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Wire up archive modal ──
    if (typeof $ !== 'undefined') {
        $('#archiveModal').on('show.bs.modal', function (e) {
            var btn       = $(e.relatedTarget);
            var vehicleId = btn.data('vehicle-id');
            $('#archiveConfirmBtn').attr('href', 'index.php?controller=vehicles&action=delete&id=' + vehicleId);
        });
    }

    // ── Auto-dismiss alerts ──
    setTimeout(function () {
        ['success-alert', 'error-alert'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.style.transition = 'opacity .5s';
                el.style.opacity = '0';
                setTimeout(function () { el.remove(); }, 500);
            }
        });
    }, 4000);

    // ── Debounced auto-search ──
    var searchTimer;
    var searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                document.getElementById('filter-form').submit();
            }, 400);
        });
    }
    // ── Auto-submit on filter dropdown change ──
    ['filter-type', 'filter-fuel'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', function () {
            document.getElementById('filter-form').submit();
        });
    });
    // ── Debounced auto-submit on color text field ──
    var colorTimer;
    var colorInput = document.getElementById('filter-color');
    if (colorInput) {
        colorInput.addEventListener('input', function () {
            clearTimeout(colorTimer);
            colorTimer = setTimeout(function () {
                document.getElementById('filter-form').submit();
            }, 400);
        });
    }
});
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
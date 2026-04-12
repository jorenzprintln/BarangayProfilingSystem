<?php
ob_start();

$currentPage        = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage            = 10;
$totalRecords       = count($archivedVehicles ?? []);
$totalPages         = $totalRecords > 0 ? ceil($totalRecords / $perPage) : 1;
$offset             = ($currentPage - 1) * $perPage;
$paginatedVehicles  = array_slice($archivedVehicles ?? [], $offset, $perPage);
$queryBase          = 'index.php?controller=vehicles&action=archivedVehicles'
    . '&search=' . urlencode($search ?? '');
?>

<link rel="stylesheet" href="public/assets/css/constituents_index.css">
<link rel="stylesheet" href="public/assets/css/constituents_archived.css">

<div class="container-fluid px-4 mt-3">
    <div class="content-wrapper">

        <div style="display:flex; margin-bottom:1.5rem;">
            <a href="index.php?controller=vehicles" class="btn-back">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
                </svg>
                Back to Vehicle Registry
            </a>
        </div>

        <?php if (Session::hasFlash('success')): ?>
            <div class="alert alert-dismissible fade show" role="alert"
                style="border-radius:.75rem;border:none;border-left:4px solid #06d6a0;background:#d1fae5;color:#065f46;margin-bottom:1rem;">
                <strong>Success!</strong> <?= Session::getFlash('success') ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="page-header" style="background:linear-gradient(135deg,#4361ee 0%,#3651d4 60%,#2c46d4 100%);">
            <div class="page-title">
                <svg fill="white" viewBox="0 0 20 20" width="28" height="28">
                    <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                    <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="font-weight-bold mb-0">Archived Vehicles</h3>
                    <p class="mb-0 mt-1" style="opacity:0.9;font-size:0.9rem;">View and restore archived vehicles</p>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div style="margin-bottom:1.5rem;">
            <form method="GET" action="index.php" id="search-form">
                <input type="hidden" name="controller" value="vehicles">
                <input type="hidden" name="action" value="archivedVehicles">
                <div class="search-wrapper" style="max-width:350px;margin-left:auto;">
                    <svg class="search-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                    </svg>
                    <input type="text" id="search-input" name="search"
                        value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="form-control-modern"
                        placeholder="Search by plate, make, or owner...">
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Plate No.</th>
                            <th>Make / Model</th>
                            <th>Type</th>
                            <th>Year</th>
                            <th>Owner</th>
                            <th>Archived At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($paginatedVehicles)): ?>
                            <?php foreach ($paginatedVehicles as $vehicle): ?>
                                <tr>
                                    <td>
                                        <span style="display:inline-block;background:#1e293b;color:#f1f5f9;font-family:'Courier New',monospace;font-size:.82rem;font-weight:700;letter-spacing:.08em;padding:.22rem .6rem;border-radius:5px;border:2px solid #334155;white-space:nowrap;">
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
                                        <span style="display:inline-block;background:#e0f2fe;color:#0369a1;font-size:.75rem;font-weight:600;padding:.2rem .55rem;border-radius:999px;border:1px solid #bae6fd;white-space:nowrap;">
                                            <?= htmlspecialchars($vehicle['vehicle_type'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-weight:600;color:#374151;">
                                            <?= htmlspecialchars($vehicle['year'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($vehicle['owner_name'])): ?>
                                            <span style="font-size:.85rem;color:#374151;font-weight:600;">
                                                <i class="fas fa-user" style="font-size:.75rem;color:#6b7280;margin-right:.25rem;"></i>
                                                <?= htmlspecialchars($vehicle['owner_name'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size:.85rem;">— Unlinked —</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $archivedAt = $vehicle['deleted_at'] ?? '';
                                        if ($archivedAt) {
                                            try {
                                                $dt = new DateTime($archivedAt);
                                                echo '<div style="line-height:1.4;">';
                                                echo '<div style="font-weight:500;color:#6b7280;">' . $dt->format('M. j, Y') . '</div>';
                                                echo '<div style="font-size:0.82rem;color:#9ca3af;margin-top:2px;">' . $dt->format('g:i A') . '</div>';
                                                echo '</div>';
                                            } catch (Exception $e) {
                                                echo htmlspecialchars($archivedAt, ENT_QUOTES, 'UTF-8');
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn-restore"
                                            data-toggle="modal"
                                            data-target="#restoreModal"
                                            data-vehicle-id="<?= (int)$vehicle['id'] ?>"
                                            data-vehicle-label="<?= htmlspecialchars(($vehicle['plate_number'] ?? '') . ' — ' . ($vehicle['make'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                            </svg>
                                            Restore
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <svg fill="currentColor" viewBox="0 0 24 24" width="48" height="48">
                                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                                            <circle cx="7.5" cy="14.5" r="1.5"/>
                                            <circle cx="16.5" cy="14.5" r="1.5"/>
                                        </svg>
                                        <h5>No archived vehicles</h5>
                                        <p>There are no archived vehicles at this time</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-row">
                <div class="pagination-info">
                    Showing <?= count($paginatedVehicles) ?> of <?= $totalRecords ?> archived vehicle<?= $totalRecords !== 1 ? 's' : '' ?>
                </div>
                <div class="pagination-controls">
                    <?php if ($totalPages > 1): ?>
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
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Restore Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <svg width="20" height="20" fill="white" viewBox="0 0 20 20" style="display:inline-block;margin-right:.5rem;">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                    </svg>
                    Confirm Restore
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to restore: <strong id="restoreVehicleLabel"></strong>?</p>
                <div class="alert alert-info mb-0" role="alert"
                    style="background:#dbeafe;color:#1e40af;border-left:4px solid #3b82f6;border-radius:.5rem;">
                    <strong>Note:</strong> This vehicle will be moved back to the active vehicle registry.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="#" id="restoreConfirmBtn" class="btn btn-success">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="display:inline-block;margin-right:.25rem;">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                    </svg>
                    Restore
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Search on enter
    var searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                document.getElementById('search-form').submit();
            }
        });
    }

    // Wire up restore modal
    if (typeof $ !== 'undefined') {
        $('#restoreModal').on('show.bs.modal', function (e) {
            var btn   = $(e.relatedTarget);
            var id    = btn.data('vehicle-id');
            var label = btn.data('vehicle-label');
            $('#restoreVehicleLabel').text(label);
            $('#restoreConfirmBtn').attr('href', 'index.php?controller=vehicles&action=restoreVehicle&id=' + id);
        });
    }
});
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
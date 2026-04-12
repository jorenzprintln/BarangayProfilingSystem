<?php
$content = ob_start();

// ── Pull values passed from controller ──
$currentPage  = $currentPage  ?? 1;
$totalPages   = $totalPages   ?? 1;
$totalRecords = $totalRecords ?? 0;
$search       = $search       ?? '';

$queryBase = 'index.php?controller=households&search=' . urlencode($search);
?>

<link rel="stylesheet" href="public/assets/css/households.css">

<div class="container-fluid px-4 mt-3">

    <!-- Flash Messages -->
    <?php if (Session::hasFlash('success')): ?>
        <div id="flash-success" class="alert alert-success-modern alert-modern alert-dismissible fade show" role="alert">
            <strong>Success!</strong> <?= Session::getFlash('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (Session::hasFlash('error')): ?>
        <div id="flash-error" class="alert alert-danger-modern alert-modern alert-dismissible fade show" role="alert">
            <strong>Error!</strong> <?= Session::getFlash('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <svg fill="white" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
            <div>
                <h3 class="font-weight-bold mb-0"><?= $title ?></h3>
                <p class="mb-0 mt-1" style="opacity: 0.9; font-size: 0.9rem;">Manage and view all registered households</p>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="controls-container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-6 mb-3 mb-lg-0">
                <div class="search-wrapper">
                    <svg class="search-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                    </svg>
                    <form method="GET" action="index.php" id="search-form">
                        <input type="hidden" name="controller" value="households">
                        <input type="hidden" name="page" value="1"> <!-- reset to page 1 on new search -->
                        <input type="text" id="search-input" name="search"
                            value="<?= htmlspecialchars($search) ?>"
                            class="form-control form-control-modern"
                            placeholder="Search households...">
                    </form>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 d-flex justify-content-md-end">
                <button type="button" class="btn btn-primary-modern btn-modern" data-toggle="modal" data-target="#createHouseholdModal">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Add Household
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <div class="table-wrapper" id="households-table">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th style="width:20%;">Household No.</th>
                        <th style="width:30%;">Head of Household</th>
                        <th style="width:15%;">Families</th>
                        <th style="width:15%;">Members</th>
                        <th style="width:20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($households)): ?>
                        <?php foreach ($households as $household): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($household['household_number'] ?? '') ?></strong></td>
                                <td><?= htmlspecialchars($household['full_name'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="badge-count"><?= htmlspecialchars($household['total_families'] ?? '0') ?></span>
                                </td>
                                <td>
                                    <span class="badge-count"><?= htmlspecialchars($household['total_members'] ?? '0') ?></span>
                                </td>
                                <td>
                                    <div class="action-btn-group">
                                        <a href="index.php?controller=households&action=view&household_id=<?= htmlspecialchars($household['id'] ?? '') ?>"
                                            class="btn btn-action btn-view">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                        <?php if ((int)($household['total_members'] ?? 0) === 0): ?>
                                            <button type="button" class="btn btn-action btn-delete"
                                                data-toggle="modal"
                                                data-target="#deleteConfirmModal"
                                                data-household-id="<?= htmlspecialchars($household['id'] ?? '') ?>">
                                                <i class="fas fa-trash-alt mr-1"></i> Delete
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                <h5>No households found</h5>
                                <p>Add your first household to get started</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Showing X of Y + Pagination -->
        <div class="pagination-row">
            <div class="pagination-info">
                Showing <?= count($households) ?> of <?= $totalRecords ?> household<?= $totalRecords !== 1 ? 's' : '' ?>
                <?php if ($search !== ''): ?>
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">
                        <svg width="20" height="20" fill="white" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Confirm Deletion
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this household?</p>
                    <div class="alert alert-warning mb-0" role="alert">
                        <strong>Warning:</strong> This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger" style="border-radius:.5rem; font-weight:600; padding:.625rem 1.5rem;">
                        Delete
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Create Household Modal -->
<div class="modal fade" id="createHouseholdModal" tabindex="-1" role="dialog" aria-labelledby="createHouseholdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="createHouseholdModalLabel">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Create Household
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <?php if (isset($formSubmitted) && isset($error)): ?>
                    <div class="alert" style="border-radius:.5rem;border:none;border-left:4px solid var(--danger-color);background:#fef2f2;color:#991b1b;margin-bottom:1rem;">
                        <strong>Error!</strong> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form id="createHouseholdForm" action="index.php?controller=households&action=store" method="POST" novalidate>
                    <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">

                    <!-- Household ID -->
                    <div class="modal-section">
                        <span class="modal-sublabel">Household Identification</span>
                        <div class="form-group mb-0">
                            <label for="household_number">Household Number</label>
                            <input type="text" inputmode="numeric" class="form-control"
                                id="household_number" name="household_number"
                                placeholder="e.g. 20240001"
                                value="<?= htmlspecialchars($formData['household_number'] ?? '') ?>"
                                maxlength="24">
                            <div class="error-msg" id="household_number_error">
                                Household number must contain numbers only and not exceed 24 digits.
                            </div>
                        </div>
                    </div>

                    <!-- Hidden location fields -->
                    <input type="hidden" name="region"            value="REGION VIII">
                    <input type="hidden" name="province"          value="LEYTE">
                    <input type="hidden" name="city_municipality" value="TACLOBAN CITY">
                    <input type="hidden" name="zip_code"          value="<?= htmlspecialchars($formData['zip_code'] ?? '6500') ?>">
                    <input type="hidden" name="barangay_code"     value="<?= isset($formData['barangay_code']) ? htmlspecialchars($formData['barangay_code']) : '36-A' ?>">
                    <input type="hidden" name="barangay_name"     value="<?= htmlspecialchars($formData['barangay_name'] ?? 'IMELDA VILLAGE') ?>">

                    <!-- Address -->
                    <div class="modal-section">
                        <span class="modal-sublabel">Street &amp; Unit Details</span>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="zone">Purok / Zone <span class="req">*</span></label>
                                    <input type="text" class="form-control" id="zone" name="zone" required
                                        placeholder="e.g. Purok 3"
                                        value="<?= htmlspecialchars($formData['purok'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="street_name">Street Name</label>
                                    <input type="text" class="form-control" id="street_name" name="street_name"
                                        placeholder="e.g. Magsaysay Blvd."
                                        value="<?= htmlspecialchars($formData['street_name'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <hr class="modal-divider">

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label for="block_no">Block No.</label>
                                    <input type="text" class="form-control" id="block_no" name="block_no"
                                        placeholder="e.g. 4"
                                        value="<?= htmlspecialchars($formData['block_number'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label for="lot_no">Lot No.</label>
                                    <input type="text" class="form-control" id="lot_no" name="lot_no"
                                        placeholder="e.g. 12"
                                        value="<?= htmlspecialchars($formData['lot_number'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label for="house_bldg_no">House / Bldg No.</label>
                                    <input type="text" class="form-control" id="house_bldg_no" name="house_bldg_no"
                                        placeholder="e.g. 25"
                                        value="<?= htmlspecialchars($formData['house_building_number'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label for="unit_no">Unit No.</label>
                                    <input type="text" class="form-control" id="unit_no" name="unit_no"
                                        placeholder="e.g. 2B"
                                        value="<?= htmlspecialchars($formData['unit_number'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-create" id="submitHouseholdForm">
                    Create Household
                </button>
            </div>

        </div>
    </div>
</div>

<!-- PHP flag passed to JS for re-opening modal on error -->
<script>
    window.HOUSEHOLDS_OPEN_MODAL = <?= json_encode(isset($formSubmitted) && isset($error)) ?>;
</script>

<script src="public/assets/js/households.js"></script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
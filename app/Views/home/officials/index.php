<?php
$content = ob_start();
?>

<link rel="stylesheet" href="public/assets/css/officials.css">

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
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
            </svg>
            <div>
                <h3 class="font-weight-bold mb-0"><?= $title ?></h3>
                <p class="mb-0 mt-1" style="opacity: 0.9; font-size: 0.9rem;">Manage and view all registered barangay officials</p>
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
                    <input type="text" id="search-input" class="form-control form-control-modern" placeholder="Search officials...">
                </div>
            </div>
            <div class="col-lg-6 col-md-6 d-flex justify-content-md-end">
                <button type="button" class="btn btn-primary-modern btn-modern" data-toggle="modal" data-target="#addOfficialModal">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Add Barangay Official
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <div class="table-wrapper" id="officials-table-wrapper">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th style="width:40%;">Full Name</th>
                        <th style="width:30%;">Position</th>
                        <th style="width:20%;">Actions</th>
                    </tr>
                </thead>
                <tbody id="officials-table-body">
                    <?php if (!empty($officials)): ?>
                        <?php foreach ($officials as $official): ?>
                            <tr>
                                <td><?= htmlspecialchars($official['full_name']) ?></td>
                                <td>
                                    <?php
                                        $role = strtolower($official['role']);
                                        $badgeClass = 'role-konsehal';
                                        if (str_contains($role, 'punong')) $badgeClass = 'role-punong';
                                        elseif (str_contains($role, 'secretary')) $badgeClass = 'role-secretary';
                                        elseif (str_contains($role, 'treasurer')) $badgeClass = 'role-treasurer';
                                    ?>
                                    <span class="role-badge <?= $badgeClass ?>">
                                        <?= htmlspecialchars($official['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btn-group">
                                        <button type="button" class="btn btn-action btn-delete delete-btn"
                                            data-id="<?= $official['id'] ?>"
                                            data-name="<?= htmlspecialchars($official['full_name']) ?>"
                                            data-toggle="modal"
                                            data-target="#deleteModal">
                                            <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Remove
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="empty-state">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                                </svg>
                                <h5>No officials found</h5>
                                <p>Add your first barangay official to get started</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <svg width="20" height="20" fill="white" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Confirm Removal
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to remove <strong><span id="officialName"></span></strong> as a barangay official?</p>
                    <div class="alert alert-warning mb-0" role="alert" style="border-radius:.5rem;">
                        <strong>Warning:</strong> This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger" style="border-radius:.5rem; font-weight:600; padding:.625rem 1.5rem; display:inline-flex; align-items:center; gap:.4rem;">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Remove
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Add Official Modal -->
<div class="modal fade" id="addOfficialModal" tabindex="-1" role="dialog" aria-labelledby="addOfficialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-50w" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addOfficialModalLabel">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                    </svg>
                    Add Barangay Official
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="padding: 1.5rem 2rem 1rem;">
                <div style="display: flex; gap: 1.25rem;">

                    <!-- Left: Available Constituents -->
                    <div class="modal-panel">
                        <h6>Available Constituents</h6>

                        <div class="modal-search-wrapper">
                            <svg class="search-icon" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                            </svg>
                            <input type="text" id="modal-search-input" placeholder="Search constituents...">
                        </div>

                        <div class="modal-table-scroll">
                            <table class="table modal-inner-table">
                                <thead>
                                    <tr>
                                        <th>Full Name</th>
                                        <th style="width:80px; text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="constituents-table">
                                    <?php foreach ($constituents as $constituent): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($constituent['full_name']) ?></td>
                                            <td style="text-align:center;">
                                                <button type="button" class="btn-select select-btn"
                                                    data-id="<?= $constituent['id'] ?>"
                                                    data-name="<?= htmlspecialchars($constituent['full_name']) ?>">
                                                    Select
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Right: Selected Constituents -->
                    <div class="modal-panel">
                        <h6>Selected Officials</h6>
                        <form id="appointForm" method="POST" action="index.php?controller=officials&action=create">
                            <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                            <div id="selected-constituents" class="modal-table-scroll" style="border:none; padding: 0 0 0.5rem;">
                                <p id="empty-selection-msg" class="text-center" style="color:#a0aec0; font-size:0.85rem; padding-top:2rem;">
                                    No officials selected yet.<br>Select from the list on the left.
                                </p>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-create" id="appointBtn">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                    </svg>
                    Appoint Officials
                </button>
            </div>

        </div>
    </div>
</div>

<!-- PHP variable passed to JS -->
<script>
    const HAS_PUNONG_BARANGAY = <?= json_encode($hasPunongBarangay ?? false) ?>;
</script>

<script src="public/assets/js/officials.js"></script>

<?php
$content = ob_get_clean();
require_once('app/Views/layouts/main.php');
?>
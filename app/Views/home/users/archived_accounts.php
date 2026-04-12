<?php
ob_start();

$queryBase = 'index.php?controller=users&action=archivedAccounts&search=' . urlencode($search ?? '');
?>

<link rel="stylesheet" href="public/assets/css/manage_users.css?v=<?= time() ?>">
<link rel="stylesheet" href="public/assets/css/archived_accounts.css?v=<?= time() ?>">

<div class="container-fluid px-4 mt-3">

    <!-- Back Button -->
    <div style="display:flex; margin-bottom:1.5rem;">
        <a href="index.php?controller=users&tab=constituent" class="back-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Back to Accounts
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (Session::hasFlash('success')): ?>
        <div class="alert alert-dismissible fade show" role="alert"
            style="border-radius:.75rem;border:none;border-left:4px solid #06d6a0;background:#d1fae5;color:#065f46;margin-bottom:1rem;">
            <strong>Success!</strong> <?= Session::getFlash('success') ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <?php if (Session::hasFlash('error')): ?>
        <div class="alert alert-dismissible fade show" role="alert"
            style="border-radius:.75rem;border:none;border-left:4px solid #ef476f;background:#fee2e2;color:#991b1b;margin-bottom:1rem;">
            <strong>Error!</strong> <?= Session::getFlash('error') ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="page-header" style="background:linear-gradient(135deg,#6b7280 0%,#4b5563 100%);">
        <div class="page-title">
            <svg fill="white" viewBox="0 0 20 20">
                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="font-weight-bold mb-0">Rejected Accounts</h3>
                <p class="mb-0 mt-1" style="opacity:0.9;font-size:0.875rem;">Manage rejected constituent accounts — approve or permanently remove</p>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div style="margin-bottom:1.5rem;">
        <form method="GET" action="index.php" id="search-form">
            <input type="hidden" name="controller" value="users">
            <input type="hidden" name="action" value="archivedAccounts">
            <div class="search-wrapper" style="max-width:350px;margin-left:auto;">
                <svg class="search-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
                <input type="text" id="search-input" name="search"
                    value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control form-control-modern"
                    placeholder="Search rejected accounts...">
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-container">
        <div class="table-wrapper">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th style="width:25%;">Username</th>
                        <th style="width:30%;">Full Name</th>
                        <th style="width:15%;">Status</th>
                        <th style="width:30%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rejectedAccounts)): ?>
                        <?php foreach ($rejectedAccounts as $rUser): ?>
                            <tr>
                                <td>
                                    <div class="user-info-cell">
                                        <div class="user-avatar" style="background:linear-gradient(135deg,#ef476f,#d63384);">
                                            <?= strtoupper(substr($rUser['fullname'] ?: $rUser['username'], 0, 1)) ?>
                                        </div>
                                        <span class="user-name"><?= htmlspecialchars($rUser['username']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($rUser['fullname'] ?? '—') ?></td>
                                <td>
                                    <span class="status-badge status-rejected">Rejected</span>
                                </td>
                                <td>
                                    <div class="action-btn-group">
                                        <button type="button" class="btn btn-action btn-reapprove reapprove-btn"
                                            data-id="<?= $rUser['id'] ?>"
                                            data-name="<?= htmlspecialchars($rUser['fullname'] ?: $rUser['username'], ENT_QUOTES) ?>"
                                            data-toggle="modal"
                                            data-target="#reApproveModal">
                                            <i class="fas fa-check-circle"></i>
                                            Approve
                                        </button>
                                        <button type="button" class="btn btn-action btn-remove remove-btn"
                                            data-id="<?= $rUser['id'] ?>"
                                            data-name="<?= htmlspecialchars($rUser['fullname'] ?: $rUser['username'], ENT_QUOTES) ?>"
                                            data-toggle="modal"
                                            data-target="#removeModal">
                                            <i class="fas fa-trash-alt"></i>
                                            Remove
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                                        <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <h5>No rejected accounts</h5>
                                    <p>There are no rejected accounts at this time</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination-row">
                <div class="pagination-info">
                    Showing <?= (($currentPage - 1) * 10) + 1 ?>–<?= min($currentPage * 10, $totalRecords) ?> of <?= $totalRecords ?> rejected accounts
                </div>
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
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Approve Modal -->
<div class="modal fade" id="reApproveModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content approve-modal-content">
            <form method="POST" action="index.php?controller=users&action=reApprove" id="reApproveForm">
                <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                <input type="hidden" name="id" id="reApproveId" value="">
                <div class="modal-header" style="background:#065f46;color:#fff;border-radius:.75rem .75rem 0 0;">
                    <h5 class="modal-title">
                        <i class="fas fa-user-check mr-2"></i>
                        Approve Account
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:.9;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Approve the account for <strong id="reApproveName"></strong>?</p>

                    <div class="form-group">
                        <label class="font-weight-bold" style="font-size:0.85rem; color:#374151;">
                            <i class="fas fa-link mr-1 text-primary"></i>
                            Link to Constituent Record <small class="text-muted">(optional)</small>
                        </label>
                        <select name="constituent_id" class="form-control selectpicker"
                                data-live-search="true"
                                data-size="8"
                                data-none-selected-text="— No link (approve only) —"
                                title="— No link (approve only) —">
                            <?php foreach ($unlinkedConstituents ?? [] as $c): ?>
                                <?php
                                    $cName = $c['last_name'] . ', ' . $c['first_name'];
                                    if (!empty($c['middle_name'])) $cName .= ' ' . substr($c['middle_name'], 0, 1) . '.';
                                    if (!empty($c['suffix'])) $cName .= ' ' . $c['suffix'];
                                ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($cName) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-info-circle mr-1"></i>
                            Optionally link to an existing constituent record.
                        </small>
                    </div>

                    <div class="alert alert-info mb-0" role="alert"
                        style="background:#dbeafe;color:#1e40af;border-left:4px solid #3b82f6;border-radius:0.5rem;">
                        <strong>Note:</strong> This account will be moved to the active constituent accounts list.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" style="border-radius:.5rem;font-weight:600;padding:.625rem 1.5rem;display:inline-flex;align-items:center;gap:.4rem;">
                        <i class="fas fa-check"></i> Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Remove Modal -->
<div class="modal fade" id="removeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#991b1b;color:#fff;border-radius:.75rem .75rem 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Permanently Remove Account
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <strong>permanently remove</strong> the account for <strong id="removeName"></strong>?</p>
                <div class="alert alert-danger mb-0" role="alert" style="border-radius:.5rem;">
                    <strong>Warning:</strong> This action cannot be undone. The account will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                <form method="POST" action="index.php?controller=users&action=removeRejected" style="display:inline;">
                    <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                    <input type="hidden" name="id" id="removeRejectedId" value="">
                    <button type="submit" id="confirmRemove" class="btn btn-danger" style="border-radius:.5rem;font-weight:600;padding:.625rem 1.5rem;display:inline-flex;align-items:center;gap:.4rem;">
                        <i class="fas fa-trash-alt"></i>
                        Remove Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Re-approve modal
$(document).on('click', '.reapprove-btn', function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    $('#reApproveId').val(id);
    $('#reApproveName').text(name);
});

// Remove modal
$(document).on('click', '.remove-btn', function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    $('#removeName').text(name);
    $('#removeRejectedId').val(id);
});

// Initialize selectpicker when re-approve modal is shown
$('#reApproveModal').on('shown.bs.modal', function() {
    $(this).find('.selectpicker').selectpicker('refresh');
});

// Search on enter
$('#search-input').on('keypress', function(e) {
    if (e.which === 13) {
        $('#search-form').submit();
    }
});

// Auto-dismiss flash messages
setTimeout(function() {
    $('.alert-dismissible').fadeOut(500);
}, 4000);
</script>

<?php
$content = ob_get_clean();
require_once('app/Views/layouts/main.php');
?>

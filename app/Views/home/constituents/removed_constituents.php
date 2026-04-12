<?php
ob_start();

$currentPage           = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage               = 10;
$totalRecords          = count($removedConstituents ?? []);
$totalPages            = $totalRecords > 0 ? ceil($totalRecords / $perPage) : 1;
$offset                = ($currentPage - 1) * $perPage;
$paginatedConstituents = array_slice($removedConstituents ?? [], $offset, $perPage);
$queryBase = 'index.php?controller=constituents&action=removedConstituents'
    . '&search=' . urlencode($search ?? '');
?>

<link rel="stylesheet" href="public/assets/css/constituents_archived.css">

<div class="container-fluid px-4 mt-3">
    <div class="content-wrapper">

        <div style="display:flex; margin-bottom:1.5rem;">
            <a href="index.php?controller=constituents&action=index" class="btn-back">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
                </svg>
                Back to List
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
        <div class="page-header">
            <div class="page-title">
                <svg fill="white" viewBox="0 0 20 20">
                    <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                    <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="font-weight-bold mb-0">Archived Constituents</h3>
                    <p class="mb-0 mt-1" style="opacity:0.9;font-size:0.9rem;">View and restore removed constituents</p>
                </div>
            </div>
        </div>

        <div style="margin-bottom:1.5rem;">
            <form method="GET" action="index.php" id="search-form">
                <input type="hidden" name="controller" value="constituents">
                <input type="hidden" name="action" value="removedConstituents">
                <div class="search-wrapper" style="max-width:350px;margin-left:auto;">
                    <svg class="search-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                    </svg>
                    <input type="text" id="search-input" name="search"
                        value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="form-control-modern" placeholder="Search by name...">
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Sex</th>
                            <th>Age</th>
                            <th>Voter</th>
                            <th>Removed At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($paginatedConstituents)): ?>
                            <?php foreach ($paginatedConstituents as $constituent): ?>
                                <?php
                                $fullName = trim(
                                    htmlspecialchars($constituent['first_name'], ENT_QUOTES, 'UTF-8') . ' ' .
                                    htmlspecialchars($constituent['middle_name'] ?? '', ENT_QUOTES, 'UTF-8') . ' ' .
                                    htmlspecialchars($constituent['last_name'], ENT_QUOTES, 'UTF-8') . ' ' .
                                    htmlspecialchars($constituent['suffix'] ?? '', ENT_QUOTES, 'UTF-8')
                                );
                                ?>
                                <tr>
                                    <td><strong><?= $fullName ?></strong></td>
                                    <td><?= htmlspecialchars($constituent['sex'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <?php
                                        if (!empty($constituent['birthdate'])) {
                                            try {
                                                $age = (new DateTime())->diff(new DateTime($constituent['birthdate']))->y;
                                                echo '<strong>' . $age . '</strong> years';
                                            } catch (Exception $e) {
                                                echo '<span class="text-muted">Invalid Date</span>';
                                            }
                                        } else {
                                            echo '<span class="text-muted">N/A</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $voter      = htmlspecialchars($constituent['registered_voter'] ?? '', ENT_QUOTES, 'UTF-8');
                                        $badgeClass = $voter === 'YES' ? 'badge-yes' : 'badge-no';
                                        echo '<span class="badge-modern ' . $badgeClass . '">' . $voter . '</span>';
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $removedAt = $constituent['removed_at'] ?? '';
                                        if ($removedAt) {
                                            try {
                                                $dt = new DateTime($removedAt);
                                                echo '<div style="line-height:1.4;">';
                                                echo '<div style="font-weight:500;color:#6b7280;">' . htmlspecialchars($dt->format('M. j, Y'), ENT_QUOTES, 'UTF-8') . '</div>';
                                                echo '<div style="font-size:0.82rem;color:#9ca3af;margin-top:2px;">' . htmlspecialchars($dt->format('g:i A'), ENT_QUOTES, 'UTF-8') . '</div>';
                                                echo '</div>';
                                            } catch (Exception $e) {
                                                echo htmlspecialchars($removedAt, ENT_QUOTES, 'UTF-8');
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn-restore"
                                            data-toggle="modal"
                                            data-target="#restoreModal"
                                            data-constituent-id="<?= $constituent['id'] ?>"
                                            data-constituent-name="<?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>">
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
                                <td colspan="6">
                                    <div class="empty-state">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                                            <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm9 3a1 1 0 10-2 0v.01a1 1 0 102 0V11zm-4 0a1 1 0 10-2 0v.01a1 1 0 102 0V11z" clip-rule="evenodd"/>
                                        </svg>
                                        <h5>No archived constituents</h5>
                                        <p>There are no removed constituents at this time</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination-row">
                <div class="pagination-info">
                    Showing <?= count($paginatedConstituents) ?> of <?= $totalRecords ?> archived constituents
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
<div class="modal fade" id="restoreModal" tabindex="-1" role="dialog" aria-labelledby="restoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="restoreModalLabel">
                    <svg width="20" height="20" fill="white" viewBox="0 0 20 20" style="display:inline-block;margin-right:0.5rem;">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                    </svg>
                    Confirm Restore
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to restore <strong id="constituentName"></strong>?</p>
                <div class="alert alert-info mb-0" role="alert"
                    style="background:#dbeafe;color:#1e40af;border-left:4px solid #3b82f6;border-radius:0.5rem;">
                    <strong>Note:</strong> This constituent will be moved back to the active constituents list.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="#" id="restoreConfirmBtn" class="btn btn-success">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="display:inline-block;margin-right:0.25rem;">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                    </svg>
                    Restore
                </a>
            </div>
        </div>
    </div>
</div>

<script src="public/assets/js/constituents_archived.js?v=<?= time() ?>"></script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
<?php
ob_start();

// ── Pagination & Server-side search setup ──
$currentPage  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage      = 10;
$totalRecords = count($ofwConstituents ?? []);
$totalPages   = $totalRecords > 0 ? ceil($totalRecords / $perPage) : 1;
$offset       = ($currentPage - 1) * $perPage;
$paginatedOfw = array_slice($ofwConstituents ?? [], $offset, $perPage);
$queryBase = 'index.php?controller=forms&action=bcOfwEntry&search=' . urlencode($search ?? '');
?>

<link rel="stylesheet" href="public/assets/css/bc_ofw_entry.css?v=<?= filemtime('public/assets/css/bc_ofw_entry.css') ?>">

<div class="container-fluid px-4 mt-3">

    <!-- Back Button -->
    <a href="index.php?controller=home&action=forms" class="back-link">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        Back to Forms
    </a>

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title-icon">
            <svg width="22" height="22" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <h3>Certification for OFW Residency</h3>
            <p>Select an OFW constituent below to generate the certificate</p>
        </div>
    </div>

    <!-- Search + Count -->
    <form method="GET" action="index.php" id="search-form">
        <input type="hidden" name="controller" value="forms">
        <input type="hidden" name="action" value="bcOfwEntry">
        <div class="controls-container">
            <div class="search-wrapper">
                <svg class="search-icon" width="15" height="15" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
                <input type="text"
                       id="searchInput"
                       name="search"
                       value="<?= htmlspecialchars($search ?? '') ?>"
                       placeholder="Search by name..."
                       autocomplete="off">
            </div>
            <span class="record-count" id="recordCount">
                <?= $totalRecords ?> OFW constituent<?= $totalRecords !== 1 ? 's' : '' ?>
                <?= !empty($search) ? '- filtered' : '' ?>
            </span>
        </div>
    </form>

    <!-- Table -->
    <div class="table-container">
        <div class="table-wrapper">
            <table class="table-modern" id="ofwTable">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($paginatedOfw)): ?>
                        <?php foreach ($paginatedOfw as $ofw): ?>
                            <?php if (empty($ofw['full_name'])) continue; ?>
                            <?php $fullName = htmlspecialchars($ofw['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            <tr>
                                <td style="font-weight:600; color:#2d3748;">
                                    <?= $fullName ?: '<span style="color:#94a3b8;font-style:italic;">No name on record</span>' ?>
                                </td>
                                <td>
                                    <form method="POST"
                                        action="index.php?controller=forms&action=processBcOfwEntry"
                                        target="_blank"
                                        class="ofw-cert-form">
                                        <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                                        <input type="hidden" name="full_name" value="<?= $fullName ?>">
                                        <button type="button"
                                            class="btn-select ofw-generate-btn"
                                            data-name="<?= $fullName ?>">
                                            <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
                                            </svg>
                                            Generate
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">
                                <div class="empty-state">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                    <h6><?= !empty($search) ? 'No results found' : 'No OFW constituents found' ?></h6>
                                    <p><?= !empty($search) ? 'Try a different search term' : 'There are no constituents with OFW occupation on record' ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Row -->
        <div class="pagination-row">
            <div class="pagination-info">
                Showing <?= count($paginatedOfw) ?> of <?= $totalRecords ?> OFW constituent<?= $totalRecords !== 1 ? 's' : '' ?>
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

<!-- Confirmation Modal -->
<div class="ofw-modal-overlay" id="ofwConfirmModal" role="dialog" aria-modal="true" aria-labelledby="ofwModalTitle">
    <div class="ofw-modal">
        <div class="ofw-modal-icon">
            <svg width="26" height="26" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
            </svg>
        </div>
        <h5 id="ofwModalTitle">Generate OFW Certificate</h5>
        <p>You are about to generate a certificate for<br><strong id="ofwModalName"></strong>.<br>Do you want to proceed?</p>
        <hr class="ofw-modal-divider">
        <div class="ofw-modal-actions">
            <button type="button" class="ofw-modal-cancel" id="ofwModalCancel">Cancel</button>
            <button type="button" class="ofw-modal-confirm" id="ofwModalConfirm">
                Generate
            </button>
        </div>
    </div>
</div>

<script src="public/assets/js/bc_ofw_entry.js?v=<?= filemtime('public/assets/js/bc_ofw_entry.js') ?>"></script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
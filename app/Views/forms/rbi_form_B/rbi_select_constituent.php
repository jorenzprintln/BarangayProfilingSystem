<?php
ob_start();

$currentPage    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage        = 10;
$search         = $_GET['search'] ?? '';

// Filter BEFORE paginating
$filtered = $constituents ?? [];
if ($search !== '') {
    $filtered = array_filter($filtered, function($c) use ($search) {
        return stripos($c['first_name'] ?? '', $search) !== false
            || stripos($c['last_name']  ?? '', $search) !== false;
    });
    $filtered = array_values($filtered); // re-index
}

$totalRecords   = count($filtered);
$totalPages     = $totalRecords > 0 ? ceil($totalRecords / $perPage) : 1;
$offset         = ($currentPage - 1) * $perPage;
$paginated      = array_slice($filtered, $offset, $perPage);
$queryBase      = 'index.php?controller=home&action=rbiBSelectConstituent'
                . '&search=' . urlencode($search);
?>

<link rel="stylesheet" href="public/assets/css/rbi_form_b_select.css?v=<?= filemtime('public/assets/css/rbi_form_b_select.css') ?>">

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
            <svg fill="white" viewBox="0 0 20 20" width="22" height="22">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <h3>RBI Form B - Select Constituent</h3>
            <p>Choose a constituent below to generate the RBI Form B</p>
        </div>
    </div>

    <!-- Search -->
    <form method="GET" action="index.php" id="searchForm">
        <input type="hidden" name="controller" value="home">
        <input type="hidden" name="action" value="rbiBSelectConstituent">
        <div class="controls-container">
            <div class="search-wrapper">
                <svg class="search-icon" width="15" height="15" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
                <input type="text" name="search" id="searchInput"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search by name..."
                    autocomplete="off">
            </div>
            <span class="record-count">
                <?= $totalRecords ?> constituent<?= $totalRecords !== 1 ? 's' : '' ?>
            </span>
        </div>
    </form>

    <!-- Table -->
    <div class="table-container">
        <div class="table-wrapper">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Birthdate</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($paginated)): ?>
                        <?php foreach ($paginated as $constituent): ?>
                            <?php
                                $firstName = htmlspecialchars($constituent['first_name']);
                                $lastName  = htmlspecialchars($constituent['last_name']);
                                $fullLabel = $firstName . ' ' . $lastName;
                                $href      = 'index.php?controller=forms&action=rbi_form_B&id=' . htmlspecialchars($constituent['id']);
                            ?>
                            <tr>
                                <td class="td-name"><?= $firstName ?></td>
                                <td><?= $lastName ?></td>
                                <td class="td-date"><?= htmlspecialchars($constituent['birthdate']) ?></td>
                                <td>
                                    <button type="button"
                                        class="btn-generate-sm rbi-generate-btn"
                                        data-name="<?= $fullLabel ?>"
                                        data-href="<?= $href ?>">
                                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
                                        </svg>
                                        Generate RBI
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                    <h6>No constituents found</h6>
                                    <p><?= $search ? 'Try a different search term' : 'No constituent records are available at this time' ?></p>
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
                Showing <?= count($paginated) ?> of <?= $totalRecords ?> constituents
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
<div class="rbi-modal-overlay" id="rbiConfirmModal" role="dialog" aria-modal="true" aria-labelledby="rbiModalTitle">
    <div class="rbi-modal">
        <div class="rbi-modal-icon">
            <svg width="26" height="26" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
            </svg>
        </div>
        <h5 id="rbiModalTitle">Generate RBI Form B</h5>
        <p>You are about to generate RBI Form B for<br><strong id="rbiModalName"></strong>.<br>Do you want to proceed?</p>
        <hr class="rbi-modal-divider">
        <div class="rbi-modal-actions">
            <button type="button" class="rbi-modal-cancel" id="rbiModalCancel">Cancel</button>
            <a href="#" class="rbi-modal-confirm" id="rbiModalConfirm" target="_blank">
                Generate
            </a>
        </div>
    </div>
</div>

<script src="public/assets/js/rbi_form_b_select.js?v=<?= filemtime('public/assets/js/rbi_form_b_select.js') ?>"></script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>  
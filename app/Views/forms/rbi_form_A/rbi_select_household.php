<?php
ob_start();

// ── Pagination & Server-side search setup ──
$currentPage  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage      = 10;
$totalRecords = count($households ?? []);
$totalPages   = $totalRecords > 0 ? ceil($totalRecords / $perPage) : 1;
$offset       = ($currentPage - 1) * $perPage;
$paginatedHouseholds = array_slice($households ?? [], $offset, $perPage);
$queryBase    = 'index.php?controller=home&action=rbi_form_a_select&search=' . urlencode($search ?? '');
?>

<link rel="stylesheet" href="public/assets/css/rbi_form_a_select.css?v=<?= filemtime('public/assets/css/rbi_form_a_select.css') ?>">

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
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
        </div>
        <div>
            <h3>RBI Form A - Select Household</h3>
            <p>Choose a household below to generate the RBI Form A</p>
        </div>
    </div>

    <!-- Search + Count -->
    <form method="GET" action="index.php" id="search-form">
        <input type="hidden" name="controller" value="home">
        <input type="hidden" name="action" value="rbiASelectHousehold">
        <div class="controls-container">
            <div class="search-wrapper">
                <svg class="search-icon" width="15" height="15" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
                <input type="text"
                       id="searchInput"
                       name="search"
                       value="<?= htmlspecialchars($search ?? '') ?>"
                       placeholder="Search by household number or head name..."
                       autocomplete="off">
            </div>
            <span class="record-count" id="recordCount">
                <?= $totalRecords ?> household<?= $totalRecords !== 1 ? 's' : '' ?>
                <?= !empty($search) ? '- filtered' : '' ?>
            </span>
        </div>
    </form>

    <!-- Table -->
    <div class="table-container">
        <div class="table-wrapper">
            <table class="table-modern" id="householdTable">
                <thead>
                    <tr>
                        <th>Household No.</th>
                        <th>Head of Household</th>
                        <th>Families</th>
                        <th>Members</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($paginatedHouseholds)): ?>
                        <?php foreach ($paginatedHouseholds as $household): ?>
                            <?php
                                $householdNo  = htmlspecialchars($household['household_number'] ?? 'N/A');
                                $headName     = htmlspecialchars($household['head_of_household'] ?? 'N/A');
                                $label        = 'Household ' . $householdNo . ' - ' . $headName;
                                $href         = 'index.php?controller=households&action=generate_rbi_A&household_id=' . $household['id'];
                            ?>
                            <tr>
                                <td>
                                    <span class="td-bold"><?= $householdNo ?></span>
                                </td>
                                <td><?= $headName ?></td>
                                <td>
                                    <span class="num-badge">
                                        <?= htmlspecialchars($household['number_of_families'] ?? 0) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="num-badge">
                                        <?= htmlspecialchars($household['number_of_members'] ?? 0) ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button"
                                        class="btn-generate-sm rbia-generate-btn"
                                        data-name="<?= $label ?>"
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
                            <td colspan="5">
                                <div class="empty-state">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                    </svg>
                                    <h6><?= !empty($search) ? 'No results found' : 'No households found' ?></h6>
                                    <p><?= !empty($search) ? 'Try a different search term' : 'No household records are available at this time' ?></p>
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
                Showing <?= count($paginatedHouseholds) ?> of <?= $totalRecords ?> households
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
<div class="rbia-modal-overlay" id="rbiaConfirmModal" role="dialog" aria-modal="true" aria-labelledby="rbiaModalTitle">
    <div class="rbia-modal">
        <div class="rbia-modal-icon">
            <svg width="26" height="26" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
        </div>
        <h5 id="rbiaModalTitle">Generate RBI Form A</h5>
        <p>You are about to generate RBI Form A for<br><strong id="rbiaModalName"></strong>.<br>Do you want to proceed?</p>
        <hr class="rbia-modal-divider">
        <div class="rbia-modal-actions">
            <button type="button" class="rbia-modal-cancel" id="rbiaModalCancel">Cancel</button>
            <a href="#" class="rbia-modal-confirm" id="rbiaModalConfirm" target="_blank">
                Generate
            </a>
        </div>
    </div>
</div>

<script src="public/assets/js/rbi_form_a_select.js?v=<?= filemtime('public/assets/js/rbi_form_a_select.js') ?>"></script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
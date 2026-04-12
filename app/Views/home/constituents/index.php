<?php
ob_start();

// ── Pull values passed from controller ──
$filters      = $filters      ?? [];
$currentPage  = $currentPage  ?? 1;
$totalPages   = $totalPages   ?? 1;
$totalRecords = $totalRecords ?? 0;
$perPage      = $perPage      ?? 10;

$search    = $filters['search']     ?? '';
$ageMin    = $filters['age_min']    ?? '';
$ageMax    = $filters['age_max']    ?? '';
$filterOcc = $filters['occupation'] ?? '';
$filterEdu = $filters['education']  ?? '';

// ── Education map ──
$educationMap = [
    '1'  => 'Daycare',
    '2'  => 'Nursery',
    '3'  => 'Kindergarten',
    '4'  => 'Elementary',
    '5'  => 'ALS',
    '6'  => 'High School',
    '7'  => 'Junior High School',
    '8'  => 'Senior High School',
    '9'  => 'Vocational',
    '10' => 'College',
    '11' => 'Post Graduate',
];

$occupations = [
    'Government Employee',
    'Private Employee',
    'Barangay Official',
    'Barangay Volunteers',
    'OFW',
    'Business',
    'Carpenter',
    'Laborer/Construction',
    'Driver',
    'Self-Employed',
    'Student',
    'Homemaker/Housewife',
];

// ── Active filters check ──
$activeFilters = array_filter([$search, $ageMin, $ageMax, $filterOcc, $filterEdu], fn($v) => $v !== '');

// ── Base query string (preserves all filters across page links) ──
$queryBase = 'index.php?controller=constituents'
    . '&search='     . urlencode($search)
    . '&age_min='    . urlencode($ageMin)
    . '&age_max='    . urlencode($ageMax)
    . '&occupation=' . urlencode($filterOcc)
    . '&education='  . urlencode($filterEdu);
?>

<link rel="stylesheet" href="public/assets/css/constituents_index.css">

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

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <svg fill="white" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
                <div>
                    <h3 class="font-weight-bold mb-0">Constituents Management</h3>
                    <p class="mb-0 mt-1" style="opacity:0.9;font-size:0.9rem;">Manage and view all registered constituents</p>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div class="controls-container">
            <form method="GET" action="index.php" id="filter-form">
                <input type="hidden" name="controller" value="constituents">
                <input type="hidden" name="page" value="1"> <!-- reset to page 1 on new filter -->

                <!-- Row 1: Archive + Search + Add New -->
                <div class="row align-items-center mb-3">
                    <div class="col-12 col-sm-auto mb-2 mb-sm-0">
                        <a href="index.php?controller=constituents&action=removedConstituents" class="btn-archive-custom">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                                <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Archived Constituents
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
                                placeholder="Search by name...">
                        </div>
                    </div>

                    <div class="col-12 col-sm-auto">
                        <a href="index.php?controller=constituents&action=addConstituent" class="btn btn-primary-modern btn-modern w-100">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                            </svg>
                            Add New Constituent
                        </a>
                    </div>
                </div>

                <!-- Row 2: Dynamic Filters -->
                <div class="row align-items-end">

                    <!-- Age Range -->
                    <div class="col-12 col-sm-6 col-lg-3 mb-2">
                        <label style="font-size:.78rem;font-weight:600;color:#374151;margin-bottom:.3rem;display:block;">Age Range</label>
                        <div style="display:flex;gap:.4rem;align-items:center;">
                            <input type="number" name="age_min" id="age_min"
                                value="<?= htmlspecialchars($ageMin) ?>"
                                min="0" max="150"
                                class="form-control form-control-modern"
                                placeholder="Min"
                                style="min-width:0;flex:1;">
                            <span style="color:#9ca3af;font-size:.85rem;">to</span>
                            <input type="number" name="age_max" id="age_max"
                                value="<?= htmlspecialchars($ageMax) ?>"
                                min="0" max="150"
                                class="form-control form-control-modern"
                                placeholder="Max"
                                style="min-width:0;flex:1;">
                        </div>
                    </div>

                    <!-- Occupation -->
                    <div class="col-12 col-sm-6 col-lg-3 mb-2">
                        <label style="font-size:.78rem;font-weight:600;color:#374151;margin-bottom:.3rem;display:block;">Occupation</label>
                        <select name="occupation" id="filter-occupation" class="form-control form-control-modern">
                            <option value="">All Occupations</option>
                            <?php foreach ($occupations as $occ): ?>
                                <option value="<?= htmlspecialchars($occ) ?>" <?= $filterOcc === $occ ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($occ) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Educational Attainment -->
                    <div class="col-12 col-sm-6 col-lg-3 mb-2">
                        <label style="font-size:.78rem;font-weight:600;color:#374151;margin-bottom:.3rem;display:block;">Educational Attainment</label>
                        <select name="education" id="filter-education" class="form-control form-control-modern">
                            <option value="">All Levels</option>
                            <?php foreach ($educationMap as $val => $label): ?>
                                <option value="<?= $val ?>" <?= $filterEdu === (string)$val ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Apply / Clear -->
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
                                <a href="index.php?controller=constituents" class="btn btn-modern" style="background:#f3f4f6;color:#374151;border:1px solid #d1d5db;flex-shrink:0;" title="Clear all filters">
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
                                    Name: "<?= htmlspecialchars($search) ?>"
                                </span>
                            <?php endif; ?>
                            <?php if ($ageMin !== '' || $ageMax !== ''): ?>
                                <span style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;border-radius:999px;padding:.15rem .65rem;font-size:.78rem;">
                                    Age: <?= $ageMin !== '' ? $ageMin : '0' ?> – <?= $ageMax !== '' ? $ageMax : '∞' ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($filterOcc !== ''): ?>
                                <span style="background:#fdf4ff;color:#7e22ce;border:1px solid #e9d5ff;border-radius:999px;padding:.15rem .65rem;font-size:.78rem;">
                                    Occupation: <?= htmlspecialchars($filterOcc) ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($filterEdu !== ''): ?>
                                <span style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;border-radius:999px;padding:.15rem .65rem;font-size:.78rem;">
                                    Education: <?= htmlspecialchars($educationMap[$filterEdu] ?? $filterEdu) ?>
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
                            <th style="width:30%;">Full Name</th>
                            <th style="width:12%;">Sex</th>
                            <th style="width:10%;">Age</th>
                            <th style="width:18%;">Occupation</th>
                            <th style="width:10%;">Voter</th>
                            <th style="width:20%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($constituents)): ?>
                            <?php foreach ($constituents as $constituent): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?= trim(
                                                htmlspecialchars($constituent['last_name']   ?? '', ENT_QUOTES, 'UTF-8') . ', ' .
                                                htmlspecialchars($constituent['first_name']  ?? '', ENT_QUOTES, 'UTF-8') . ' ' .
                                                htmlspecialchars($constituent['middle_name'] ?? '', ENT_QUOTES, 'UTF-8') . ' ' .
                                                htmlspecialchars($constituent['suffix']      ?? '', ENT_QUOTES, 'UTF-8')
                                            ) ?>
                                        </strong>
                                    </td>
                                    <td><?= htmlspecialchars($constituent['sex'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <?php
                                        if (!empty($constituent['birthdate'])) {
                                            try {
                                                $age = (new DateTime())->diff(new DateTime($constituent['birthdate']))->y;
                                                echo '<strong>' . $age . '</strong>';
                                            } catch (Exception $e) {
                                                echo '<span class="text-muted">—</span>';
                                            }
                                        } else {
                                            echo '<span class="text-muted">—</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span style="font-size:.85rem;color:#374151;">
                                            <?= htmlspecialchars($constituent['occupation'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $voter      = $constituent['registered_voter'] ?? '';
                                        $badgeClass = $voter === 'YES' ? 'badge-yes' : 'badge-no';
                                        echo '<span class="badge-modern ' . $badgeClass . '">' . htmlspecialchars($voter) . '</span>';
                                        ?>
                                    </td>
                                    <td>
                                        <div class="action-btn-group">
                                            <a href="index.php?controller=constituents&action=view&id=<?= $constituent['id'] ?>"
                                                class="btn btn-action btn-view">
                                                <i class="fas fa-eye mr-1"></i> View
                                            </a>
                                            <a href="index.php?controller=constituents&action=edit&id=<?= $constituent['id'] ?>"
                                                class="btn btn-action btn-edit">
                                                <i class="fas fa-pencil-alt mr-1"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-action btn-archive"
                                                data-toggle="modal"
                                                data-target="#archiveModal"
                                                data-constituent-id="<?= $constituent['id'] ?>">
                                                <i class="fas fa-archive mr-1"></i> Archive
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                    </svg>
                                    <h5>No constituents found</h5>
                                    <p>Try adjusting your filters or search term</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Showing X of Y + Pagination -->
            <div class="pagination-row">
                <div class="pagination-info">
                    Showing <?= count($constituents) ?> of <?= $totalRecords ?> constituent<?= $totalRecords !== 1 ? 's' : '' ?>
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

<!-- Archive Modal -->
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
                <p>Are you sure you want to archive this constituent?</p>
                <div class="alert alert-warning mb-0" role="alert">
                    <strong>Note:</strong> If this constituent is a barangay official, they will also be removed from the officials list.
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

<script src="public/assets/js/constituents_index.js"></script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
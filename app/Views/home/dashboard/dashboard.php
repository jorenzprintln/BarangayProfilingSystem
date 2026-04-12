<?php
ob_start();
$currentPage  = isset($currentPage)  ? $currentPage  : 1;
$totalPages   = isset($totalPages)   ? $totalPages   : 1;
$perPage      = isset($perPage)      ? $perPage      : 10;
$totalRecords = isset($totalRecords) ? $totalRecords : 0;
$transactions = isset($transactions) ? $transactions : [];
?>

<link rel="stylesheet" href="public/assets/css/dashboard.css">

<script>
    window.DASHBOARD_COUNTS = {
        constituents:   <?= (int)($totalConstituents   ?? 0) ?>,
        households:     <?= (int)($totalHouseholds     ?? 0) ?>,
        families:       <?= (int)($totalFamilies       ?? 0) ?>,
        seniorCitizens: <?= (int)($totalSeniorCitizens ?? 0) ?>
    };
</script>

<div class="container-fluid py-4" style="padding-left: 2rem; padding-right: 0.75rem;">

    <!-- Toast (login success) -->
    <?php if (!empty($toast_success)): ?>
        <div class="toast-container">
            <div class="custom-toast toast-success show">
                <div class="toast-icon"><i class="fas fa-check-circle"></i></div>
                <div class="toast-body-text"><?= htmlspecialchars($toast_success) ?></div>
                <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
                <div class="toast-progress"></div>
            </div>
        </div>
        <style>
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; }
        .custom-toast {
            display: flex; align-items: center; gap: 10px;
            min-width: 300px; max-width: 420px;
            padding: 14px 16px; border-radius: 10px;
            background: #fff; color: #1e293b;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            border-left: 4px solid #22c55e;
            position: relative; overflow: hidden;
            animation: toastSlideIn 0.35s ease;
        }
        .custom-toast.toast-hiding { animation: toastSlideOut 0.3s ease forwards; }
        .toast-icon { color: #22c55e; font-size: 1.15rem; flex-shrink: 0; }
        .toast-body-text { flex: 1; font-size: 0.875rem; font-weight: 500; }
        .toast-close {
            background: none; border: none; font-size: 1.2rem; color: #94a3b8;
            cursor: pointer; padding: 0 2px; line-height: 1;
        }
        .toast-close:hover { color: #475569; }
        .toast-progress {
            position: absolute; bottom: 0; left: 0; height: 3px;
            background: #22c55e; border-radius: 0 0 0 10px;
            animation: toastProgress 4s linear forwards;
        }
        @keyframes toastSlideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes toastSlideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
        @keyframes toastProgress { from { width: 100%; } to { width: 0%; } }
        </style>
        <script>
        (function() {
            document.querySelectorAll('.custom-toast').forEach(function(toast) {
                setTimeout(function() {
                    toast.classList.add('toast-hiding');
                    setTimeout(function() { toast.parentElement.remove(); }, 300);
                }, 4000);
            });
        })();
        </script>
    <?php endif; ?>

    <!-- Header -->
    <div class="dash-header">
        <div>
            <div class="dash-header-title">Dashboard Overview</div>
            <div class="dash-header-sub">Barangay records and document transactions</div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="stat-grid">

        <div class="stat-card stat-card-1">
            <div class="stat-icon">
                <img src="public/assets/icons/crowd-of-users (1).png" width="24" height="24" alt="Constituents">
            </div>
            <div class="stat-label">Constituents</div>
            <div class="stat-value" id="constituentsCount">0</div>
        </div>

        <div class="stat-card stat-card-2">
            <div class="stat-icon">
                <img src="public/assets/icons/household.png" width="24" height="24" alt="Households">
            </div>
            <div class="stat-label">Households</div>
            <div class="stat-value" id="householdCount">0</div>
        </div>

        <div class="stat-card stat-card-3">
            <div class="stat-icon">
                <img src="public/assets/icons/family.png" width="24" height="24" alt="Families">
            </div>
            <div class="stat-label">Families</div>
            <div class="stat-value" id="familiesCount">0</div>
        </div>

        <div class="stat-card stat-card-4">
            <div class="stat-icon">
                <img src="public/assets/icons/person.png" width="24" height="24" alt="Senior Citizens">
            </div>
            <div class="stat-label">Senior Citizens</div>
            <div class="stat-value" id="recentFamiliesCount">0</div>
        </div>

    </div>

    <!-- Transactions Table -->
    <div class="table-section">
        <div class="table-header">
            <div>
                <div class="table-header-title">Document Transactions</div>
                <div class="table-header-sub">
                    <?php if (!empty($transactions)): ?>
                        <?= count($transactions) ?> record<?= count($transactions) !== 1 ? 's' : '' ?> shown
                    <?php else: ?>
                        No records yet
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($transactions)): ?>
                <a href="index.php?controller=forms&action=downloadAllFormsToZip" class="btn-download">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download All Forms
                </a>
            <?php endif; ?>
        </div>

        <div class="table-scroll">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th class="sortable" onclick="sortTable(0, this)">Document Type <span class="sort-icon">&#8645;</span></th>
                        <th class="sortable" onclick="sortTable(1, this)">Requested By <span class="sort-icon">&#8645;</span></th>
                        <th class="sortable" onclick="sortTable(2, this)">Generated By <span class="sort-icon">&#8645;</span></th>
                        <th class="sortable" onclick="sortTable(3, this)">Date Generated <span class="sort-icon">&#8645;</span></th>
                        <th>Purpose</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($transactions)): ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><span class="doc-badge"><?= htmlspecialchars($transaction['transaction'] ?? '') ?></span></td>
                                <td class="requester-name">
                                    <?= htmlspecialchars(trim($transaction['requester_fullname'] ?? '') ?: ($transaction['requested_by'] ?? '')) ?>
                                </td>
                                <td class="muted"><?= htmlspecialchars($transaction['generated_by'] ?? '') ?></td>
                                <td class="muted">
                                    <?php
                                        $dateTime = $transaction['date_of_transaction'] ?? '';
                                        if ($dateTime) {
                                            try {
                                                $dt = new DateTime($dateTime);
                                                echo '<div style="line-height:1.4;">';
                                                echo '<div style="font-weight:500;color:var(--text-secondary);">' . htmlspecialchars($dt->format('M. j, Y')) . '</div>';
                                                echo '<div style="font-size:0.82rem;color:var(--text-muted);margin-top:2px;">' . htmlspecialchars($dt->format('g:i A')) . '</div>';
                                                echo '</div>';
                                            } catch (Exception $e) {
                                                echo htmlspecialchars($dateTime);
                                            }
                                        }
                                    ?>
                                </td>
                                <td class="muted"><?= htmlspecialchars($transaction['purpose'] ?? '') ?></td>
                                <td style="text-align:center;">
                                    <a href="<?= htmlspecialchars($transaction['document_location'] ?? '#') ?>"
                                       target="_blank" class="btn-view">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#98a2b3"
                                             stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/>
                                            <line x1="16" y1="17" x2="8" y2="17"/>
                                        </svg>
                                    </div>
                                    <div class="empty-text">No transactions found.</div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination-row">
                <div class="pagination-info">
                    Showing <?= count($transactions) ?> of <?= $totalRecords ?> transactions
                </div>
                <div class="pagination-controls">
                    <a class="page-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>"
                       href="index.php?controller=dashboard&action=index&page=1">&#171;&#171;</a>
                    <a class="page-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>"
                       href="index.php?controller=dashboard&action=index&page=<?= max(1, $currentPage - 1) ?>">&#171;</a>

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
                           href="index.php?controller=dashboard&action=index&page=<?= $i ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <a class="page-btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>"
                       href="index.php?controller=dashboard&action=index&page=<?= min($totalPages, $currentPage + 1) ?>">&#187;</a>
                    <a class="page-btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>"
                       href="index.php?controller=dashboard&action=index&page=<?= $totalPages ?>">&#187;&#187;</a>
                </div>
            </div>
        <?php endif; ?>

    </div>

</div>

<script src="public/assets/js/dashboard.js"></script>

<?php
$content = ob_get_clean();
require_once('app/Views/layouts/main.php');
?>
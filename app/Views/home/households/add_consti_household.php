<?php
$content = ob_start();
?>

<link rel="stylesheet" href="public/assets/css/household_add_constituents.css">

<div class="container-fluid px-4 mt-3">

    <div class="action-bar">
        <a href="index.php?controller=households&action=view&household_id=<?= htmlspecialchars($_GET['household_id'] ?? '') ?>" class="btn-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Back to Household
        </a>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex align-items-center gap-3">
            <svg width="36" height="36" fill="white" viewBox="0 0 20 20" class="mr-3" style="flex-shrink:0">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h2 class="font-weight-bold mb-0">Add Constituents to Household</h2>
                    <?php if (!empty($household_number)): ?>
                        <span style="background:rgba(255,255,255,0.2);border:1px solid rgba(255,255,255,0.3);border-radius:0.5rem;padding:0.35rem 0.875rem;font-size:0.875rem;font-weight:700;letter-spacing:0.03em;margin-left:0.5rem;"># <?= htmlspecialchars($household_number) ?></span>
                    <?php endif; ?>
                </div>
                <p class="mb-0 mt-1" style="opacity:0.85;font-size:0.9rem;">
                    Select constituents to assign to this household
                </p>
            </div>
        </div>
    </div>

    <?php if (empty($constituents)): ?>
        <!-- Empty State Card -->
        <div class="info-card">
            <div class="info-card-body">
                <div class="empty-state">
                    <svg width="64" height="64" fill="none" stroke="#a0aec0" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h5 style="color:#4a5568;font-weight:700;margin-bottom:0.5rem;">No Constituents Available</h5>
                    <p>There are currently no constituents available to add to this household.</p>
                    <a href="index.php?controller=constituents&action=index"
                        style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.625rem 1.5rem;background:linear-gradient(135deg,var(--primary-color),#3651d4);color:white;border-radius:0.5rem;font-size:0.875rem;font-weight:600;text-decoration:none;margin-top:1rem;box-shadow:0 4px 6px rgba(67,97,238,0.3);">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        Manage Constituents
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>

        <form action="index.php?controller=households&action=storeConstituents" method="POST">
            <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
            <input type="hidden" name="household_id" value="<?= htmlspecialchars($household_id) ?>">

            <!-- Select Constituents Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-header-left">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="color:var(--primary-color)">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                        <h5>Select Constituents</h5>
                    </div>
                    <div class="d-flex align-items-center" style="gap:1rem;">
                        <div class="selected-counter">
                            <span class="count" id="selectedCount">0</span>
                            Selected
                        </div>
                        <input type="text" id="search-constituents" placeholder="Search by name..." class="search-input" style="width:220px;">
                    </div>
                </div>
                <div class="info-card-body">

                    <div class="alert-info-custom mb-4">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;margin-top:1px;">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span>Check the box next to each constituent you want to add. You can optionally set one as the <strong>Head of Household</strong>.</span>
                    </div>

                    <div class="constituent-scroll-list" id="constituentList">
                        <?php foreach ($constituents as $constituent): ?>
                            <div class="constituent-item" data-name="<?= strtolower(htmlspecialchars($constituent['first_name'] . ' ' . $constituent['last_name'] . (!empty($constituent['suffix']) ? ' ' . $constituent['suffix'] : ''))) ?>">
                                <div class="constituent-main-row">
                                    <label class="custom-checkbox-wrapper">
                                        <input type="checkbox"
                                            id="consti_<?= $constituent['id'] ?>"
                                            name="constituents[<?= $constituent['id'] ?>][selected]"
                                            value="1"
                                            class="consti-checkbox">
                                        <span class="constituent-label">
                                            <?= htmlspecialchars($constituent['first_name'] . ' ' . $constituent['last_name'] . (!empty($constituent['suffix']) ? ' ' . $constituent['suffix'] : '')) ?>
                                        </span>
                                    </label>
                                </div>
                                <div class="role-section">
                                    <?php if (!$hasHead): ?>
                                        <div class="role-label">Role</div>
                                        <label class="custom-radio-wrapper">
                                            <input type="radio"
                                                id="is_head_<?= $constituent['id'] ?>"
                                                name="is_head"
                                                value="<?= $constituent['id'] ?>"
                                                class="is-head-radio">
                                            <span>Set as Head of Household</span>
                                        </label>
                                    <?php else: ?>
                                        <span class="badge-head" style="opacity:0.6;">Member (Head already assigned)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- No results message -->
                    <div id="noResults" style="display:none;text-align:center;padding:2rem;color:#a0aec0;font-style:italic;font-size:0.875rem;">
                        No constituents match your search.
                    </div>
                </div>
            </div>

            <!-- Submit Bar -->
            <div style="display:flex;justify-content:flex-end;gap:1rem;align-items:center;margin-bottom:2rem;">
                <a href="index.php?controller=households&action=view&household_id=<?= htmlspecialchars($household_id ?? '') ?>"
                    style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 1.5rem;background:white;border:1.5px solid #e2e8f0;border-radius:0.5rem;color:#4a5568;font-size:0.875rem;font-weight:500;text-decoration:none;transition:all 0.2s;font-family:'Montserrat',sans-serif;">
                    Cancel
                </a>
                <button type="submit" class="btn-submit" id="submit-btn" disabled>
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Add Selected Members
                </button>
            </div>
        </form>

    <?php endif; ?>
</div>

<script src="public/assets/js/household_add_constituents.js"></script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
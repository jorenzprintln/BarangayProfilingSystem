<?php
$content = ob_start();
?>

<link rel="stylesheet" href="public/assets/css/create_family.css">

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
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
            </svg>
            <div>
                <h2 class="font-weight-bold mb-0">Create New Family</h2>
                <p class="mb-0 mt-1" style="opacity:0.85;font-size:0.9rem;">
                    Set up a family unit within this household
                </p>
            </div>
        </div>
    </div>

    <form action="index.php?controller=family&action=store&household_id=<?= htmlspecialchars($_GET['household_id'] ?? '') ?>" method="POST">

        <!-- Family Details Card -->
        <div class="info-card">
            <div class="info-card-header">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
                </svg>
                <h5>Family Information</h5>
            </div>
            <div class="info-card-body">
                <div class="info-grid">
                    <div class="form-group-modern">
                        <label class="form-label-modern" for="family_name">
                            Family Name <span class="required-star">*</span>
                        </label>
                        <input type="text"
                            class="form-control-modern"
                            id="family_name"
                            name="family_name"
                            placeholder="e.g. Santos Family"
                            required>
                    </div>
                    <div class="form-group-modern">
                        <label class="form-label-modern" for="date_resided">
                            Date Resided <span class="required-star">*</span>
                        </label>
                        <input type="date"
                            class="form-control-modern"
                            id="date_resided"
                            name="date_resided"
                            required>
                    </div>
                </div>

                <hr class="info-divider">

                <div class="form-group-modern" style="margin-bottom:0;">
                    <label class="form-label-modern" for="head_constituent_id">
                        Head of Family <span class="required-star">*</span>
                    </label>
                    <select class="form-control-modern" id="head_constituent_id" name="head_constituent_id" required>
                        <option value="">— Select head from chosen members —</option>
                    </select>
                    <p style="font-size:0.75rem;color:#a0aec0;margin-top:0.4rem;margin-bottom:0;">
                        Select at least one member below first, then choose the head.
                    </p>
                </div>
            </div>
        </div>

        <!-- Members Card -->
        <div class="info-card">
            <div class="info-card-header" style="justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="color:var(--primary-color)">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                    <h5>Family Members</h5>
                </div>
                <span class="selected-chip">
                    <span class="count-dot" id="memberCount">0</span>
                    Selected
                </span>
            </div>
            <div class="info-card-body">

                <div class="alert-info-custom">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;margin-top:1px;">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span>Only household members not yet assigned to a family are shown. Select members to include in this family.</span>
                </div>

                <?php if (!empty($constituents)): ?>
                    <div class="member-scroll-list">
                        <?php foreach ($constituents as $constituent): ?>
                            <div class="member-check-item" data-id="<?= $constituent['id'] ?>" data-name="<?= htmlspecialchars($constituent['full_name']) ?>">
                                <input type="checkbox"
                                    class="custom-checkbox member-checkbox"
                                    id="member_<?= $constituent['id'] ?>"
                                    name="members[]"
                                    value="<?= $constituent['id'] ?>">
                                <label class="member-check-label" for="member_<?= $constituent['id'] ?>">
                                    <?= htmlspecialchars($constituent['full_name']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-members">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:0.75rem;opacity:0.35;display:block;margin-left:auto;margin-right:auto;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        No household members available to add to a family.
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Submit Bar -->
        <div style="display:flex;justify-content:flex-end;gap:1rem;align-items:center;margin-bottom:2rem;">
            <a href="index.php?controller=households&action=view&household_id=<?= htmlspecialchars($_GET['household_id'] ?? '') ?>" class="btn-cancel">
                Cancel
            </a>
            <button type="submit" class="btn-submit">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                </svg>
                Create Family
            </button>
        </div>

    </form>
</div>

<script src="public/assets/js/create_family.js"></script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
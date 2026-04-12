<?php
$content = ob_start();
?>

<link rel="stylesheet" href="public/assets/css/household_view.css?v=<?= filemtime('public/assets/css/household_view.css') ?>">

<div class="container-fluid px-4 mt-3">

    <?php if (Session::hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert"
             style="border-radius:.75rem;border:none;border-left:4px solid #06d6a0;background:#d1fae5;color:#065f46;">
            <strong>Success!</strong> <?= Session::getFlash('success') ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <?php if (Session::hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert"
             style="border-radius:.75rem;border:none;border-left:4px solid #ef476f;background:#fee2e2;color:#991b1b;">
            <strong>Error!</strong> <?= Session::getFlash('error') ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <!-- Action Bar -->
    <div class="action-bar">
        <a href="index.php?controller=households&action=index" class="btn-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Back to List
        </a>
        <div class="action-group">
            <a href="index.php?controller=households&action=generate_rbi_A&household_id=<?= $household['id'] ?>" class="btn-action-top generate" target="_blank">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Generate RBI Form A
            </a>
        </div>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex align-items-center mt-3 gap-3">
            <svg width="36" height="36" fill="white" viewBox="0 0 20 20" class="mr-3" style="flex-shrink:0">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h2 class="font-weight-bold mb-0">Household Details</h2>
                    <?php if (!empty($household['household_number'])): ?>
                        <span class="household-number-badge ml-3"># <?= htmlspecialchars($household['household_number']) ?></span>
                    <?php endif; ?>
                </div>
                <p class="mb-0 mt-1" style="opacity:0.85;font-size:0.9rem;">
                    <?= htmlspecialchars($household['barangay_name'] ?? '') ?>,
                    <?= htmlspecialchars($household['city_municipality'] ?? '') ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Address Information Card -->
    <div class="info-card">
        <div class="info-card-header">
            <div class="info-card-header-left">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="color:var(--primary-color)">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
                <h5>Address Information</h5>
            </div>
        </div>
        <div class="info-card-body">
            <p class="mb-2" style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#a0aec0;">Administrative Location</p>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Region</span>
                    <span class="info-value"><?= htmlspecialchars($household['region'] ?? '') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Province</span>
                    <span class="info-value"><?= htmlspecialchars($household['province'] ?? '') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">City / Municipality</span>
                    <span class="info-value"><?= htmlspecialchars($household['city_municipality'] ?? '') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Barangay</span>
                    <span class="info-value"><?= htmlspecialchars($household['barangay_name'] ?? '') ?></span>
                </div>
            </div>

            <hr class="info-divider">

            <p class="mb-2" style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#a0aec0;">Street & Unit Details</p>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Purok</span>
                    <?php if (empty($household['purok'])): ?>
                        <span class="info-value not-specified">Not specified</span>
                    <?php else: ?>
                        <span class="info-value"><?= htmlspecialchars($household['purok']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Street</span>
                    <?php if (empty($household['street_name'])): ?>
                        <span class="info-value not-specified">Not specified</span>
                    <?php else: ?>
                        <span class="info-value"><?= htmlspecialchars($household['street_name']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Zip Code</span>
                    <?php if (empty($household['zip_code'])): ?>
                        <span class="info-value not-specified">Not specified</span>
                    <?php else: ?>
                        <span class="info-value"><?= htmlspecialchars($household['zip_code']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Block No.</span>
                    <?php if (empty($household['block_number'])): ?>
                        <span class="info-value not-specified">Not specified</span>
                    <?php else: ?>
                        <span class="info-value"><?= htmlspecialchars($household['block_number']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Lot No.</span>
                    <?php if (empty($household['lot_number'])): ?>
                        <span class="info-value not-specified">Not specified</span>
                    <?php else: ?>
                        <span class="info-value"><?= htmlspecialchars($household['lot_number']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">House / Bldg No.</span>
                    <?php if (empty($household['house_building_number'])): ?>
                        <span class="info-value not-specified">Not specified</span>
                    <?php else: ?>
                        <span class="info-value"><?= htmlspecialchars($household['house_building_number']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Unit No.</span>
                    <?php if (empty($household['unit_number'])): ?>
                        <span class="info-value not-specified">Not specified</span>
                    <?php else: ?>
                        <span class="info-value"><?= htmlspecialchars($household['unit_number']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Household Members Card -->
    <div class="info-card">
        <div class="info-card-header">
            <div class="info-card-header-left">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="color:var(--primary-color)">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
                <h5>Household Members</h5>
            </div>
            <div class="d-flex align-items-center gap-2" style="flex:1; justify-content:flex-end;">
                <a href="index.php?controller=households&action=addConstituents&household_id=<?= $household['id'] ?>" class="btn-add mr-2">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Add Members
                </a>
                <input type="text" id="memberSearch" placeholder="Search members..." class="search-input form-control" style="width:200px;">
            </div>
        </div>
        <div class="info-card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="modern-table" id="membersTable">
                    <thead>
                        <tr>
                            <th style="width:50px">No.</th>
                            <th>Full Name</th>
                            <th>Age</th>
                            <th>Sex</th>
                            <th>Civil Status</th>
                            <th>Contact</th>
                            <th>Role</th>
                            <th style="width:120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($members)): ?>
                            <?php foreach ($members as $index => $member): ?>
                                <tr>
                                    <td style="color:#a0aec0;font-weight:600;"><?= $index + 1 ?></td>
                                    <td style="font-weight:600;color:#2d3748;">
                                        <?= htmlspecialchars($member['full_name']) ?>
                                        <?php if ($member['is_archived']): ?>
                                            <span style="background:#fee2e2;color:#991b1b;font-size:0.65rem;font-weight:700;padding:0.15rem 0.5rem;border-radius:1rem;margin-left:0.4rem;text-transform:uppercase;">Archived</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($member['age']) ?></td>
                                    <td><?= htmlspecialchars($member['sex']) ?></td>
                                    <td><?= htmlspecialchars($member['civil_status']) ?></td>
                                    <td><?= htmlspecialchars($member['contact'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php if ($member['is_head'] === 'YES'): ?>
                                            <span class="badge-head">Head</span>
                                            <?php if ($member['is_archived']): ?>
                                                <div style="font-size:0.65rem;color:#d97706;margin-top:0.2rem;font-weight:600;">No active head</div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge-member">Member</span>
                                        <?php endif; ?>
                                    </td>
                                   <td>
                                        <div class="dropdown">
                                            <button class="btn-actions-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                                </svg>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right actions-dropdown">
                                                <a class="dropdown-item action-item view-item"
                                                href="index.php?controller=constituents&action=view&id=<?= $member['constituent_id'] ?>">
                                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    View Details
                                                </a>

                                                <?php if ($member['is_head'] !== 'YES' && !$member['is_archived']): ?>
                                                    <a class="dropdown-item action-item sethead-item" href="javascript:void(0)"
                                                    onclick="confirmSetHouseholdHead(<?= $member['constituent_id'] ?>, <?= $household['id'] ?>, '<?= htmlspecialchars($member['full_name']) ?>')">
                                                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Set as Head
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($member['is_head'] !== 'YES'): ?>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item action-item remove-item" href="javascript:void(0)"
                                                    onclick="confirmRemoveMember(<?= $member['constituent_id'] ?>, <?= $household['id'] ?>)">
                                                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Remove
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="empty-row">
                                <td colspan="8">No members found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Families in Household Card -->
    <div class="info-card">
        <div class="info-card-header">
            <div class="info-card-header-left">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="color:var(--primary-color)">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                </svg>
                <h5>Families in Household</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="index.php?controller=family&action=createHouseholdFamily&household_id=<?= $household['id'] ?>" class="btn-add mr-2">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    New Family
                </a>
                <input type="text" id="familySearch" class="search-input form-control" placeholder="Search family...">
            </div>
        </div>
        <div class="info-card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="modern-table" id="familiesTable">
                    <thead>
                        <tr>
                            <th style="width:50px">No.</th>
                            <th>Family Name</th>
                            <th>Head of Family</th>
                            <th>Members</th>
                            <th style="width:130px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($families)): ?>
                            <?php foreach ($families as $index => $family): ?>
                                <tr>
                                    <td style="color:#a0aec0;font-weight:600;"><?= $index + 1 ?></td>
                                    <td style="font-weight:600;color:#2d3748;"><?= htmlspecialchars($family['family_name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($family['head_full_name'] ?? 'N/A') ?></td>
                                    <td>
                                        <ul class="family-members-list">
                                            <?php foreach ($family['members'] as $member): ?>
                                                <?php if (empty($member['id'])) continue; ?>
                                                <li><?= htmlspecialchars($member['full_name'] ?? '') ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn-actions-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                                </svg>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right actions-dropdown">
                                                <a class="dropdown-item action-item view-item"
                                                href="index.php?controller=family&action=addMember&family_id=<?= $family['family_id'] ?>&household_id=<?= $household['id'] ?>">
                                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Add Member
                                                </a>
                                                <a class="dropdown-item action-item sethead-item" href="javascript:void(0)"
                                                onclick="openManageFamilyModal(<?= $family['family_id'] ?>, <?= $household['id'] ?>, '<?= htmlspecialchars($family['family_name']) ?>', <?= $family['head_constituent_id'] ?? 'null' ?>)">
                                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                                    </svg>
                                                    Manage Members
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item action-item remove-item" href="javascript:void(0)"
                                                onclick="confirmDeleteFamily(<?= $family['family_id'] ?>, <?= $household['id'] ?>)">
                                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Delete Family
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="empty-row">
                                <td colspan="5">No families found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== MODALS ===== -->

    <!-- Delete Family Modal -->
    <div class="modal fade" id="deleteFamilyModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius:1rem;border:none;">
                <div class="modal-header" style="background:linear-gradient(135deg,#ef476f,#d32f2f);border:none;border-radius:1rem 1rem 0 0;">
                    <h5 class="modal-title" style="color:white;font-weight:700;">Delete Family</h5>
                    <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:0.8;"><span>&times;</span></button>
                </div>
                <div class="modal-body" style="background:#f8f9fa;padding:1.5rem 2rem 0.5rem;">
                    <p>Are you sure you want to delete this family? This action cannot be undone.</p>
                </div>
                <div class="modal-footer" style="background:#f8f9fa;border-top:1px solid #e9ecef;padding:1rem 2rem 1.5rem;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDeleteFamilyBtn" class="btn btn-danger" style="border-radius:.5rem;font-weight:600;">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Remove Member Modal -->
    <div class="modal fade" id="removeMemberModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius:1rem;border:none;">
                <div class="modal-header" style="background:linear-gradient(135deg,#ef476f,#d32f2f);border:none;border-radius:1rem 1rem 0 0;">
                    <h5 class="modal-title" style="color:white;font-weight:700;">Remove Member</h5>
                    <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:0.8;"><span>&times;</span></button>
                </div>
                <div class="modal-body" style="background:#f8f9fa;padding:1.5rem 2rem 0.5rem;">
                    <p>Are you sure you want to remove this member from the household?</p>
                    <div class="alert alert-warning mb-0">
                        <strong>Note:</strong> The constituent record will not be deleted, only removed from this household.
                    </div>
                </div>
                <div class="modal-footer" style="background:#f8f9fa;border-top:1px solid #e9ecef;padding:1rem 2rem 1.5rem;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmRemoveMemberBtn" class="btn btn-danger" style="border-radius:.5rem;font-weight:600;">Remove</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Family Members Modal -->
    <div class="modal fade" id="manageFamilyModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius:1rem;border:none;overflow:hidden;">
                <div class="modal-header" style="background:linear-gradient(135deg,var(--primary-color),#3651d4);border:none;border-radius:1rem 1rem 0 0;">
                    <div>
                        <h5 class="modal-title" style="color:white;font-weight:700;margin-bottom:0.15rem;">
                            Manage Members - <span id="manageFamilyName"></span>
                        </h5>
                        <p style="color:rgba(255,255,255,0.75);font-size:0.75rem;margin:0;">
                            Remove members or reassign the head of family
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:0.8;"><span>&times;</span></button>
                </div>
                <div class="modal-body" style="background:#f8f9fa;padding:1.5rem 2rem;">
                    <div style="display:flex;gap:1rem;margin-bottom:1rem;font-size:0.75rem;flex-wrap:wrap;">
                        <span style="display:flex;align-items:center;gap:0.3rem;color:#a0aec0;">
                            <span style="background:rgba(255,209,102,0.3);border:1px solid #ffd166;border-radius:3px;padding:1px 6px;font-size:0.7rem;font-weight:700;color:#b7791f;">Set as Head</span>
                            Promotes member to head (demotes current head to member)
                        </span>
                    </div>
                    <div id="manageFamilyMembersList">
                        <p class="text-center text-muted">Loading members...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Remove from Family Modal -->
    <div class="modal fade" id="removeFromFamilyModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius:1rem;border:none;">
                <div class="modal-header" style="background:linear-gradient(135deg,#ef476f,#d32f2f);border:none;border-radius:1rem 1rem 0 0;">
                    <h5 class="modal-title" style="color:white;font-weight:700;">Remove from Family</h5>
                    <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:0.8;"><span>&times;</span></button>
                </div>
                <div class="modal-body" style="background:#f8f9fa;padding:1.5rem 2rem 0.5rem;">
                    <p>Are you sure you want to remove <strong id="removeMemberName"></strong> from this family?</p>
                    <div class="alert alert-info mb-0">
                        <strong>Note:</strong> The member will remain in the household and can be added to another family.
                    </div>
                </div>
                <div class="modal-footer" style="background:#f8f9fa;border-top:1px solid #e9ecef;padding:1rem 2rem 1.5rem;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmRemoveFromFamilyBtn" class="btn btn-danger" style="border-radius:.5rem;font-weight:600;">Remove</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Set as Head Modal -->
    <div class="modal fade" id="setHeadModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius:1rem;border:none;">
                <div class="modal-header" style="background:linear-gradient(135deg,#f6ad55,#ed8936);border:none;border-radius:1rem 1rem 0 0;">
                    <h5 class="modal-title" style="color:white;font-weight:700;">Change Head of Family</h5>
                    <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:0.8;"><span>&times;</span></button>
                </div>
                <div class="modal-body" style="background:#f8f9fa;padding:1.5rem 2rem 0.5rem;">
                    <p>Set <strong id="newHeadName"></strong> as the new head of this family?</p>
                    <div class="alert alert-warning mb-0" style="border-radius:0.5rem;">
                        <strong>Note:</strong> The current head will be demoted to a regular member.
                    </div>
                </div>
                <div class="modal-footer" style="background:#f8f9fa;border-top:1px solid #e9ecef;padding:1rem 2rem 1.5rem;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmSetHeadBtn"
                        style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.5rem 1.25rem;background:linear-gradient(135deg,#f6ad55,#ed8936);color:white;border-radius:0.5rem;font-weight:600;font-size:0.875rem;text-decoration:none;border:none;">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        Confirm
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Set Household Head Modal -->
    <div class="modal fade" id="setHouseholdHeadModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius:1rem;border:none;">
                <div class="modal-header" style="background:linear-gradient(135deg,#f6ad55,#ed8936);border:none;border-radius:1rem 1rem 0 0;">
                    <h5 class="modal-title" style="color:white;font-weight:700;">Change Household Head</h5>
                    <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:0.8;"><span>&times;</span></button>
                </div>
                <div class="modal-body" style="background:#f8f9fa;padding:1.5rem 2rem 0.5rem;">
                    <p>Set <strong id="newHouseholdHeadName"></strong> as the new head of this household?</p>
                    <div class="alert alert-warning mb-0" style="border-radius:0.5rem;">
                        <strong>Note:</strong> The current household head will be demoted to a regular member.
                    </div>
                </div>
                <div class="modal-footer" style="background:#f8f9fa;border-top:1px solid #e9ecef;padding:1rem 2rem 1.5rem;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmSetHouseholdHeadBtn"
                        style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.5rem 1.25rem;background:linear-gradient(135deg,#f6ad55,#ed8936);color:white;border-radius:0.5rem;font-weight:600;font-size:0.875rem;text-decoration:none;">
                        Confirm
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="public/assets/js/household_view.js"></script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
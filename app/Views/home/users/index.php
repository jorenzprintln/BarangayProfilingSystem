<?php
$content = ob_start();

$conQueryBase = 'index.php?controller=users&con_search=' . urlencode($conSearch ?? '');

// [PENDING APPROVALS DISABLED] - No longer needed since self-registration is disabled.
// $pendingCount = count($pendingUsers ?? []);
?>

<link rel="stylesheet" href="public/assets/css/manage_users.css?v=<?= time() ?>">

<div class="container-fluid px-4 mt-3">

    <!-- Flash Messages -->
    <?php if (Session::hasFlash('success')): ?>
        <div id="flash-success" class="alert alert-success-modern alert-modern alert-dismissible fade show" role="alert">
            <strong>Success!</strong> <?= Session::getFlash('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (Session::hasFlash('error')): ?>
        <div id="flash-error" class="alert alert-danger-modern alert-modern alert-dismissible fade show" role="alert">
            <strong>Error!</strong> <?= Session::getFlash('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <svg fill="white" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.97 5.97 0 00-.75-2.906A3.005 3.005 0 0119 17v1h-3zM4.75 14.094A5.973 5.973 0 004 17v1H1v-1a3 3 0 013.75-2.906z"/>
            </svg>
            <div>
                <h3 class="font-weight-bold mb-0">Manage Accounts</h3>
                <p class="mb-0 mt-1" style="opacity:0.85;font-size:0.875rem;">Manage constituent accounts</p>
            </div>
        </div>
    </div>

    <?php
    // [PENDING APPROVALS DISABLED] - Self-registration is disabled. Accounts are now
    // auto-created by the secretary when adding a constituent. This entire section
    // (pending cards, approve modal, reject modal) is commented out.
    // Uncomment if self-registration is ever re-enabled.
    /*

    // PENDING APPROVALS
    if (!empty($pendingUsers)):
    $pendingCount = count($pendingUsers);
    ?>
    <div class="pending-section">
        <div class="pending-header">
            <div class="pending-header-left">
                <i class="fas fa-clock"></i>
                <h5>Pending Approvals</h5>
                <span class="pending-count"><?= $pendingCount ?></span>
            </div>
        </div>
        <div class="pending-body">
            <?php foreach ($pendingUsers as $pUser): ?>
                <div class="pending-card">
                    <div class="pending-user-info">
                        <div class="pending-avatar">
                            <?= strtoupper(substr($pUser['fullname'] ?: $pUser['username'], 0, 1)) ?>
                        </div>
                        <div class="pending-details">
                            <span class="pending-fullname"><?= htmlspecialchars($pUser['fullname'] ?: '—') ?></span>
                            <span class="pending-username">@<?= htmlspecialchars($pUser['username']) ?></span>
                        </div>
                    </div>
                    <div class="pending-role">
                        <span class="role-badge role-constituent">
                            <i class="fas fa-user"></i>
                            Constituent
                        </span>
                    </div>
                    <div class="pending-actions">
                        <button type="button" class="btn btn-action btn-approve"
                                data-toggle="modal"
                                data-target="#approveModal-<?= $pUser['id'] ?>">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button type="button"
                                class="btn btn-action btn-reject btn-reject-trigger"
                                data-id="<?= $pUser['id'] ?>"
                                data-csrf="<?= Session::generateCsrfToken() ?>"
                                data-name="<?= htmlspecialchars($pUser['fullname'] ?: $pUser['username'], ENT_QUOTES) ?>">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </div>
                </div>

                <!-- Approve Modal -->
                <div class="modal fade" id="approveModal-<?= $pUser['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content approve-modal-content">
                            <form method="POST" action="index.php?controller=users&action=approve">
                                <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                                <input type="hidden" name="id" value="<?= $pUser['id'] ?>">
                                <div class="modal-header approve-modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-user-check mr-2"></i>
                                        Approve Account
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="approve-user-preview mb-3">
                                        <div class="pending-avatar" style="width:48px;height:48px;font-size:1.1rem;">
                                            <?= strtoupper(substr($pUser['fullname'] ?: $pUser['username'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong><?= htmlspecialchars($pUser['fullname'] ?: '—') ?></strong>
                                            <br><small class="text-muted">@<?= htmlspecialchars($pUser['username']) ?></small>
                                        </div>
                                    </div>
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
                                            Search and select the matching constituent record.
                                        </small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success" style="border-radius:.5rem; font-weight:600; padding:.625rem 1.5rem; display:inline-flex; align-items:center; gap:.4rem;">
                                        <i class="fas fa-check"></i> Approve Account
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-info mb-3" style="border-radius:.75rem; font-size:0.875rem;">
        <i class="fas fa-info-circle mr-2"></i>
        No pending account approvals at the moment.
    </div>
    <?php endif;
    */ // END [PENDING APPROVALS DISABLED]
    ?>

    <!-- CONSTITUENT ACCOUNTS -->
    <div class="controls-container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-6 mb-3 mb-lg-0">
                <form method="GET" action="index.php" id="con-search-form">
                    <input type="hidden" name="controller" value="users">
                    <div class="search-wrapper">
                        <svg class="search-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                        </svg>
                        <input type="text" id="con-search-input" name="con_search"
                            value="<?= htmlspecialchars($conSearch ?? '') ?>"
                            class="form-control form-control-modern"
                            placeholder="Search constituent accounts...">
                    </div>
                </form>
            </div>
            <!-- <div class="col-lg-6 col-md-6 d-flex justify-content-md-end">
                <a href="index.php?controller=users&action=archivedAccounts"
                   class="btn btn-modern"
                   style="background:linear-gradient(135deg,#6b7280,#4b5563);color:#fff;">
                    <i class="fas fa-archive" style="margin-right:.4rem;"></i>
                    Rejected Accounts
                </a>
            </div> -->
        </div>
    </div>

    <div class="table-container">
        <div class="table-wrapper">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th style="width:22%;">Username</th>
                        <th style="width:26%;">Full Name</th>
                        <th style="width:12%;">Role</th>
                        <th style="width:12%;">Status</th>
                        <th style="width:28%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($constituentAccounts)): ?>
                        <?php foreach ($constituentAccounts as $cUser): ?>
                            <tr>
                                <td>
                                    <div class="user-info-cell">
                                        <div class="user-avatar" style="background:linear-gradient(135deg,#10b981,#059669);">
                                            <?= strtoupper(substr($cUser['fullname'] ?: $cUser['username'], 0, 1)) ?>
                                        </div>
                                        <span class="user-name"><?= htmlspecialchars($cUser['username']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($cUser['fullname'] ?? '—') ?></td>
                                <td>
                                    <span class="role-badge role-constituent">
                                        <i class="fas fa-user"></i>
                                        Constituent
                                    </span>
                                </td>
                                <td>
                                    <?php if ($cUser['status'] === 'deactivated'): ?>
                                        <span class="status-badge status-deactivated">Deactivated</span>
                                    <?php else: ?>
                                        <span class="status-badge status-approved">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-btn-group">

                                        <!-- Reset Password -->
                                        <button type="button"
                                            class="btn btn-action btn-reset-password"
                                            data-toggle="modal"
                                            data-target="#resetPasswordModal"
                                            data-id="<?= $cUser['id'] ?>"
                                            data-name="<?= htmlspecialchars($cUser['fullname'] ?: $cUser['username'], ENT_QUOTES) ?>"
                                            data-username="<?= htmlspecialchars($cUser['username'], ENT_QUOTES) ?>">
                                            <i class="fas fa-key"></i> Reset Password
                                        </button>

                                        <!-- Activate / Deactivate -->
                                        <?php if ($cUser['status'] === 'deactivated'): ?>
                                            <button type="button" class="btn btn-action btn-activate toggle-status-btn"
                                                data-id="<?= $cUser['id'] ?>"
                                                data-name="<?= htmlspecialchars($cUser['username'], ENT_QUOTES) ?>"
                                                data-action="activate">
                                                <i class="fas fa-check-circle"></i> Activate
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-action btn-deactivate toggle-status-btn"
                                                data-id="<?= $cUser['id'] ?>"
                                                data-name="<?= htmlspecialchars($cUser['username'], ENT_QUOTES) ?>"
                                                data-action="deactivate">
                                                <i class="fas fa-ban"></i> Deactivate
                                            </button>
                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
                                </svg>
                                <h5>No constituent accounts</h5>
                                <p>Constituent accounts are automatically created when the secretary adds a new constituent.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Showing X of Y + Pagination (always visible) -->
        <div class="pagination-row" style="justify-content:space-between;align-items:center;">
            <div class="pagination-info">
                Showing <?= count($constituentAccounts ?? []) ?> of <?= $conTotal ?? 0 ?> account<?= ($conTotal ?? 0) !== 1 ? 's' : '' ?>
            </div>

            <?php if (($conTotalPages ?? 1) > 1): ?>
                <nav class="pagination-wrapper" style="margin-top:0;">
                    <ul class="pagination pagination-modern">
                        <li class="page-item <?= ($conPage ?? 1) <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $conQueryBase ?>&con_page=<?= ($conPage ?? 1) - 1 ?>">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= ($conTotalPages ?? 1); $i++): ?>
                            <li class="page-item <?= $i == ($conPage ?? 1) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $conQueryBase ?>&con_page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($conPage ?? 1) >= ($conTotalPages ?? 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $conQueryBase ?>&con_page=<?= ($conPage ?? 1) + 1 ?>">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

    </div>

</div>

<!-- ── Reset Password Modal ── -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border:none;border-radius:1rem;overflow:hidden;">
            <form method="POST" action="index.php?controller=users&action=resetPassword" id="resetPasswordForm">
                <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                <input type="hidden" name="id" id="resetUserId" value="">

                <div class="modal-header" style="background:linear-gradient(135deg,#4361ee,#3651d4);border:none;padding:1.25rem 1.5rem;">
                    <h5 class="modal-title" style="color:white;font-weight:700;display:flex;align-items:center;gap:.5rem;">
                        <i class="fas fa-key"></i> Reset Password
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" style="padding:1.5rem;background:#f8faff;">

                    <!-- User preview -->
                    <div style="display:flex;align-items:center;gap:.75rem;background:white;border-radius:.75rem;padding:1rem;margin-bottom:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,.06);">
                        <div id="resetUserAvatar" style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:1rem;flex-shrink:0;"></div>
                        <div>
                            <div id="resetUserFullname" style="font-weight:700;color:#1e293b;font-size:.9rem;"></div>
                            <div id="resetUserUsername" style="font-size:.8rem;color:#64748b;"></div>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div style="margin-bottom:1rem;">
                        <label style="font-size:.82rem;font-weight:600;color:#374151;margin-bottom:.35rem;display:block;">
                            New Password <span style="color:#ef4444;">*</span>
                        </label>
                        <div style="position:relative;">
                            <input type="password" name="new_password" id="resetNewPassword"
                                class="form-control"
                                placeholder="Enter new password"
                                style="border:1.5px solid #e2e8f0;border-radius:.6rem;padding:.6rem .875rem;padding-right:2.5rem;font-size:.9rem;"
                                required minlength="8">
                            <button type="button" onclick="toggleResetPw('resetNewPassword', this)"
                                style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;padding:0;">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        <small style="color:#94a3b8;font-size:.75rem;">Minimum 8 characters</small>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label style="font-size:.82rem;font-weight:600;color:#374151;margin-bottom:.35rem;display:block;">
                            Confirm New Password <span style="color:#ef4444;">*</span>
                        </label>
                        <div style="position:relative;">
                            <input type="password" name="confirm_password" id="resetConfirmPassword"
                                class="form-control"
                                placeholder="Re-enter new password"
                                style="border:1.5px solid #e2e8f0;border-radius:.6rem;padding:.6rem .875rem;padding-right:2.5rem;font-size:.9rem;"
                                required>
                            <button type="button" onclick="toggleResetPw('resetConfirmPassword', this)"
                                style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;padding:0;">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        <div id="resetPasswordMismatch" style="display:none;color:#ef4444;font-size:.78rem;margin-top:.25rem;">
                            <i class="fas fa-exclamation-circle mr-1"></i>Passwords do not match.
                        </div>
                    </div>

                </div>

                <div class="modal-footer" style="background:#f8faff;border-top:1px solid #e8edf8;padding:1rem 1.5rem;gap:.5rem;">
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn"
                        style="background:linear-gradient(135deg,#4361ee,#3651d4);color:white;border:none;border-radius:.6rem;font-weight:600;padding:.6rem 1.4rem;display:inline-flex;align-items:center;gap:.4rem;">
                        <i class="fas fa-key"></i> Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Toggle Status Modal ── -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" id="toggleModalHeader">
                <h5 class="modal-title" id="toggleModalTitle">
                    <i id="toggleModalIcon" class="fas"></i>
                    <span id="toggleModalTitleText"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="toggleModalMessage"></p>
                <div id="toggleModalAlert" class="alert mb-0" role="alert" style="border-radius:.5rem;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                <form method="POST" action="index.php?controller=users&action=toggleStatus" style="display:inline;">
                    <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                    <input type="hidden" name="id" id="toggleStatusId" value="">
                    <button type="submit" id="confirmToggle" class="btn"
                            style="border-radius:.5rem;font-weight:600;padding:.625rem 1.5rem;display:inline-flex;align-items:center;gap:.4rem;">
                        <i id="confirmToggleIcon" class="fas"></i>
                        <span id="confirmToggleText"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// [PENDING APPROVALS DISABLED] - Reject Account Modal is no longer needed.
// Uncomment if self-registration is re-enabled.
/*
?>
<!-- ── Reject Account Modal ── -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border:none;border-radius:1rem;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#ef4444,#dc2626);border:none;padding:1.25rem 1.5rem;">
                <h5 class="modal-title" style="color:white;font-weight:700;display:flex;align-items:center;gap:.5rem;">
                    <i class="fas fa-user-times"></i> Reject Account
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding:1.5rem;background:#fffbeb;">
                <div style="display:flex;align-items:center;gap:.75rem;background:white;border-radius:.75rem;padding:1rem;margin-bottom:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,.06);">
                    <div id="rejectUserAvatar" style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#ef4444,#dc2626);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:1rem;flex-shrink:0;"></div>
                    <div>
                        <div id="rejectUserName" style="font-weight:700;color:#1e293b;font-size:.9rem;"></div>
                        <div style="font-size:.8rem;color:#64748b;">Pending account request</div>
                    </div>
                </div>
                <div class="alert alert-warning mb-0" style="border-radius:.6rem;font-size:.875rem;">
                    <strong>Note:</strong> This account will be moved to Rejected Accounts. You can still re-approve it from there at any time.
                </div>
            </div>
            <div class="modal-footer" style="background:#fffbeb;border-top:1px solid #fde68a;padding:1rem 1.5rem;gap:.5rem;">
                <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                <form method="POST" action="index.php?controller=users&action=reject" id="rejectForm" style="display:inline;">
                    <input type="hidden" name="_csrf_token" id="rejectCsrfToken" value="">
                    <input type="hidden" name="id" id="rejectUserId" value="">
                    <button type="submit" class="btn"
                        style="background:linear-gradient(135deg,#ef4444,#dc2626);color:white;border:none;border-radius:.6rem;font-weight:600;padding:.6rem 1.4rem;display:inline-flex;align-items:center;gap:.4rem;">
                        <i class="fas fa-times"></i> Reject Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
*/ // END [PENDING APPROVALS DISABLED]
?>

<style>
.btn-reset-password {
    background: #ede9fe;
    color: #6d28d9;
}
.btn-reset-password:hover {
    background: #6d28d9;
    color: white;
}
</style>

<script>
// ── Reset Password Modal ──
$(document).on('click', '.btn-reset-password', function() {
    var id       = $(this).data('id');
    var name     = $(this).data('name');
    var username = $(this).data('username');

    $('#resetUserId').val(id);
    $('#resetUserFullname').text(name);
    $('#resetUserUsername').text('@' + username);
    $('#resetUserAvatar').text(name.charAt(0).toUpperCase());
    $('#resetNewPassword').val('');
    $('#resetConfirmPassword').val('');
    $('#resetPasswordMismatch').hide();
});

function toggleResetPw(fieldId, btn) {
    var input = document.getElementById(fieldId);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye-slash';
    }
}

$('#resetPasswordForm').on('submit', function(e) {
    var pw  = $('#resetNewPassword').val();
    var cpw = $('#resetConfirmPassword').val();
    if (pw !== cpw) {
        e.preventDefault();
        $('#resetPasswordMismatch').show();
        return false;
    }
    $('#resetPasswordMismatch').hide();
});

$('#resetConfirmPassword').on('input', function() {
    if ($(this).val() !== $('#resetNewPassword').val()) {
        $('#resetPasswordMismatch').show();
    } else {
        $('#resetPasswordMismatch').hide();
    }
});

// ── Toggle Status Modal ──
$(document).on('click', '.toggle-status-btn', function() {
    var id           = $(this).data('id');
    var name         = $(this).data('name');
    var action       = $(this).data('action');
    var isDeactivate = action === 'deactivate';

    $('#toggleModalHeader').css('background', isDeactivate
        ? 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)'
        : 'linear-gradient(135deg, #06d6a0 0%, #059669 100%)');
    $('#toggleModalIcon').attr('class', 'fas ' + (isDeactivate ? 'fa-ban' : 'fa-check-circle'));
    $('#toggleModalTitleText').text(isDeactivate ? 'Deactivate Account' : 'Activate Account');
    $('#toggleModalMessage').html('Are you sure you want to ' + action + ' the account <strong>"' + $('<span>').text(name).html() + '"</strong>?');
    $('#toggleModalAlert')
        .attr('class', 'alert mb-0 ' + (isDeactivate ? 'alert-warning' : 'alert-success'))
        .html('<strong>' + (isDeactivate ? 'Warning:' : 'Note:') + '</strong> ' +
            (isDeactivate ? 'This user will not be able to log in.' : 'This user will be able to log in again.'));
    $('#confirmToggle').css('background', isDeactivate ? '#f59e0b' : '#06d6a0').css('color', '#fff');
    $('#confirmToggleIcon').attr('class', 'fas ' + (isDeactivate ? 'fa-ban' : 'fa-check-circle'));
    $('#confirmToggleText').text(isDeactivate ? 'Deactivate' : 'Activate');
    $('#toggleStatusId').val(id);
    $('#toggleStatusModal').modal('show');
});

// [PENDING APPROVALS DISABLED] - Reject modal JS removed. Uncomment if re-enabled.
/*
$(document).on('click', '.btn-reject-trigger', function() {
    var id   = $(this).data('id');
    var name = $(this).data('name');
    var csrf = $(this).data('csrf');

    $('#rejectUserId').val(id);
    $('#rejectCsrfToken').val(csrf);
    $('#rejectUserName').text(name);
    $('#rejectUserAvatar').text(name.charAt(0).toUpperCase());
    $('#rejectModal').modal('show');
});

$('[id^="approveModal-"]').on('shown.bs.modal', function() {
    $(this).find('.selectpicker').selectpicker('refresh');
});
*/

setTimeout(function() { $('.alert-modern').fadeOut(500); }, 4000);

$('#con-search-input').on('keypress', function(e) {
    if (e.which === 13) $('#con-search-form').submit();
});
</script>

<?php
$content = ob_get_clean();
require_once('app/Views/layouts/main.php');
?>
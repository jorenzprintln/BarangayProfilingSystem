<?php
$content = ob_start();
$pendingDocumentRequests = $pendingDocumentRequests ?? [];
$pendingCount = count($pendingDocumentRequests);

// ── Detect the "no linked constituent" error specifically ──
$flashError       = Session::hasFlash('error') ? Session::getFlash('error') : null;
$isNoProfileError = $flashError !== null && str_contains($flashError, 'linked constituent profile');

preg_match('/for user:\s*(\S+?)\./', $flashError ?? '', $profileErrorMatches);
$profileErrorUser = $profileErrorMatches[1] ?? '';

// ── Helper: build a clean full name with optional middle name and suffix ──
$buildFullName = function (array $req): string {
    $first  = trim((string)($req['first_name']  ?? ''));
    $middle = trim((string)($req['middle_name'] ?? ''));
    $last   = trim((string)($req['last_name']   ?? ''));
    $suffix = trim((string)($req['suffix']      ?? ''));

    // Fall back to stored requester_fullname or username if no name parts available
    if ($first === '' && $last === '') {
        return trim((string)($req['requester_fullname'] ?? $req['requested_by'] ?? '—'));
    }

    $mid  = $middle !== '' ? ' ' . $middle : '';
    $suf  = $suffix !== '' ? ', ' . $suffix : '';
    return $first . $mid . ' ' . $last . $suf;
};

// ── Helper: format a Manila-stored datetime (no UTC shift) ──
$formatManila = function ($value): string {
    if (empty($value)) return 'N/A';
    try {
        $dt = new DateTime($value, new DateTimeZone('Asia/Manila'));
        return $dt->format('M d, Y h:i A');
    } catch (Exception $e) {
        return 'N/A';
    }
};
?>

<link rel="stylesheet" href="public/assets/css/manage_users.css?v=<?= time() ?>">

<div class="container-fluid px-4 mt-3">

    <?php if (Session::hasFlash('success')): ?>
        <div id="flash-success" class="alert alert-success-modern alert-modern alert-dismissible fade show" role="alert">
            <strong>Success!</strong> <?= Session::getFlash('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($flashError && !$isNoProfileError): ?>
        <div id="flash-error" class="alert alert-danger-modern alert-modern alert-dismissible fade show" role="alert">
            <strong>Error!</strong> <?= htmlspecialchars($flashError) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <div class="page-title">
            <svg fill="white" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="font-weight-bold mb-0">Document Requests</h3>
                <p class="mb-0 mt-1" style="opacity:0.85;font-size:0.875rem;">Review and process constituent document requests</p>
            </div>
        </div>
    </div>

    <div class="pending-section">
        <div class="pending-header">
            <div class="pending-header-left">
                <i class="fas fa-file-alt"></i>
                <h5>Pending Document Requests</h5>
                <span class="pending-count"><?= $pendingCount ?></span>
            </div>
        </div>

        <div class="pending-body">
            <?php if (!empty($pendingDocumentRequests)): ?>
                <?php foreach ($pendingDocumentRequests as $docRequest): ?>
                    <?php
                        $requestId      = (int)($docRequest['id'] ?? 0);
                        $requestedBy    = (string)($docRequest['requested_by'] ?? '—');
                        $transaction    = (string)($docRequest['transaction'] ?? '—');
                        $purpose        = (string)($docRequest['purpose'] ?? 'N/A');

                        // FIX: No UTC conversion — date_of_transaction is already in Manila time
                        $submittedAt    = $formatManila($docRequest['date_of_transaction'] ?? '');

                        // FIX: Build proper full name — First [Middle] Last[, Suffix]
                        $requesterName  = $buildFullName($docRequest);

                        $modalId        = 'docReviewModal-'   . $requestId;
                        $rejectModalId  = 'docRejectModal-'   . $requestId;
                        $confirmModalId = 'docConfirmModal-'  . $requestId;
                        $processUrl     = 'index.php?controller=constituentRequests&action=processDocumentRequest&id=' . $requestId;
                    ?>
                    <div class="pending-card">
                        <div class="pending-user-info">
                            <div class="pending-avatar">
                                <?= strtoupper(substr($requestedBy, 0, 1)) ?>
                            </div>
                            <div class="pending-details">
                                <span class="pending-fullname"><?= htmlspecialchars($transaction) ?></span>
                                <span class="pending-username">
                                    <?= htmlspecialchars($requesterName) ?> • Submitted <?= htmlspecialchars($submittedAt) ?>
                                </span>
                            </div>
                        </div>
                        <div class="pending-role">
                            <span class="role-badge role-constituent">
                                <i class="fas fa-file-signature"></i>
                                Document Request
                            </span>
                        </div>
                        <div class="pending-actions" style="display:flex;gap:.5rem;align-items:center;">
                            <button type="button" class="btn btn-action btn-edit"
                                data-toggle="modal" data-target="#<?= $modalId ?>">
                                <i class="fas fa-search"></i> Review
                            </button>
                        </div>
                    </div>

                    <!-- ── Review Modal ── -->
                    <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content approve-modal-content">

                                <div class="modal-header approve-modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-file-alt mr-2"></i>
                                        Document Request Review
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <div class="approve-user-preview mb-3">
                                        <div class="pending-avatar" style="width:48px;height:48px;font-size:1.1rem;">
                                            <?= strtoupper(substr($requesterName, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong><?= htmlspecialchars($requesterName) ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($submittedAt) ?>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <div class="p-2" style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:.5rem;">
                                                <div style="font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Document Type</div>
                                                <div style="font-size:.92rem;color:#1f2937;margin-top:.2rem;"><?= htmlspecialchars($transaction) ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="p-2" style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:.5rem;">
                                                <div style="font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Requested By</div>
                                                <div style="font-size:.92rem;color:#1f2937;margin-top:.2rem;">
                                                    <?= htmlspecialchars($requesterName) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="p-2" style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:.5rem;">
                                                <div style="font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Purpose</div>
                                                <div style="font-size:.92rem;color:#1f2937;margin-top:.2rem;"><?= htmlspecialchars($purpose) ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="p-2" style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:.5rem;">
                                                <div style="font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Date Submitted</div>
                                                <div style="font-size:.92rem;color:#1f2937;margin-top:.2rem;"><?= htmlspecialchars($submittedAt) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer" style="justify-content:flex-end;">
                                    <div style="display:flex;gap:.5rem;">
                                        <button type="button" class="btn btn-action btn-reject"
                                            data-dismiss="modal"
                                            data-toggle="modal"
                                            data-target="#<?= $rejectModalId ?>">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                        <button type="button" class="btn btn-action btn-approve"
                                            data-dismiss="modal"
                                            data-toggle="modal"
                                            data-target="#<?= $confirmModalId ?>">
                                            <i class="fas fa-check"></i> Process Request
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ── Process Confirmation Modal ── -->
                    <div class="modal fade" id="<?= $confirmModalId ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content" style="border:none;border-radius:1rem;overflow:hidden;">

                                <div class="modal-header" style="background:linear-gradient(135deg,#22c55e 0%,#16a34a 100%);border:none;padding:1.25rem 1.5rem;">
                                    <h5 class="modal-title" style="color:white;font-weight:700;display:flex;align-items:center;gap:.5rem;">
                                        <i class="fas fa-file-signature"></i> Confirm Process Request
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:.8;">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body" style="padding:1.5rem;background:#f0fdf4;">
                                    <div style="display:flex;align-items:center;gap:.75rem;background:white;border-radius:.75rem;padding:1rem;margin-bottom:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,.06);">
                                        <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#22c55e,#16a34a);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:1rem;flex-shrink:0;">
                                            <?= strtoupper(substr($requesterName, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight:700;color:#1e293b;font-size:.9rem;"><?= htmlspecialchars($requesterName) ?></div>
                                            <div style="font-size:.8rem;color:#64748b;"><?= htmlspecialchars($transaction) ?></div>
                                        </div>
                                    </div>
                                    <p style="font-size:.9rem;color:#374151;margin-bottom:.75rem;">
                                        Are you sure you want to process this request? The document will open in a new tab and this request will be marked as processed.
                                    </p>
                                    <div class="alert alert-success mb-0" style="border-radius:.6rem;font-size:.875rem;background:#dcfce7;border:1px solid #86efac;color:#166534;">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        <strong>Note:</strong> The generated document will open in a new tab for printing or saving.
                                    </div>
                                </div>

                                <div class="modal-footer" style="background:#f0fdf4;border-top:1px solid #bbf7d0;padding:1rem 1.5rem;gap:.5rem;">
                                    <button type="button" class="btn btn-cancel"
                                        data-dismiss="modal"
                                        data-toggle="modal"
                                        data-target="#<?= $modalId ?>">
                                        <i class="fas fa-arrow-left mr-1"></i> Back
                                    </button>
                                    <a href="<?= htmlspecialchars($processUrl) ?>"
                                        target="_blank"
                                        class="btn js-confirm-process"
                                        data-modal-id="<?= $confirmModalId ?>"
                                        style="background:linear-gradient(135deg,#22c55e,#16a34a);color:white;border:none;border-radius:.6rem;font-weight:600;padding:.6rem 1.4rem;display:inline-flex;align-items:center;gap:.4rem;">
                                        <i class="fas fa-check"></i> Yes, Process Request
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ── Reject Modal ── -->
                    <div class="modal fade" id="<?= $rejectModalId ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">

                                <div class="modal-header" style="background:linear-gradient(135deg,#ef4444 0%,#dc2626 100%);color:#fff;">
                                    <h5 class="modal-title">
                                        <i class="fas fa-times-circle mr-2"></i> Confirm Rejection
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <form method="POST" action="index.php?controller=constituentRequests&action=rejectDocumentRequest">
                                    <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                                    <div class="modal-body">
                                        <p class="mb-2">
                                            Reject the <strong><?= htmlspecialchars($transaction) ?></strong>
                                            request from <strong><?= htmlspecialchars($requesterName) ?></strong>?
                                        </p>
                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold" style="font-size:.85rem;color:#374151;">
                                                Rejection Reason <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="reason" class="form-control" rows="3"
                                                placeholder="Explain why this request is being rejected..."
                                                required></textarea>
                                        </div>
                                        <input type="hidden" name="request_id" value="<?= $requestId ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-action btn-reject">
                                            <i class="fas fa-times"></i> Yes, Reject
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5" style="color:#6b7280;">
                    <i class="fas fa-inbox" style="font-size:2rem;opacity:.7;"></i>
                    <h5 class="mt-3 mb-1">No pending document requests</h5>
                    <p class="mb-0">New constituent document requests will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ── No Linked Profile Error Modal ── -->
<div class="modal fade" id="noProfileErrorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border:none;border-radius:1rem;overflow:hidden;">

            <div class="modal-header" style="background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);border:none;padding:1.25rem 1.5rem;">
                <h5 class="modal-title" style="color:white;font-weight:700;display:flex;align-items:center;gap:.5rem;">
                    <i class="fas fa-exclamation-triangle"></i> No Constituent Profile Linked
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="padding:1.5rem;background:#fffbeb;">
                <div style="display:flex;align-items:center;gap:.75rem;background:white;border-radius:.75rem;padding:1rem;margin-bottom:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,.06);">
                    <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:1.1rem;flex-shrink:0;">
                        <?= strtoupper(substr($profileErrorUser ?: '?', 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-weight:700;color:#1e293b;font-size:.9rem;">
                            @<?= htmlspecialchars($profileErrorUser ?: 'unknown') ?>
                        </div>
                        <div style="font-size:.8rem;color:#64748b;">Account has no linked constituent record</div>
                    </div>
                </div>

                <p style="font-size:.9rem;color:#374151;margin-bottom:1rem;">
                    This document request cannot be processed because the account
                    <strong>@<?= htmlspecialchars($profileErrorUser ?: 'unknown') ?></strong>
                    is not linked to any constituent profile in the system.
                </p>

                <div class="alert mb-0" style="border-radius:.6rem;font-size:.875rem;background:#fef3c7;border:1px solid #fcd34d;color:#92400e;">
                    <strong><i class="fas fa-tools mr-1"></i> How to fix this:</strong>
                    <ol style="margin:.5rem 0 0 1rem;padding:0;">
                        <li>Go to <strong>Manage Accounts</strong></li>
                        <li>Find the account <strong>@<?= htmlspecialchars($profileErrorUser ?: 'unknown') ?></strong></li>
                        <li>Link it to the matching constituent record</li>
                        <li>Come back and process this request again</li>
                    </ol>
                </div>
            </div>

            <div class="modal-footer" style="background:#fffbeb;border-top:1px solid #fde68a;padding:1rem 1.5rem;gap:.5rem;">
                <button type="button" class="btn btn-cancel" data-dismiss="modal">Close</button>
                <a href="index.php?controller=users&tab=constituent"
                    class="btn"
                    style="background:linear-gradient(135deg,#f59e0b,#d97706);color:white;border:none;border-radius:.6rem;font-weight:600;padding:.6rem 1.4rem;display:inline-flex;align-items:center;gap:.4rem;">
                    <i class="fas fa-users-cog mr-1"></i> Go to Manage Accounts
                </a>
            </div>

        </div>
    </div>
</div>

<script>
setTimeout(function () { $('.alert-modern').fadeOut(500); }, 4000);

<?php if ($isNoProfileError): ?>
$(document).ready(function () {
    $('#noProfileErrorModal').modal('show');
});
<?php endif; ?>

$(document).on('click', '.js-confirm-process', function () {
    var modalId = $(this).data('modal-id');
    $('#' + modalId).modal('hide');
    setTimeout(function () {
        window.location.reload();
    }, 1500);
});
</script>

<?php
$content = ob_get_clean();
require_once('app/Views/layouts/main.php');
?>
<?php
$content = ob_start();
$pendingProfileCount = count($pendingProfileRequests ?? []);
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

    <?php if (Session::hasFlash('error')): ?>
        <div id="flash-error" class="alert alert-danger-modern alert-modern alert-dismissible fade show" role="alert">
            <strong>Error!</strong> <?= Session::getFlash('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <div class="page-title">
            <svg fill="white" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.97 5.97 0 00-.75-2.906A3.005 3.005 0 0119 17v1h-3zM4.75 14.094A5.973 5.973 0 004 17v1H1v-1a3 3 0 013.75-2.906z"/>
            </svg>
            <div>
                <h3 class="font-weight-bold mb-0">Profile Requests</h3>
                <p class="mb-0 mt-1" style="opacity:0.85;font-size:0.875rem;">Review constituent profile submissions for approval</p>
            </div>
        </div>
    </div>

    <div class="pending-section">
        <div class="pending-header">
            <div class="pending-header-left">
                <i class="fas fa-id-card"></i>
                <h5>Pending Profile Submissions</h5>
                <span class="pending-count"><?= $pendingProfileCount ?></span>
            </div>
        </div>

        <div class="pending-body">
            <?php if (!empty($pendingProfileRequests)): ?>
                <?php foreach ($pendingProfileRequests as $profileRequest): ?>
                    <?php
                        $displayName = $profileRequest['fullname'] ?: $profileRequest['username'];
                        $submittedAt = !empty($profileRequest['created_at']) ? date('M d, Y h:i A', strtotime($profileRequest['created_at'])) : 'N/A';
                        $payload = $profileRequest['payload_data'] ?? [];
                        $reviewModalId = 'profileReviewModal-' . (int)$profileRequest['id'];

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

                        $fieldPreview = [
                            'PhilSys Card No.' => $payload['psn'] ?? '',
                            'Last Name' => $payload['last_name'] ?? '',
                            'Given Name' => $payload['first_name'] ?? '',
                            'Middle Name' => $payload['middle_name'] ?? '',
                            'Suffix' => $payload['suffix'] ?? '',
                            'Sex' => $payload['sex'] ?? '',
                            'Birthdate' => $payload['birthdate'] ?? '',
                            'Birth Place' => $payload['birthplace'] ?? '',
                            'Civil Status' => $payload['civil_status'] ?? '',
                            'Religion' => $payload['religion'] ?? '',
                            'Citizenship' => $payload['citizenship'] ?? '',
                            'Occupation' => $payload['occupation'] ?? '',
                            'Contact Number' => $payload['contact'] ?? '',
                            'Email Address' => $payload['email'] ?? '',
                            'Educational Attainment' => $educationMap[(string)($payload['education_attainment'] ?? '')] ?? ($payload['education_attainment'] ?? ''),
                            'Graduate' => $payload['is_graduate'] ?? '',
                            'Registered Voter' => $payload['registered_voter'] ?? '',
                            'Classifications' => !empty($payload['classification_names']) ? implode(', ', $payload['classification_names']) : '',
                        ];
                    ?>
                    <div class="pending-card">
                        <div class="pending-user-info">
                            <div class="pending-avatar">
                                <?= strtoupper(substr($displayName, 0, 1)) ?>
                            </div>
                            <div class="pending-details">
                                <span class="pending-fullname"><?= htmlspecialchars($displayName ?: '—') ?></span>
                                <span class="pending-username">@<?= htmlspecialchars($profileRequest['username']) ?> • Submitted <?= htmlspecialchars($submittedAt) ?></span>
                            </div>
                        </div>
                        <div class="pending-role">
                            <span class="role-badge role-constituent">
                                <i class="fas fa-user-edit"></i>
                                Profile Update
                            </span>
                        </div>
                        <div class="pending-actions" style="display:flex;gap:.5rem;align-items:center;">
                            <button type="button" class="btn btn-action btn-edit" data-toggle="modal" data-target="#<?= $reviewModalId ?>">
                                <i class="fas fa-search"></i> Review Submission
                            </button>
                        </div>
                    </div>

                    <div class="modal fade" id="<?= $reviewModalId ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content approve-modal-content">
                                <div class="modal-header approve-modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-id-card mr-2"></i>
                                        Profile Submission Review
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="approve-user-preview mb-3">
                                        <div class="pending-avatar" style="width:48px;height:48px;font-size:1.1rem;">
                                            <?= strtoupper(substr($displayName, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong><?= htmlspecialchars($displayName ?: '—') ?></strong>
                                            <br><small class="text-muted">@<?= htmlspecialchars($profileRequest['username']) ?> • Submitted <?= htmlspecialchars($submittedAt) ?></small>
                                        </div>
                                    </div>

                                    <div class="row">
    <?php foreach ($fieldPreview as $label => $value): ?>
        <?php if ($label === 'Classifications') continue; ?>
        <div class="col-md-6 mb-2">
            <div class="p-2" style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:.5rem;min-height:74px;">
                <div style="font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;"><?= htmlspecialchars($label) ?></div>
                <div style="font-size:.92rem;color:#1f2937;margin-top:.2rem;"><?= htmlspecialchars($value !== '' ? (string)$value : '—') ?></div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Classifications — full width so all badges have room to wrap -->
    <?php
    $classNames  = $payload['classification_names']   ?? [];
    $classOrgIds = $payload['classification_org_ids'] ?? [];
    ?>
    <div class="col-md-6 mb-2">
        <div class="p-2" style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:.5rem;min-height:54px;">
            <div style="font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.4rem;">
                Classifications
            </div>
            <?php if (!empty($classNames)): ?>
                <div style="display:flex;flex-wrap:wrap;gap:.4rem;">
                    <?php foreach ($classNames as $classId => $cn):
                        $orgId = $classOrgIds[$classId] ?? null;
                    ?>
                        <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.25rem .65rem;background:#e0e7ff;color:#3730a3;border-radius:.375rem;font-size:.82rem;font-weight:600;">
                            <?= htmlspecialchars($cn) ?>
                            <?php if (!empty($orgId)): ?>
                                <span style="background:#c7d2fe;color:#312e81;padding:.1rem .35rem;border-radius:.25rem;font-size:.75rem;font-weight:700;letter-spacing:.02em;">
                                    ID: <?= htmlspecialchars($orgId) ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <span style="font-size:.92rem;color:#6b7280;">—</span>
            <?php endif; ?>
        </div>
    </div>
</div>

                                    <hr>
                                    <div class="form-group mb-0">
                                        <label for="reject-reason-<?= (int)$profileRequest['id'] ?>" class="font-weight-bold" style="font-size:.85rem;color:#374151;">
                                            Reject Reason (required when rejecting)
                                        </label>
                                        <textarea form="reject-profile-form-<?= (int)$profileRequest['id'] ?>" id="reject-reason-<?= (int)$profileRequest['id'] ?>" name="reason" class="form-control" rows="3" placeholder="Explain what needs to be corrected."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer" style="justify-content:flex-end;">
                                    <div style="display:flex;gap:.5rem;">
                                        <button
                                            type="button"
                                            class="btn btn-action btn-reject js-open-reject-confirm"
                                            data-request-id="<?= (int)$profileRequest['id'] ?>"
                                            data-display-name="<?= htmlspecialchars($displayName ?: '—', ENT_QUOTES, 'UTF-8') ?>"
                                            data-review-modal-id="<?= $reviewModalId ?>"
                                            data-reason-target="reject-reason-<?= (int)$profileRequest['id'] ?>">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-action btn-approve js-open-approve-confirm"
                                            data-request-id="<?= (int)$profileRequest['id'] ?>"
                                            data-display-name="<?= htmlspecialchars($displayName ?: '—', ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5" style="color:#6b7280;">
                    <i class="fas fa-inbox" style="font-size:2rem;opacity:.7;"></i>
                    <h5 class="mt-3 mb-1">No pending profile requests</h5>
                    <p class="mb-0">New constituent profile submissions will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Approve Confirmation Modal -->
<div class="modal fade" id="approveProfileConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg, #06d6a0 0%, #059669 100%);color:#fff;">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle mr-2"></i>
                    Confirm Approval
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="index.php?controller=users&action=approveProfileRequest" id="approveProfileConfirmForm">
                <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                <div class="modal-body">
                    <p class="mb-2">Approve this profile submission for <strong id="approveConfirmName">—</strong>?</p>
                    <div class="alert alert-success mb-0" role="alert" style="border-radius:.5rem;">
                        This will apply the submitted profile changes immediately.
                    </div>
                    <input type="hidden" name="request_id" id="approveConfirmRequestId" value="">
                    <input type="hidden" name="redirect_to" value="index.php?controller=constituentRequests&action=profileRequests">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-action btn-approve">
                        <i class="fas fa-check"></i> Yes, Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Confirmation Modal -->
<div class="modal fade" id="rejectProfileConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%);color:#fff;">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle mr-2"></i>
                    Confirm Rejection
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="index.php?controller=users&action=rejectProfileRequest" id="rejectProfileConfirmForm">
                <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                <div class="modal-body">
                    <p class="mb-2">Reject this profile submission for <strong id="rejectConfirmName">—</strong>?</p>
                    <div class="p-2 mb-2" style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:.5rem;">
                        <div style="font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Reason to send back</div>
                        <div id="rejectConfirmReasonPreview" style="font-size:.92rem;color:#1f2937;margin-top:.2rem;">—</div>
                    </div>
                    <div class="alert alert-warning mb-0" role="alert" style="border-radius:.5rem;">
                        The user will be notified to correct and re-submit profile details.
                    </div>
                    <input type="hidden" name="request_id" id="rejectConfirmRequestId" value="">
                    <input type="hidden" name="reason" id="rejectConfirmReason" value="">
                    <input type="hidden" name="redirect_to" value="index.php?controller=constituentRequests&action=profileRequests">
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

<!-- Missing Reject Reason Modal -->
<div class="modal fade" id="rejectReasonRequiredModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%);color:#fff;">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Reject Reason Required
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Please provide a reject reason before continuing.</p>
                <div class="alert alert-warning mb-0" role="alert" style="border-radius:.5rem;">
                    Add clear feedback so the constituent knows what to correct.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-dismiss="modal">Okay</button>
            </div>
        </div>
    </div>
</div>

<script>
setTimeout(function() {
    $('.alert-modern').fadeOut(500);
}, 4000);

var pendingRejectReasonInputId = '';
var pendingReviewModalId = '';

$(document).on('click', '.js-open-approve-confirm', function () {
    var requestId = $(this).data('request-id');
    var displayName = $(this).data('display-name') || '—';

    $('#approveConfirmRequestId').val(requestId);
    $('#approveConfirmName').text(displayName);
    $('#approveProfileConfirmModal').modal('show');
});

$(document).on('click', '.js-open-reject-confirm', function () {
    var requestId = $(this).data('request-id');
    var displayName = $(this).data('display-name') || '—';
    var reasonTarget = $(this).data('reason-target');
    var $reasonInput = $('#' + reasonTarget);
    var reason = $.trim($reasonInput.val());

    if (!reason) {
        $reasonInput.addClass('is-invalid');
        if (!$reasonInput.next('.invalid-feedback').length) {
            $reasonInput.after('<div class="invalid-feedback">This field is required when rejecting.</div>');
        }
        $reasonInput.trigger('focus');
        return;
    }

    $reasonInput.removeClass('is-invalid');
    $reasonInput.next('.invalid-feedback').remove();

    $('#rejectConfirmRequestId').val(requestId);
    $('#rejectConfirmName').text(displayName);
    $('#rejectConfirmReason').val(reason);
    $('#rejectConfirmReasonPreview').text(reason);
    $('#rejectProfileConfirmModal').modal('show');
});

$(document).on('input', 'textarea[id^="reject-reason-"]', function () {
    if ($.trim($(this).val())) {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    }
});
</script>

<?php
$content = ob_get_clean();
require_once('app/Views/layouts/main.php');
?>

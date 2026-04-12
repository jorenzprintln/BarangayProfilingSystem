<?php
$content = ob_start();
$profileRequests  = $profileRequests  ?? [];
$documentRequests = $documentRequests ?? [];
$vehicleRequests  = $vehicleRequests  ?? [];

$statusBadgeClass = [
    'pending'  => 'warning',
    'approved' => 'success',
    'rejected' => 'danger',
];

$formatUTCtoManila = function ($value) {
    if (empty($value)) return 'N/A';
    try {
        $dt = new DateTime($value, new DateTimeZone('Asia/Manila'));
        return $dt->format('M d, Y h:i A');
    } catch (Exception $e) {
        return 'N/A';
    }
};

$formatManilaStored = function ($value) {
    if (empty($value)) return 'N/A';
    try {
        $dt = new DateTime($value, new DateTimeZone('Asia/Manila'));
        return $dt->format('M d, Y h:i A');
    } catch (Exception $e) {
        return 'N/A';
    }
};

$activeTab = strtolower((string)($_GET['tab'] ?? 'profile'));
if (!in_array($activeTab, ['profile', 'document', 'vehicle'], true)) {
    $activeTab = 'profile';
}

if ($activeTab === 'profile' && !empty($profileRequests[0])) {
    $latestRequest = $profileRequests[0];
    $latestStatus  = strtolower((string)($latestRequest['status'] ?? ''));
    if (in_array($latestStatus, ['approved', 'rejected'], true)) {
        $_SESSION['constituent_profile_status_seen_token'] = implode('|', [
            (string)($latestRequest['id']          ?? ''),
            $latestStatus,
            (string)($latestRequest['reviewed_at'] ?? ''),
            (string)($latestRequest['updated_at']  ?? ''),
        ]);
    }
}
?>

<link rel="stylesheet" href="public/assets/css/constituent_my_requests.css?v=<?= time() ?>">

<div class="container-fluid px-4 mt-3 my-requests-page">
    <?php if (Session::hasFlash('success')): ?>
        <div id="flash-success" class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> <?= Session::getFlash('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (Session::hasFlash('error')): ?>
        <div id="flash-error" class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> <?= Session::getFlash('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="my-requests-header">
        <div class="my-requests-header-inner">
            <div class="my-requests-icon"><i class="fas fa-inbox"></i></div>
            <div>
                <h3>My Requests</h3>
                <p>Track all your profile, document, and vehicle submissions in one place.</p>
            </div>
        </div>
    </div>

    <div class="my-requests-panel mb-3">
        <div class="my-requests-body">

            <?php if ($activeTab === 'profile'): ?>

                <?php if (!empty($profileRequests)): ?>
                    <div class="my-request-list">
                        <?php foreach ($profileRequests as $request): ?>
                            <?php
                                $status        = strtolower(trim((string)($request['status'] ?? '')));
                                $displayStatus = ucfirst($status);
                                $badgeClass    = $statusBadgeClass[$status] ?? 'secondary';
                                $submittedAt   = $formatUTCtoManila($request['created_at']  ?? null);
                                $reviewedAt    = $formatUTCtoManila($request['reviewed_at'] ?? null);
                                $classifications = $request['payload_data']['classifications'] ?? [];
                            ?>
                            <div class="my-request-card">
                                <div class="my-request-top">
                                    <div>
                                        <h6 class="my-request-title">Profile Submission</h6>
                                        <p class="my-request-meta">
                                            Submitted <?= htmlspecialchars($submittedAt) ?>
                                            <?php if ($status !== 'pending' && !empty($request['reviewed_at'])): ?>
                                                <br>Reviewed <?= htmlspecialchars($reviewedAt) ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <span class="badge badge-<?= $badgeClass ?> my-request-badge">
                                        <?= htmlspecialchars($displayStatus) ?>
                                    </span>
                                </div>

                                <?php if (!empty($classifications)): ?>
                                    <div class="my-request-classifications">
                                        <div class="my-request-classifications-label">Classifications</div>
                                        <div class="my-request-chip-wrap">
                                            <?php foreach ($classifications as $cls): ?>
                                                <span class="my-request-chip"><?= htmlspecialchars($cls) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($status === 'rejected' && !empty($request['admin_notes'])): ?>
                                    <div class="my-request-note rejected">
                                        <div><i class="fas fa-exclamation-circle"></i> <strong>Reason for Rejection</strong></div>
                                        <div><?= htmlspecialchars($request['admin_notes']) ?></div>
                                        <div class="my-request-cta">
                                            <a href="index.php?controller=constituent&action=profile" class="btn btn-sm btn-danger">
                                                <i class="fas fa-edit mr-1"></i> Update Profile
                                            </a>
                                        </div>
                                    </div>
                                <?php elseif ($status === 'pending'): ?>
                                    <div class="my-request-note pending">
                                        <i class="fas fa-info-circle"></i> Your submission is under review. You can still update and resubmit anytime.
                                    </div>
                                <?php elseif ($status === 'approved'): ?>
                                    <div class="my-request-note approved">
                                        <i class="fas fa-check-circle"></i> Your profile has been approved and updated.
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="my-request-empty">
                        <i class="fas fa-inbox"></i>
                        <h5 class="mt-3 mb-1">No submissions yet</h5>
                        <p class="mb-3">Submit your profile information to get started.</p>
                        <a href="index.php?controller=constituent&action=profile" class="btn btn-primary">
                            <i class="fas fa-edit mr-1"></i> Create/Update Profile
                        </a>
                    </div>
                <?php endif; ?>

            <?php elseif ($activeTab === 'document'): ?>

                <?php if (!empty($documentRequests)): ?>
                    <div class="my-request-list">
                        <?php foreach ($documentRequests as $docReq): ?>
                            <?php
                                $requestedAt = $formatManilaStored($docReq['date_of_transaction'] ?? null);
                                $processedAt = $formatManilaStored($docReq['updated_at']          ?? null);
                                $docStatus   = strtoupper(trim((string)($docReq['generated_by'] ?? '')));
                                $isPending   = $docStatus === 'PENDING';
                                $isRejected  = $docStatus === 'REJECTED';
                                $isProcessed = !$isPending && !$isRejected && $docStatus !== '';
                            ?>
                            <div class="my-request-card">
                                <div class="my-request-top">
                                    <div>
                                        <h6 class="my-request-title">
                                            <?= htmlspecialchars((string)($docReq['transaction'] ?? 'Document Request')) ?>
                                        </h6>
                                        <p class="my-request-meta">
                                            Requested <?= htmlspecialchars($requestedAt) ?>
                                            <?php if (!$isPending && !empty($docReq['updated_at'])): ?>
                                                <br><?= $isRejected ? 'Rejected' : 'Processed' ?>
                                                <?= htmlspecialchars($processedAt) ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <span class="badge <?= $isPending ? 'badge-warning' : ($isRejected ? 'badge-danger' : 'badge-success') ?> my-request-badge">
                                        <?= $isPending ? 'Pending' : ($isRejected ? 'Rejected' : 'Processed') ?>
                                    </span>
                                </div>

                                <div class="my-request-note <?= $isPending ? 'pending' : ($isRejected ? 'rejected' : 'approved') ?>">
                                    <strong>Purpose:</strong> <?= htmlspecialchars((string)($docReq['purpose'] ?? 'N/A')) ?>
                                    <?php if ($isRejected && !empty($docReq['document_location'])): ?>
                                        <div class="mt-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <strong>Reason:</strong> <?= htmlspecialchars((string)$docReq['document_location']) ?>
                                        </div>
                                        <div class="my-request-cta">
                                            <a href="index.php?controller=constituent&action=requestDocument" class="btn btn-sm btn-danger">
                                                <i class="fas fa-redo mr-1"></i> Re-submit Request
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="my-request-empty">
                        <i class="fas fa-file-alt"></i>
                        <h5 class="mt-3 mb-1">No document requests yet</h5>
                        <p class="mb-3">Submit your first document request.</p>
                        <a href="index.php?controller=constituent&action=requestDocument" class="btn btn-primary">
                            <i class="fas fa-file-signature mr-1"></i> Request Document
                        </a>
                    </div>
                <?php endif; ?>

            <?php else: /* vehicle tab */ ?>

                <?php if (!empty($vehicleRequests)): ?>
                    <div class="my-request-list">
                        <?php foreach ($vehicleRequests as $req): ?>
                            <?php
                                $status   = $req['status'];
                                $isNew    = in_array($status, ['approved','rejected']) && empty($req['seen_at']);
                                $badgeClass = $statusBadgeClass[$status] ?? 'secondary';
                                $submittedAt = $formatManilaStored($req['created_at'] ?? null);
                                $reviewedAt  = $formatManilaStored($req['reviewed_at'] ?? null);
                            ?>
                            <div class="my-request-card" style="<?= $isNew ? 'border-left:3px solid #f59e0b;' : '' ?>">
                                <div class="my-request-top">
                                    <div>
                                        <h6 class="my-request-title">
                                            <?= htmlspecialchars(($req['make'] ?? '—') . ' ' . ($req['model'] ?? '')) ?>
                                            <?php if (!empty($req['plate_number'])): ?>
                                                <span style="font-family:'Courier New',monospace;font-size:.78rem;font-weight:700;background:#1e293b;color:#f1f5f9;padding:.1rem .4rem;border-radius:4px;border:2px solid #334155;margin-left:.4rem;">
                                                    <?= htmlspecialchars($req['plate_number']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </h6>
                                        <p class="my-request-meta">
                                            <?= htmlspecialchars($req['vehicle_type'] ?? '—') ?>
                                            <?php if (!empty($req['year'])): ?> · <?= htmlspecialchars($req['year']) ?><?php endif; ?>
                                            <?php if (!empty($req['color'])): ?> · <?= htmlspecialchars($req['color']) ?><?php endif; ?>
                                            <br>Submitted <?= htmlspecialchars($submittedAt) ?>
                                            <?php if ($status !== 'pending' && !empty($req['reviewed_at'])): ?>
                                                <br>Reviewed <?= htmlspecialchars($reviewedAt) ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.4rem;">
                                        <span class="badge badge-<?= $badgeClass ?> my-request-badge">
                                            <?= ucfirst($status) ?>
                                        </span>
                                        <?php if ($isNew): ?>
                                            <span style="background:#f59e0b;color:#fff;font-size:.62rem;font-weight:700;padding:.1rem .4rem;border-radius:999px;">NEW</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if ($status === 'approved'): ?>
                                    <div class="my-request-note approved">
                                        <i class="fas fa-check-circle"></i> Your vehicle has been approved and added to the barangay registry.
                                        <?php if (!empty($req['secretary_note'])): ?>
                                            <div class="mt-1"><strong>Note:</strong> <?= htmlspecialchars($req['secretary_note']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif ($status === 'rejected'): ?>
                                    <div class="my-request-note rejected">
                                        <div><i class="fas fa-exclamation-circle"></i> <strong>Reason for Rejection</strong></div>
                                        <div><?= htmlspecialchars($req['secretary_note'] ?? 'No reason provided.') ?></div>
                                        <div class="my-request-cta">
                                            <a href="index.php?controller=constituent&action=requestVehicle" class="btn btn-sm btn-danger">
                                                <i class="fas fa-redo mr-1"></i> Re-submit Request
                                            </a>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="my-request-note pending">
                                        <i class="fas fa-info-circle"></i> Your vehicle registration is under review by the barangay secretary.
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="my-request-empty">
                        <i class="fas fa-car"></i>
                        <h5 class="mt-3 mb-1">No vehicle requests yet</h5>
                        <p class="mb-3">Submit a request to register your vehicle with the barangay.</p>
                        <a href="index.php?controller=constituent&action=requestVehicle" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i> Register a Vehicle
                        </a>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </div>
</div>

<script>
setTimeout(function() { $('.alert-modern').fadeOut(500); }, 4000);
</script>

<?php
$content = ob_get_clean();
require_once('app/Views/layouts/main.php');
?>
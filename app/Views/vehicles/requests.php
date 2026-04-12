<?php
ob_start();

$requests     = $requests     ?? [];
$search       = $search       ?? '';
$filterStatus = $filterStatus ?? 'pending';

$statusOptions = [
    'pending'  => 'Pending',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
    ''         => 'All',
];

$pendingCount = count(array_filter($requests, fn($r) => $r['status'] === 'pending'));
?>

<link rel="stylesheet" href="public/assets/css/constituents_index.css">
<link rel="stylesheet" href="public/assets/css/manage_users.css?v=<?= time() ?>">

<div class="container-fluid px-4 mt-3">

    <?php if (Session::hasFlash('success')): ?>
        <div id="flash-success" class="alert alert-success-modern alert-modern alert-dismissible fade show" role="alert">
            <strong>Success!</strong> <?= Session::getFlash('success') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    <?php if (Session::hasFlash('error')): ?>
        <div id="flash-error" class="alert alert-danger alert-modern alert-dismissible fade show" role="alert">
            <strong>Error!</strong> <?= Session::getFlash('error') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="page-header" style="background:linear-gradient(135deg,#4361ee 0%,#3651d4 60%,#2c46d4 100%) !important;">
        <div class="page-title">
            <svg fill="white" viewBox="0 0 24 24" width="28" height="28">
                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                <circle cx="7.5" cy="14.5" r="1.5"/>
                <circle cx="16.5" cy="14.5" r="1.5"/>
            </svg>
            <div>
                <h3 class="font-weight-bold mb-0">Vehicle Registration Requests</h3>
                <p class="mb-0 mt-1" style="opacity:.9;font-size:.9rem;">Review and approve constituent vehicle registration submissions</p>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="pending-section">
        <div class="pending-header">
            <div class="pending-header-left">
                <i class="fas fa-car"></i>
                <h5>
                    <?php if ($filterStatus === 'pending'): ?>
                        Pending Vehicle Requests
                    <?php elseif ($filterStatus === 'approved'): ?>
                        Approved Vehicle Requests
                    <?php elseif ($filterStatus === 'rejected'): ?>
                        Rejected Vehicle Requests
                    <?php else: ?>
                        All Vehicle Requests
                    <?php endif; ?>
                </h5>
                <span class="pending-count"><?= count($requests) ?></span>
            </div>
        </div>

        <div class="pending-body">
            <?php if (!empty($requests)): ?>
                <?php foreach ($requests as $req): ?>
                    <?php
                        $reqId      = (int)($req['id'] ?? 0);
                        $status     = $req['status'] ?? 'pending';
                        $ownerName  = htmlspecialchars($req['owner_name'] ?? '—');
                        $username   = htmlspecialchars($req['username'] ?? '—');
                        $plate      = htmlspecialchars($req['plate_number'] ?? '—');
                        $make       = htmlspecialchars($req['make'] ?? '—');
                        $model      = htmlspecialchars($req['model'] ?? '');
                        $year       = htmlspecialchars($req['year'] ?? '');
                        $type       = htmlspecialchars($req['vehicle_type'] ?? '—');
                        $submittedAt = !empty($req['created_at'])
                            ? date('M d, Y h:i A', strtotime($req['created_at']))
                            : 'N/A';

                        $avatarChar = strtoupper(substr($req['owner_name'] ?? $req['username'] ?? '?', 0, 1));

                        $badgeCss = match($status) {
                            'approved' => 'background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;',
                            'rejected' => 'background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;',
                            default    => 'background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;',
                        };

                        $reviewModalId  = 'vReviewModal-'  . $reqId;
                        $approveModalId = 'vApproveModal-' . $reqId;
                        $rejectModalId  = 'vRejectModal-'  . $reqId;
                    ?>

                    <div class="pending-card">
                        <div class="pending-user-info">
                            <div class="pending-avatar">
                                <?= $avatarChar ?>
                            </div>
                            <div class="pending-details">
                                <span class="pending-fullname">
                                    <?= !empty($req['plate_number']) ? $plate : ($make . ($model ? ' ' . $model : '')) ?>
                                </span>
                                <span class="pending-username">
                                    <?= $ownerName ?> • Submitted <?= $submittedAt ?>
                                </span>
                            </div>
                        </div>
                        <div class="pending-role">
                            <span class="role-badge role-constituent">
                                <i class="fas fa-car"></i>
                                Vehicle Request
                            </span>
                        </div>
                        <div class="pending-actions" style="display:flex;gap:.5rem;align-items:center;">
                            <button type="button" class="btn btn-action btn-edit"
                                data-toggle="modal" data-target="#<?= $reviewModalId ?>">
                                <i class="fas fa-search"></i> Review
                            </button>
                        </div>
                    </div>

                    <!-- ── Review Modal ── -->
                    <div class="modal fade" id="<?= $reviewModalId ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content approve-modal-content">

                                <div class="modal-header approve-modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-car mr-2"></i>
                                        Vehicle Request Review
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <div class="approve-user-preview mb-3">
                                        <div class="pending-avatar" style="width:48px;height:48px;font-size:1.1rem;">
                                            <?= $avatarChar ?>
                                        </div>
                                        <div>
                                            <strong><?= $ownerName ?></strong>
                                            <br>
                                            <small class="text-muted">@<?= $username ?> • <?= $submittedAt ?></small>
                                        </div>
                                    </div>

                                    <?php
                                    $detailFields = [
                                        ['Plate Number',  $req['plate_number']   ?? '—'],
                                        ['OR Number',     $req['or_number']      ?? '—'],
                                        ['CR Number',     $req['cr_number']      ?? '—'],
                                        ['Vehicle Type',  $req['vehicle_type']   ?? '—'],
                                        ['Vehicle Use',   $req['vehicle_use']    ?? '—'],
                                        ['Make',          $req['make']           ?? '—'],
                                        ['Model',         $req['model']          ?? '—'],
                                        ['Year',          $req['year']           ?? '—'],
                                        ['Color',         $req['color']          ?? '—'],
                                        ['Fuel Type',     $req['fuel_type']      ?? '—'],
                                        ['Transmission',  $req['transmission']   ?? '—'],
                                        ['Engine No.',    $req['engine_number']  ?? '—'],
                                        ['Chassis No.',   $req['chassis_number'] ?? '—'],
                                        ['Notes',         $req['notes']          ?? '—'],
                                    ];
                                    ?>
                                    <div class="row">
                                        <?php foreach ($detailFields as [$label, $value]): ?>
                                            <div class="col-md-6 mb-2">
                                                <div class="p-2" style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:.5rem;">
                                                    <div style="font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">
                                                        <?= htmlspecialchars($label) ?>
                                                    </div>
                                                    <div style="font-size:.92rem;color:#1f2937;margin-top:.2rem;">
                                                        <?= htmlspecialchars((string)$value) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <?php if (!empty($req['secretary_note'])): ?>
                                        <div class="mt-2 p-2" style="background:#fefce8;border:1px solid #fde68a;border-radius:.5rem;">
                                            <div style="font-size:.78rem;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.04em;">Secretary Note</div>
                                            <div style="font-size:.9rem;color:#78350f;margin-top:.2rem;">
                                                <?= htmlspecialchars($req['secretary_note']) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="modal-footer" style="justify-content:flex-end;">
                                    <?php if ($status === 'pending'): ?>
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
                                                data-target="#<?= $approveModalId ?>">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ── Approve Confirmation Modal ── -->
                    <div class="modal fade" id="<?= $approveModalId ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content" style="border:none;border-radius:1rem;overflow:hidden;">

                                <div class="modal-header" style="background:linear-gradient(135deg,#22c55e 0%,#16a34a 100%);border:none;padding:1.25rem 1.5rem;">
                                    <h5 class="modal-title" style="color:white;font-weight:700;display:flex;align-items:center;gap:.5rem;">
                                        <i class="fas fa-check-circle"></i> Approve Vehicle Request
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:.8;">
                                        <span>&times;</span>
                                    </button>
                                </div>

                                <form method="POST" action="index.php?controller=vehicles&action=approveVehicleRequest">
                                    <input type="hidden" name="request_id" value="<?= $reqId ?>">
                                    <div class="modal-body" style="padding:1.5rem;background:#f0fdf4;">
                                        <div style="display:flex;align-items:center;gap:.75rem;background:white;border-radius:.75rem;padding:1rem;margin-bottom:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,.06);">
                                            <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#22c55e,#16a34a);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:1rem;flex-shrink:0;">
                                                <?= $avatarChar ?>
                                            </div>
                                            <div>
                                                <div style="font-weight:700;color:#1e293b;font-size:.9rem;"><?= $ownerName ?></div>
                                                <div style="font-size:.8rem;color:#64748b;"><?= $plate ?> · <?= $make ?><?= $model ? ' ' . $model : '' ?></div>
                                            </div>
                                        </div>
                                        <p style="font-size:.9rem;color:#374151;margin-bottom:.75rem;">
                                            Are you sure you want to <strong>approve</strong> this vehicle registration? The vehicle will be added to the official registry.
                                        </p>
                                        <div class="form-group mb-0">
                                            <label style="font-size:.82rem;font-weight:600;color:#374151;">Secretary Note <span class="text-muted">(optional)</span></label>
                                            <textarea name="secretary_note" class="form-control" rows="2"
                                                placeholder="e.g. Approved — documents verified."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="background:#f0fdf4;border-top:1px solid #bbf7d0;padding:1rem 1.5rem;gap:.5rem;">
                                        <button type="button" class="btn btn-cancel"
                                            data-dismiss="modal"
                                            data-toggle="modal"
                                            data-target="#<?= $reviewModalId ?>">
                                            <i class="fas fa-arrow-left mr-1"></i> Back
                                        </button>
                                        <button type="submit" class="btn"
                                            style="background:linear-gradient(135deg,#22c55e,#16a34a);color:white;border:none;border-radius:.6rem;font-weight:600;padding:.6rem 1.4rem;">
                                            <i class="fas fa-check mr-1"></i> Yes, Approve
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                    <!-- ── Reject Modal ── -->
                    <div class="modal fade" id="<?= $rejectModalId ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content" style="border:none;border-radius:1rem;overflow:hidden;">

                                <div class="modal-header" style="background:linear-gradient(135deg,#ef4444 0%,#dc2626 100%);color:#fff;border:none;padding:1.25rem 1.5rem;">
                                    <h5 class="modal-title" style="font-weight:700;display:flex;align-items:center;gap:.5rem;">
                                        <i class="fas fa-times-circle"></i> Reject Vehicle Request
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:.8;">
                                        <span>&times;</span>
                                    </button>
                                </div>

                                <form method="POST" action="index.php?controller=vehicles&action=rejectVehicleRequest">
                                    <div class="modal-body" style="padding:1.5rem;">
                                        <div style="display:flex;align-items:center;gap:.75rem;background:#fef2f2;border-radius:.75rem;padding:1rem;margin-bottom:1.25rem;">
                                            <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#ef4444,#dc2626);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:1rem;flex-shrink:0;">
                                                <?= $avatarChar ?>
                                            </div>
                                            <div>
                                                <div style="font-weight:700;color:#1e293b;font-size:.9rem;"><?= $ownerName ?></div>
                                                <div style="font-size:.8rem;color:#64748b;"><?= $plate ?> · <?= $make ?><?= $model ? ' ' . $model : '' ?></div>
                                            </div>
                                        </div>
                                        <p style="font-size:.9rem;color:#374151;margin-bottom:.75rem;">
                                            Are you sure you want to <strong>reject</strong> this vehicle registration request?
                                        </p>
                                        <div class="form-group mb-0">
                                            <label style="font-size:.82rem;font-weight:600;color:#374151;">
                                                Reason for Rejection <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="secretary_note" class="form-control" rows="3"
                                                placeholder="Explain why this request is being rejected..." required></textarea>
                                        </div>
                                        <input type="hidden" name="request_id" value="<?= $reqId ?>">
                                    </div>
                                    <div class="modal-footer" style="border-top:1px solid #fecaca;padding:1rem 1.5rem;gap:.5rem;">
                                        <button type="button" class="btn btn-cancel"
                                            data-dismiss="modal"
                                            data-toggle="modal"
                                            data-target="#<?= $reviewModalId ?>">
                                            <i class="fas fa-arrow-left mr-1"></i> Back
                                        </button>
                                        <button type="submit" class="btn btn-action btn-reject">
                                            <i class="fas fa-times mr-1"></i> Yes, Reject
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5" style="color:#6b7280;">
                    <i class="fas fa-car" style="font-size:2rem;opacity:.7;"></i>
                    <h5 class="mt-3 mb-1">No vehicle requests found</h5>
                    <p class="mb-0">No vehicle registration requests match the current filter.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {
    var si = document.getElementById('search-input');
    var st;
    if (si) si.addEventListener('input', function () {
        clearTimeout(st);
        st = setTimeout(function () { document.getElementById('filter-form').submit(); }, 400);
    });
    var fs = document.getElementById('filter-status');
    if (fs) fs.addEventListener('change', function () { document.getElementById('filter-form').submit(); });
});
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
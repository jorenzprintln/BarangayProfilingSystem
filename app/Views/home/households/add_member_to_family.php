<?php $content = ob_start(); ?>

<div class="container-fluid px-4 mt-3">
    <div class="action-bar" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
        <a href="index.php?controller=households&action=view&household_id=<?= $householdId ?>" class="btn-back">
            ← Back to Household
        </a>
    </div>

    <div class="page-header">
    <div class="d-flex align-items-center mt-3">
        <svg width="36" height="36" fill="white" viewBox="0 0 20 20" class="mr-3">
                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
        </svg>
        <div>
            <h3 class="font-weight-bold mb-0">Add Member to Family <?= htmlspecialchars($family['family_name']) ?></h3>
            <p class="mb-0 mt-1" style="opacity: 0.9; font-size: 0.95rem;">Select household members not yet assigned to any family</p>
        </div>
    </div>
    </div>

    <?php if (Session::hasFlash('error')): ?>
        <div class="alert alert-danger mt-3"><?= Session::getFlash('error') ?></div>
    <?php endif; ?>

    <div class="info-card mt-3">
        <div class="info-card-header">
            <div class="info-card-header-left">
                <h5>Available Members</h5>
            </div>
        </div>
        <div class="info-card-body">
            <form action="index.php?controller=family&action=storeMember" method="POST">
                <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
                <input type="hidden" name="family_id"    value="<?= $family['id'] ?>">
                <input type="hidden" name="household_id" value="<?= $householdId ?>">

                <?php if (!empty($constituents)): ?>
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th style="width:50px">Select</th>
                                <th>Full Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($constituents as $c): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="members[]" value="<?= $c['id'] ?>">
                                    </td>
                                    <td><?= htmlspecialchars($c['full_name']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="mt-3">
                        <button type="submit" class="btn-add">Add Selected Members</button>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No available members. All household members are already assigned to a family.</p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
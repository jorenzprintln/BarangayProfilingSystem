<?php
$content = ob_start();
?>
<div class="container-fluid px-4 mt-3">
    <div class="d-flex align-items-center mb-3">
        <a href="index.php?controller=constituents&action=index" class="mr-2">
            <img src="public/assets/icons/back.icon.png" alt="Back" style="width: 32px; height: 32px;">
        </a>
        <h3 class="font-weight-bold mb-0">Removed Constituents</h3>
    </div>
    
    <div class="shadow-sm border">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Sex</th>
                            <th>Age</th>
                            <th>Registered Voter</th>
                            <th>Removed At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($removedConstituents)): ?>
                            <?php foreach ($removedConstituents as $constituent): ?>
                                <tr>
                        <td><?php echo htmlspecialchars($constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name'] . ' ' . $constituent['suffix'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td><?php echo htmlspecialchars($constituent['sex'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <?php
                            if (!empty($constituent['birthdate'])) {
                                try {
                                    $birthDate = new DateTime($constituent['birthdate']);
                                    $today = new DateTime();
                                    $age = $today->diff($birthDate)->y;
                                    echo $age;
                                } catch (Exception $e) {
                                    echo 'Invalid Date';
                                }
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($constituent['registered_voter'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($constituent['removed_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-start">
                                <a href="index.php?controller=constituents&action=restoreConstituent&id=<?php echo $constituent['id']; ?>"
                                    class="btn btn-sm btn-success"
                                    onclick="return confirm('Are you sure you want to restore this constituent?');">
                                    Restore
                                </a>
                            </div>
                        </td>
                        </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No constituents found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
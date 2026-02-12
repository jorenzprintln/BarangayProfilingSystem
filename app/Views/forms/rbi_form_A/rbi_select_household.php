<?php
$content = ob_start();
?>
<div class="container-fluid px-4 mt-4">
    <div class="d-flex align-items-center mb-3">
        <!-- Header Container -->
        <a href="index.php?controller=home&action=forms" class="mr-2">
            <img src="public/assets/icons/back.icon.png" alt="Back" style="width: 32px; height: 32px;">
        </a>
        <h3 class="font-weight-bold mb-0">Select Household for RBI Form A</h3>
    </div>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Household Number</th>
                <th>Head of Household</th>
                <th>Number of Families</th>
                <th>Number of Members</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($households as $household): ?>
                <tr>
                    <td><?= htmlspecialchars($household['household_number'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($household['head_of_household'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($household['number_of_families'] ?? 0) ?></td>
                    <td><?= htmlspecialchars($household['number_of_members'] ?? 0) ?></td>
                    <td>
                        <a href="index.php?controller=households&action=generate_rbi_A&household_id=<?= $household['id'] ?>"
                            class="btn btn-primary btn-sm" target="_blank"
                            onclick="return confirm('Are you sure you want to generate the RBI for this household?');">Generate
                            RBI</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
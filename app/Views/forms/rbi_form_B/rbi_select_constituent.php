<?php
$content = ob_start();
?>

<div class="container-fluid px-4 mt-4">
    <div class="d-flex align-items-center mb-3">
        <a href="index.php?controller=home&action=forms" class="mr-2">
            <img src="public/assets/icons/back.icon.png" alt="Back" style="width: 32px; height: 32px;">
        </a>
        <h3 class="font-weight-bold mb-0">Constituent List</h3>
    </div>
    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>First Nam</th>
                    <th>Last Name</th>
                    <th>Birthdate</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($constituents as $constituent): ?>
                    <tr>
                        <td><?= htmlspecialchars($constituent['id']) ?></td>
                        <td><?= htmlspecialchars($constituent['first_name']) ?></td>
                        <td><?= htmlspecialchars($constituent['last_name']) ?></td>
                        <td><?= htmlspecialchars($constituent['birthdate']) ?></td>
                        <td>
                            <a href="index.php?controller=forms&action=rbi_form_B&id=<?= htmlspecialchars($constituent['id']) ?>"
                                class="btn btn-primary btn-sm" target="_blank"
                                onclick="return confirm('Are you sure you want to generate the RBI for this constituent?');">
                                Generate RBI
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
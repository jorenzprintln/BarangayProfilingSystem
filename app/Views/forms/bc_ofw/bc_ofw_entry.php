<?php
$content = ob_start();
?>
<div class="container-fluid px-4 mt-4">
    <div class="d-flex align-items-center mb-3">
        <!-- Header Container -->
        <a href="index.php?controller=home&action=forms" class="mr-2">
            <img src="public/assets/icons/back.icon.png" alt="Back" style="width: 32px; height: 32px;">
        </a>
        <h3 class="font-weight-bold mb-0">Certification for OFW Residency in Barangay</h3>
    </div>
    <div>
        <!-- Search Bar -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by name...">
        </div>
        <!-- Table for OFW Constituents -->
        <div style="max-height: 600px; overflow-y: auto;">
            <table class="table table-bordered table-striped" id="ofwTable">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ofwConstituents)): ?>
                        <?php foreach ($ofwConstituents as $ofw): ?>
                            <tr>
                                <td><?= htmlspecialchars($ofw['full_name']) ?></td>
                                <td>
                                    <form method="POST" action="index.php?controller=forms&action=processBcOfwEntry"
                                        target="_blank" onsubmit="return confirmSubmission()">
                                        <input type="hidden" name="full_name"
                                            value="<?= htmlspecialchars($ofw['full_name']) ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">SELECT</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center">No OFW constituents found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#ofwTable tbody tr');
        rows.forEach(row => {
            const name = row.cells[0].textContent.toLowerCase();
            row.style.display = name.includes(filter) ? '' : 'none';
        });
    });

    // Confirmation dialog
    function confirmSubmission() {
        return confirm("Are you sure you want to generate the certificate?");
    }
</script>
<?php
$content = ob_get_clean();
require_once("app/Views/layouts/main.php");
?>
<?php
$content = ob_start();
?>
<div class="container-fluid px-4 mt-4">
    <div class="d-flex align-items-center mb-3">
        <!-- Header Container -->
        <a href="index.php?controller=home&action=forms" class="mr-2">
            <img src="public/assets/icons/back.icon.png" alt="Back" style="width: 32px; height: 32px;">
        </a>
        <h3 class="font-weight-bold mb-0">Barangay Certificate</h3>
    </div>

    <div class="shadow">
        <div class="card-body">
            <form action="index.php?controller=forms&action=processBcGeneralEntry" target="_blank" method="POST">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="constituent_id" class="form-label">Person Requesting:</label>
                        <select class="form-control selectpicker" id="constituent_id" name="constituent_id" data-live-search="true" required>
                            <option value="">Select a person</option>
                            <?php foreach ($constituents as $constituent): ?>
                                <option value="<?= $constituent['id'] ?>">
                                    <?= $constituent['last_name'] . ', ' . $constituent['first_name'] . ' ' . $constituent['middle_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="purpose" class="form-label">Purpose of Requesting:</label>
                        <textarea class="form-control" id="purpose" name="purpose" rows="3" required></textarea>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-lg">Generate Certificate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Initialize Bootstrap Select -->
<script>
    $(document).ready(function() {
        $('.selectpicker').selectpicker({
            liveSearch: true,
            size: 10
        });
    });
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
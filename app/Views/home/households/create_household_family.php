<?php
$content = ob_start();
?>
<div class="container-fluid px-4 mt-4">
    <div class="shadow-sm border">
        <div class="card-body">
            <div class="d-flex align-items-center mb-4">
                <a href="index.php?controller=households&action=view&household_id=<?= htmlspecialchars($_GET['household_id'] ?? '') ?>"
                    class="btn btn-secondary">Back</a>
                <h3 class="font-weight-bold m-0 ml-2">Create Family for Household</h3>
            </div>
            
            <form action="index.php?controller=family&action=store&household_id=<?= htmlspecialchars($_GET['household_id'] ?? '') ?>" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="family_name">Family Name</label>
                            <input type="text" class="form-control" id="family_name" name="family_name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="head_constituent_id">Head of Household</label>
                            <select class="form-control" id="head_constituent_id" name="head_constituent_id" required>
                                <option value="">Select Head of Family</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="date_resided">Date Resided</label>
                    <input type="date" class="form-control" id="date_resided" name="date_resided" required>
                </div>
                
                <div class="form-group">
                    <label for="members">Members</label>
                    <div class="form-check border border-dark rounded px-5 py-3" style="max-height: 300px; overflow-y: auto;">
                        <?php if (!empty($constituents)): ?>
                            <?php foreach ($constituents as $constituent): ?>
                                <div class="mb-2">
                                    <input class="form-check-input" type="checkbox" name="members[]" value="<?= $constituent['id'] ?>"
                                        id="member_<?= $constituent['id'] ?>">
                                    <label class="form-check-label" for="member_<?= $constituent['id'] ?>">
                                        <?= htmlspecialchars($constituent['full_name']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center">No constituents found in this household that are not part of a family.</div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Create Family</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const memberCheckboxes = document.querySelectorAll('input[name="members[]"]');
        const headSelect = document.getElementById('head_constituent_id');

        memberCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateHeadOptions);
        });

        function updateHeadOptions() {
            // Clear existing options except the default
            while (headSelect.options.length > 1) {
                headSelect.remove(1);
            }

            // Add options for checked members
            memberCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const label = document.querySelector(`label[for="${checkbox.id}"]`);
                    const option = new Option(label.textContent.trim(), checkbox.value);
                    headSelect.add(option);
                }
            });
        }
    });
</script>
<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
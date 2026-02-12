<?php
$content = ob_start();
?>
<div class="container-fluid px-4 mt-4">
    <?php if (empty($constituents)): ?>
        <div class="shadow-sm border">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <a href="index.php?controller=households&action=view&household_id=<?= htmlspecialchars($_GET['household_id'] ?? '') ?>"
                        class="btn btn-secondary">Back</a>
                    <h3 class="font-weight-bold m-0 ml-2">Add Constituents to Household</h3>
                </div>
                <div class="alert alert-info">
                    There are currently no constituents available.
                </div>
                <a href="index.php?controller=constituents&action=index" class="btn btn-primary">Add Constituents</a>
            </div>
        </div>
    <?php else: ?>
        <div class="shadow-sm border">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <a href="index.php?controller=households&action=view&household_id=<?= htmlspecialchars($_GET['household_id'] ?? '') ?>"
                        class="btn btn-secondary">Back</a>
                    <h3 class="font-weight-bold m-0 ml-2">Add Constituents to Household</h3>
                </div>
                <p>Select constituents to add to the household.</p>
                <input type="text" id="search-constituents" class="form-control mb-3" placeholder="Search constituents...">
                <form action="index.php?controller=households&action=storeConstituents" method="POST">
                    <div>
                        <input type="hidden" name="household_id" value="<?= htmlspecialchars($household_id) ?>">
                        <div class="form-group" style="max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                            <?php foreach ($constituents as $constituent): ?>
                                <div class="mb-3 constituent-item">
                                    <input type="checkbox" id="consti_<?= $constituent['id'] ?>"
                                        name="constituents[<?= $constituent['id'] ?>][selected]" value="1" class="consti-checkbox">
                                    <label for="consti_<?= $constituent['id'] ?>">
                                        <?= htmlspecialchars($constituent['first_name'] . ' ' . $constituent['last_name']) ?>
                                    </label>
                                    <div class="role-section mt-2" style="display: none;">
                                        <div class="mt-2">
                                            <?php if (!$hasHead): // Check if a head already exists ?>
                                                <input type="radio" id="is_head_<?= $constituent['id'] ?>" name="is_head"
                                                    value="<?= $constituent['id'] ?>" class="is-head-radio">
                                                <label for="is_head_<?= $constituent['id'] ?>">Set as Head</label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3" id="submit-btn" disabled>Submit</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    const checkboxes = document.querySelectorAll('.consti-checkbox');
    const submitButton = document.getElementById('submit-btn');
    const searchInput = document.getElementById('search-constituents');
    const constituentItems = document.querySelectorAll('.constituent-item');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const roleSection = this.closest('div').querySelector('.role-section');
            roleSection.style.display = this.checked ? 'block' : 'none';

            // Enable or disable the submit button based on checked checkboxes
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            submitButton.disabled = !anyChecked;
        });
    });

    searchInput.addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        constituentItems.forEach(item => {
            const label = item.querySelector('label').textContent.toLowerCase();
            item.style.display = label.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
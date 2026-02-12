<?php
$content = ob_start();
?>
<div style="display: flex; gap: 20px;">
    <!-- Left: Table of Constituents -->
    <div style="flex: 1;">
        <h3>Available Constituents</h3>
        <input type="text" id="search-input" class="form-control mb-2" placeholder="Search constituents...">
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="constituents-table">
                    <?php foreach ($constituents as $constituent): ?>
                        <tr>
                            <td><?= htmlspecialchars($constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name']) ?>
                            </td>
                            <td>
                                <button type="button" class="select-btn" data-id="<?= $constituent['id'] ?>"
                                    data-name="<?= htmlspecialchars($constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name']) ?>">
                                    SELECT
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Selected Constituents -->
    <div style="flex: 1;">
        <h3>Selected Constituents</h3>
        <form method="POST" action="index.php?controller=officials&action=create" class="p-3 border">
            <div id="selected-constituents" class="list-group mb-3" style="max-height: 400px; overflow-y: auto;"></div>
            <button type="submit" class="btn btn-primary w-100">Appoint Officials</button>
        </form>
    </div>
</div>

<script>
    // Search function
    document.getElementById('search-input').addEventListener('input', function () {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#constituents-table tr');
        rows.forEach(row => {
            const fullName = row.querySelector('td').textContent.toLowerCase();
            row.style.display = fullName.includes(searchValue) ? '' : 'none';
        });
    });

    document.querySelectorAll('.select-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            const name = button.dataset.name;

            // Check if already added
            if (document.querySelector(`#selected-${id}`)) {
                alert('Constituent already selected.');
                return;
            }

            // Add to selected list
            const container = document.getElementById('selected-constituents');
            const div = document.createElement('div');
            div.id = `selected-${id}`;
            div.className = 'list-group-item d-flex justify-content-between align-items-start';
            div.innerHTML = `
                <div>
                    <p class="mb-1 font-weight-bold">${name}</p>
                    <input type="hidden" name="constituents[${id}][id]" value="${id}">
                    <label class="form-label">Role:
                        <select name="constituents[${id}][role]" class="form-select role-select" required>
                            <option value="SECRETARY">Secretary</option>
                            <option value="TREASURER">Treasurer</option>
                            <option value="KONSEHAL" selected>Konsehal</option>
                        </select>
                    </label>
                    <label class="form-label">Start Term:
                        <input type="date" name="constituents[${id}][start_term]" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </label>
                </div>
                <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('selected-${id}').remove()">Remove</button>
            `;
            container.appendChild(div);

            // Add role validation
            validateRoles();
        });
    });

    function validateRoles() {
        const roleCounts = { 1: 0, 2: 0, 3: 0 }; // Chairperson, Secretary, Treasurer
        document.querySelectorAll('.role-select').forEach(select => {
            const role = select.value;
            if (role in roleCounts) {
                roleCounts[role]++;
            }
        });

        document.querySelectorAll('.role-select').forEach(select => {
            const role = select.value;
            if (role in roleCounts && roleCounts[role] > 1) {
                select.querySelector(`option[value="${role}"]`).disabled = true;
            } else {
                select.querySelectorAll('option').forEach(option => {
                    if (option.value in roleCounts) {
                        option.disabled = roleCounts[option.value] >= 1 && option.value !== role;
                    }
                });
            }
        });
    }

    document.addEventListener('change', event => {
        if (event.target.classList.contains('role-select')) {
            validateRoles();
        }
    });
</script>

<?php
$content = ob_get_clean();
require_once('app/Views/layouts/main.php');
?>
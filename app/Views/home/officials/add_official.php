<?php
// Disable Bootstrap Select on this page
echo '<script>var disableBootstrapSelect = true;</script>';

$content = ob_start();
$todayDate = date('Y-m-d');
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
            <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">
            <div id="selected-constituents" class="list-group mb-3" style="max-height: 400px; overflow-y: auto;"></div>
            <button type="submit" class="btn btn-primary w-100">Appoint Officials</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const TODAY_DATE = '<?= $todayDate ?>';
    const HAS_PUNONG_BARANGAY = <?= json_encode($hasPunongBarangay ?? false) ?>;
    
    // Search function
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#constituents-table tr');
            rows.forEach(row => {
                const td = row.querySelector('td');
                if (td) {
                    const fullName = td.textContent.toLowerCase();
                    row.style.display = fullName.includes(searchValue) ? '' : 'none';
                }
            });
        });
    }

    document.querySelectorAll('.select-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            const name = button.dataset.name;

            if (document.querySelector(`#selected-${id}`)) {
                alert('Constituent already selected.');
                return;
            }

            const container = document.getElementById('selected-constituents');
            const div = document.createElement('div');
            div.id = `selected-${id}`;
            div.className = 'list-group-item d-flex justify-content-between align-items-start';
            
            const innerDiv = document.createElement('div');
            innerDiv.style.flex = '1';
            
            const nameP = document.createElement('p');
            nameP.className = 'mb-1 font-weight-bold';
            nameP.textContent = name;
            innerDiv.appendChild(nameP);
            
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `constituents[${id}][id]`;
            hiddenInput.value = id;
            innerDiv.appendChild(hiddenInput);
            
            const roleLabel = document.createElement('label');
            roleLabel.className = 'form-label d-block';
            roleLabel.textContent = 'Role: ';
            
            const roleSelect = document.createElement('select');
            roleSelect.name = `constituents[${id}][role]`;
            roleSelect.className = 'form-select role-select';
            roleSelect.required = true;
            roleSelect.setAttribute('data-no-selectpicker', 'true');
            
            // Only add Punong Barangay option if it doesn't exist yet
            if (!HAS_PUNONG_BARANGAY) {
                const punong_barangayOption = document.createElement('option');
                punong_barangayOption.value = 'PUNONG BARANGAY';
                punong_barangayOption.textContent = 'Punong Barangay';
                roleSelect.appendChild(punong_barangayOption);
            }
            
            const secretaryOption = document.createElement('option');
            secretaryOption.value = 'SECRETARY';
            secretaryOption.textContent = 'Secretary';
            roleSelect.appendChild(secretaryOption);
            
            const treasurerOption = document.createElement('option');
            treasurerOption.value = 'TREASURER';
            treasurerOption.textContent = 'Treasurer';
            roleSelect.appendChild(treasurerOption);
            
            const konsehalOption = document.createElement('option');
            konsehalOption.value = 'KONSEHAL';
            konsehalOption.textContent = 'Konsehal';
            konsehalOption.selected = true;
            roleSelect.appendChild(konsehalOption);
            
            roleLabel.appendChild(roleSelect);
            innerDiv.appendChild(roleLabel);
            
            const termLabel = document.createElement('label');
            termLabel.className = 'form-label d-block mt-2';
            termLabel.textContent = 'Start Term: ';
            
            const termInput = document.createElement('input');
            termInput.type = 'date';
            termInput.name = `constituents[${id}][start_term]`;
            termInput.className = 'form-control';
            termInput.required = true;
            termInput.value = TODAY_DATE;
            
            termLabel.appendChild(termInput);
            innerDiv.appendChild(termLabel);
            
            div.appendChild(innerDiv);
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-danger btn-sm';
            removeBtn.textContent = 'Remove';
            removeBtn.onclick = function() {
                const elementToRemove = document.getElementById(`selected-${id}`);
                if (elementToRemove) {
                    elementToRemove.remove();
                    validateRoles();
                }
            };
            div.appendChild(removeBtn);
            
            container.appendChild(div);
            validateRoles();
        });
    });

    function validateRoles() {
        const roleCounts = { 'PUNONG BARANGAY': 0, SECRETARY: 0, TREASURER: 0 };
        
        document.querySelectorAll('.role-select').forEach(select => {
            const role = select.value;
            if (role in roleCounts) {
                roleCounts[role]++;
            }
        });

        document.querySelectorAll('.role-select').forEach(select => {
            const currentRole = select.value;
            
            select.querySelectorAll('option').forEach(option => {
                const optionValue = option.value;
                
                if (optionValue in roleCounts) {
                    option.disabled = (roleCounts[optionValue] >= 1 && optionValue !== currentRole);
                } else {
                    option.disabled = false;
                }
            });
        });
    }

    document.addEventListener('change', event => {
        if (event.target.classList.contains('role-select')) {
            validateRoles();
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require_once('app/Views/layouts/main.php');
?>
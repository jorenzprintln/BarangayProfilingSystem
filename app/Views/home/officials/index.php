<?php
$content = ob_start();
?>
<style>
    .modal-50w {
        max-width: 50% !important;
        width: 50% !important;
    }
</style>
<div>
    <h1><?= $title ?></h1>
    <div class="text-right mb-3">
        <button class="btn btn-primary" data-toggle="modal" data-target="#addOfficialModal">Add Barangay Official</button>
    </div>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Position</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($officials)): ?>
                <?php foreach ($officials as $official): ?>
                    <tr>
                        <td><?= htmlspecialchars($official['id']) ?></td>
                        <td><?= htmlspecialchars($official['full_name']) ?></td>
                        <td>
                            <?= htmlspecialchars($official['role']) ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                data-id="<?= $official['id'] ?>"
                                data-name="<?= htmlspecialchars($official['full_name']) ?>">
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No officials found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <span id="officialName"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Official Modal -->
    <div class="modal fade" id="addOfficialModal" tabindex="-1" role="dialog" aria-labelledby="addOfficialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-50w" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOfficialModalLabel">Add Barangay Official</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
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
                                                <td><?= htmlspecialchars($constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name']) ?></td>
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
                            <form id="appointForm" method="POST" action="index.php?controller=officials&action=create" class="p-3 border">
                                <div id="selected-constituents" class="list-group mb-3" style="max-height: 400px; overflow-y: auto;"></div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="appointBtn">Appoint Officials</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete official functionality
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        const officialNameSpan = document.getElementById('officialName');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                
                officialNameSpan.textContent = name;
                confirmDeleteBtn.href = `index.php?controller=officials&action=delete&id=${id}`;
                
                $('#deleteModal').modal('show');
            });
        });

        // Add official functionality
        // Search function
        document.getElementById('search-input').addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#constituents-table tr');
            rows.forEach(row => {
                const fullName = row.querySelector('td')?.textContent.toLowerCase();
                if (fullName) {
                    row.style.display = fullName.includes(searchValue) ? '' : 'none';
                }
            });
        });

        // Select constituent
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
            const roleCounts = { SECRETARY: 0, TREASURER: 0, KONSEHAL: 0 };
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

        // Submit form when the Appoint button is clicked
        document.getElementById('appointBtn').addEventListener('click', function() {
            document.getElementById('appointForm').submit();
        });
    });
</script>

<?php
$content = ob_get_clean();
require_once('app/Views/layouts/main.php');
?>
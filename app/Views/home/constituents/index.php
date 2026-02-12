<?php
$content = ob_start();
?>

<style>
    th.sortable {
        cursor: pointer;
    }

    th.sortable:after {
        content: '\25B4';
        font-size: 0.8em;
        margin-left: 5px;
        visibility: hidden;
    }

    th.sortable[data-sort-order="asc"]:after {
        visibility: visible;
        content: '\25B4';
    }

    th.sortable[data-sort-order="desc"]:after {
        visibility: visible;
        content: '\25BE';
    }
</style>

<div class="container-fluid px-4 mt-3">
    <?php if (Session::hasFlash('success')): ?>
        <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo Session::getFlash('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <script>
            setTimeout(function () {
                const successAlert = document.getElementById('success-alert');
                if (successAlert) {
                    const bsAlert = new bootstrap.Alert(successAlert);
                    bsAlert.close();
                }
            }, 5000);
        </script>
    <?php endif; ?>
    <h3 class="font-weight-bold">Constituents</h3>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
        <div class="d-flex align-items-center mb-2 mb-md-0">
            <a href="index.php?controller=constituents&action=removedConstituents" class="btn btn-outline-dark">
                Archived Constituents
            </a>
        </div>
        <div class="d-flex flex-column flex-md-row align-items-md-center w-75 w-md-auto">
            <div class="d-flex align-items-center mb-2 mb-md-0 mr-md-2 w-100 w-md-auto ml-2">
                <strong class="mr-2">Showing:</strong>
                <select id="age-filter" class="form-control text-dark bg-light rounded">
                    <option value="all">All</option>
                    <option value="children">Children (0-14)</option>
                    <option value="youth">Youth (15-24)</option>
                    <option value="adults">Adults (25-59)</option>
                    <option value="seniors">Seniors (60+)</option>
                </select>
            </div>
            <div class="d-flex w-100 w-md-auto">
                <input type="text" id="search-input" class="form-control rounded mr-2" placeholder="Search...">
                <a href="index.php?controller=constituents&action=addConstituent" class="btn btn-primary">
                    Add Constituent
                </a>
            </div>
        </div>
    </div>

    <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
        <table class="table table-bordered border rounded" data-sort-order="asc">
            <thead class="thead-dark">
                <tr>
                    <th class="sortable" onclick="sortTable(0, this)">Name</th>
                    <th class="sortable" onclick="sortTable(1, this)">Sex</th>
                    <th class="sortable" onclick="sortTable(2, this)">Age</th>
                    <th class="sortable" onclick="sortTable(3, this)">Registered Voter</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($constituents)): ?>
                    <?php foreach ($constituents as $constituent): ?>
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
                                <div class="d-flex align-items-center justify-content-start">
                                    <a href="index.php?controller=constituents&action=view&id=<?php echo $constituent['id']; ?>"
                                        class="btn btn-sm btn-primary mr-2">
                                        View
                                    </a>
                                    <button type="button" class="btn btn-sm btn-warning mr-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editModal" 
                                        data-constituent-id="<?php echo $constituent['id']; ?>">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#archiveModal" 
                                        data-constituent-id="<?php echo $constituent['id']; ?>">
                                        Archive
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No constituents found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Confirmation Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Confirm Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to edit this constituent's information?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="editConfirmBtn" class="btn btn-warning">Edit</a>
            </div>
        </div>
    </div>
</div>

<!-- Archive Confirmation Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="archiveModalLabel">Confirm Archive</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to archive this constituent?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="archiveConfirmBtn" class="btn btn-danger">Archive</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Sorting functionality
    function sortTable(columnIndex, element) {
        const table = document.querySelector('table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        // Get current sort order or set default to 'asc'
        let sortOrder = element.getAttribute('data-sort-order') || 'asc';

        // Toggle sort order
        sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';

        // Reset all columns
        document.querySelectorAll('th.sortable').forEach(th => {
            th.removeAttribute('data-sort-order');
        });

        // Set the current sort column
        element.setAttribute('data-sort-order', sortOrder);

        // Sort the rows
        rows.sort((rowA, rowB) => {
            const cellA = rowA.querySelectorAll('td')[columnIndex].textContent.trim();
            const cellB = rowB.querySelectorAll('td')[columnIndex].textContent.trim();

            // Special case for age column (numeric sorting)
            if (columnIndex === 2) {
                const numA = parseInt(cellA) || 0;
                const numB = parseInt(cellB) || 0;
                return sortOrder === 'asc' ? numA - numB : numB - numA;
            } else {
                return sortOrder === 'asc'
                    ? cellA.localeCompare(cellB, undefined, { sensitivity: 'base' })
                    : cellB.localeCompare(cellA, undefined, { sensitivity: 'base' });
            }
        });

        // Remove existing rows
        rows.forEach(row => tbody.removeChild(row));

        // Append sorted rows
        rows.forEach(row => tbody.appendChild(row));
    }

    // Search functionality
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search-input');
        const ageFilter = document.getElementById('age-filter');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const ageRange = ageFilter.value;

            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                const ageCell = row.querySelectorAll('td')[2].textContent.trim();
                const age = parseInt(ageCell) || 0;

                let showByAge = true;

                // Apply age filters
                if (ageRange !== 'all') {
                    if (ageRange === 'children' && (age > 14 || age < 0)) showByAge = false;
                    if (ageRange === 'youth' && (age < 15 || age > 24)) showByAge = false;
                    if (ageRange === 'adults' && (age < 25 || age > 59)) showByAge = false;
                    if (ageRange === 'seniors' && age < 60) showByAge = false;
                }

                // Apply search term filter
                const showBySearch = searchTerm === '' || rowText.includes(searchTerm);

                // Show/hide the row based on both filters
                row.style.display = (showByAge && showBySearch) ? '' : 'none';
            });
        }

        // Add event listeners
        searchInput.addEventListener('input', filterTable);
        ageFilter.addEventListener('change', filterTable);

        // Initialize filters
        filterTable();

        // Modal functionality for edit and archive
        // Initialize Bootstrap modals manually
        const editModalEl = document.getElementById('editModal');
        const archiveModalEl = document.getElementById('archiveModal');
        const editModal = new bootstrap.Modal(editModalEl);
        const archiveModal = new bootstrap.Modal(archiveModalEl);
        const editConfirmBtn = document.getElementById('editConfirmBtn');
        const archiveConfirmBtn = document.getElementById('archiveConfirmBtn');
        
        // Add click handlers directly to buttons
        document.querySelectorAll('[data-bs-target="#editModal"]').forEach(button => {
            button.addEventListener('click', function() {
                const constituentId = this.getAttribute('data-constituent-id');
                editConfirmBtn.href = 'index.php?controller=constituents&action=edit&id=' + constituentId;
                editModal.show();
            });
        });
        
        document.querySelectorAll('[data-bs-target="#archiveModal"]').forEach(button => {
            button.addEventListener('click', function() {
                const constituentId = this.getAttribute('data-constituent-id');
                archiveConfirmBtn.href = 'index.php?controller=constituents&action=removeConstituent&id=' + constituentId;
                archiveModal.show();
            });
        });
        
        // Add event listeners to the Cancel buttons
        editModalEl.querySelector('.btn-secondary').addEventListener('click', function() {
            editModal.hide();
        });
        
        archiveModalEl.querySelector('.btn-secondary').addEventListener('click', function() {
            archiveModal.hide();
        });
        
        // Add event listeners to close buttons too
        editModalEl.querySelector('.btn-close').addEventListener('click', function() {
            editModal.hide();
        });
        
        archiveModalEl.querySelector('.btn-close').addEventListener('click', function() {
            archiveModal.hide();
        });
    });
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
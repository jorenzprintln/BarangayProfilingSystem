<?php
// views/home/index.php - Home page
$content = ob_start();
?>

<div class="container mt-3">
    <?php if (Session::hasFlash('success')): ?>
        <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo Session::getFlash('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <script>
            // Auto-dismiss the success message after 5 seconds
            setTimeout(function() {
                const successAlert = document.getElementById('success-alert');
                if (successAlert) {
                    const bsAlert = new bootstrap.Alert(successAlert);
                    bsAlert.close();
                }
            }, 5000);
        </script>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Constituents</h2>
        <span class="text-muted">
            Showing <?php echo count($constituents); ?> of <?php echo $totalConstituents; ?> constituents
        </span>
    </div>
    <div class="d-flex justify-content-between">
        <!-- Search box -->
        <div>
            <form method="GET" class="form-inline mb-3">
                <input type="hidden" name="controller" value="home">
                <input type="hidden" name="action" value="index">
                <input type="text" name="search" class="form-control mr-2" placeholder="Search" value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
                <button type="submit" class="btn btn-primary mr-2">
                    <img src="public/assets/icons/search.icon.png" alt="Search" width="16" height="16">
                </button>
                <button type="button" class="btn btn-secondary" onclick="clearSearch()">Clear</button>
            </form>
        </div>
        <div>
            <a href="index.php?controller=constituents&action=index" class="btn btn-primary">Add Constituents</a>
        </div>
    </div>

    <div style="height: 480px; overflow-y: auto;" id="constituents-table">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark"">
                <tr>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Suffix</th>
                    <th>Sex</th>
                    <th>Birthdate</th>
                    <th>Registered Voter</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($constituents) > 0): ?>
                    <?php foreach ($constituents as $constituent): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($constituent['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($constituent['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($constituent['middle_name']); ?></td>
                            <td><?php echo htmlspecialchars($constituent['suffix'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($constituent['sex']); ?></td>
                            <td><?php echo htmlspecialchars($constituent['birthdate']); ?></td>
                            <td><?php echo htmlspecialchars($constituent['registered_voter']); ?></td>
                            <td>
                                <a href="index.php?controller=constituents&action=view&id=<?php echo $constituent['id']; ?>" class="btn btn-sm btn-info">View</a>
                                <a href="index.php?controller=constituents&action=update&id=<?php echo $constituent['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="index.php?controller=constituents&action=remove&id=<?php echo $constituent['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this constituent?');">Remove</a>
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

    <div class="d-flex w-100 justify-content-center mt-3">
        <div class="d-flex justify-content-between align-items-center mb-3 mr-2">
            <div class="d-flex align-items-center">
                <label for="recordsPerPage" class="mr-2 w-100">Records:</label>

                <select id="recordsPerPage" class="form-control" onchange="changeRecordsPerPage()">
                    <option value="10" <?php echo $perPage == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="50" <?php echo $perPage == 50 ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?php echo $perPage == 100 ? 'selected' : ''; ?>>100</option>
                    <option value="500" <?php echo $perPage == '500' ? 'selected' : ''; ?>>500</option>
                </select>
            </div>
        </div>
        <!-- Pagination Controls -->
        <?php if ($totalPages > 1 && $perPage != 'all'): ?>
            <div class="d-flex align-items-center">
                <nav aria-label="Constituents pagination">
                    <ul class="pagination justify-content-center">
                        <!-- First Page Button -->
                        <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?controller=home&action=index&page=1&perPage=<?php echo $perPage; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" aria-label="First">
                                <span aria-hidden="true">&laquo;&laquo;</span>
                            </a>
                        </li>

                        <!-- Previous Button -->
                        <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?controller=home&action=index&page=<?php echo $currentPage - 1; ?>&perPage=<?php echo $perPage; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);

                        // Always show at least 5 pages if available
                        if ($endPage - $startPage + 1 < 5) {
                            if ($startPage == 1) {
                                $endPage = min($totalPages, $startPage + 4);
                            } elseif ($endPage == $totalPages) {
                                $startPage = max(1, $endPage - 4);
                            }
                        }

                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                <a class="page-link" href="index.php?controller=home&action=index&page=<?php echo $i; ?>&perPage=<?php echo $perPage; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next Button -->
                        <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?controller=home&action=index&page=<?php echo $currentPage + 1; ?>&perPage=<?php echo $perPage; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>

                        <!-- Last Page Button -->
                        <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?controller=home&action=index&page=<?php echo $totalPages; ?>&perPage=<?php echo $perPage; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" aria-label="Last">
                                <span aria-hidden="true">&raquo;&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Page Information -->
                <div class="text-center text-muted ml-2 mb-4">
                    Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function changeRecordsPerPage() {
        const recordsPerPage = document.getElementById('recordsPerPage').value;
        // Append search parameter if present
        const search = "<?php echo $search; ?>";
        const searchParam = search ? '&search=' + encodeURIComponent(search) : '';
        window.location.href = `index.php?controller=home&action=index&perPage=${recordsPerPage}${searchParam}`;
    }

    function clearSearch() {
        window.location.href = 'index.php?controller=home&action=index';
    }

    // Lazy load function for "All" option
    if (<?php echo json_encode($perPage); ?> === 'all') {
        let page = 1;
        const tableBody = document.querySelector('#constituents-table tbody');
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                page++;
                fetch(`index.php?controller=home&action=loadMore&page=${page}`)
                    .then(response => response.text())
                    .then(data => {
                        tableBody.insertAdjacentHTML('beforeend', data);
                    });
            }
        }, {
            threshold: 1.0
        });

        observer.observe(document.querySelector('#constituents-table tbody tr:last-child'));
    }
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
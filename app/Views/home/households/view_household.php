<?php
$content = ob_start();
?>
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between mb-3">
        <a href="index.php?controller=households&action=index" class="btn btn-secondary">Back to List</a>
        <a href="index.php?controller=households&action=generate_rbi_A&household_id=<?= $household['id'] ?>"
            class="btn btn-primary" target="_blank">Generate RBI Form</a>
    </div>
    
    <!-- Household Information Card -->
    <div class="shadow-sm border mb-4">
        <div class="card-header bg-dark text-white">
            <h4 class="font-weight-bold m-0">Household Information</h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong class="mr-2">Household Number:</strong>
                    <span><?php echo htmlspecialchars($household['household_number'] ?? ''); ?></span>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <strong class="mr-2">Region:</strong>
                    <span><?php echo htmlspecialchars($household['region'] ?? ''); ?></span>
                </div>
                <div class="col-md-3">
                    <strong class="mr-2">Province:</strong>
                    <span><?php echo htmlspecialchars($household['province'] ?? ''); ?></span>
                </div>
                <div class="col-md-3">
                    <strong class="mr-2">City/Municipality:</strong>
                    <span><?php echo htmlspecialchars($household['city_municipality'] ?? ''); ?></span>
                </div>
                <div class="col-md-3">
                    <strong class="mr-2">Barangay:</strong>
                    <span><?php echo htmlspecialchars($household['barangay_name'] ?? ''); ?></span>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-3">
                    <strong class="mr-2">Street:</strong>
                    <span><?php echo empty($household['street_name']) ? 'NOT SPECIFIED' : htmlspecialchars($household['street_name']); ?></span>
                </div>
                <div class="col-md-3">
                    <strong class="mr-2">Zip Code:</strong>
                    <span><?php echo empty($household['zip_code']) ? 'NOT SPECIFIED' : htmlspecialchars($household['zip_code']); ?></span>
                </div>
                <div class="col-md-3">
                    <strong class="mr-2">Purok:</strong>
                    <span><?php echo empty($household['purok']) ? 'NOT SPECIFIED' : htmlspecialchars($household['purok']); ?></span>
                </div>
                <div class="col-md-3">
                    <strong class="mr-2">Block:</strong>
                    <span><?php echo empty($household['block_number']) ? 'NOT SPECIFIED' : htmlspecialchars($household['block_number']); ?></span>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-3">
                    <strong class="mr-2">Lot:</strong>
                    <span><?php echo empty($household['lot_number']) ? 'NOT SPECIFIED' : htmlspecialchars($household['lot_number']); ?></span>
                </div>
                <div class="col-md-3">
                    <strong class="mr-2">House:</strong>
                    <span><?php echo empty($household['house_number']) ? 'NOT SPECIFIED' : htmlspecialchars($household['house_number']); ?></span>
                </div>
                <div class="col-md-3">
                    <strong class="mr-2">Unit:</strong>
                    <span><?php echo empty($household['unit']) ? 'NOT SPECIFIED' : htmlspecialchars($household['unit']); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Household Members Card -->
    <div class="shadow-sm border mb-4">
        <div class="card-header bg-dark text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="font-weight-bold m-0">Household Members</h4>
                <div class="d-flex">
                    <a href="index.php?controller=households&action=addConstituents&household_id=<?= $household['id'] ?>"
                        class="btn btn-primary btn-sm mr-2"><span><i class="fas fa-plus"></i></span> Add Members</a>
                    <input type="text" id="memberSearch" class="form-control form-control-sm" placeholder="Search members...">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Full Name</th>
                            <th>Age</th>
                            <th>Sex</th>
                            <th>Civil Status</th>
                            <th>Contact</th>
                            <th>Is Head</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($members)): ?>
                            <?php foreach ($members as $index => $member): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($member['full_name']) ?></td>
                                    <td><?= htmlspecialchars($member['age']) ?></td>
                                    <td><?= htmlspecialchars($member['sex']) ?></td>
                                    <td><?= htmlspecialchars($member['civil_status']) ?></td>
                                    <td><?= htmlspecialchars($member['contact'] ?? 'N/A') ?></td>
                                    <td><?= $member['is_head'] ?></td>
                                    <td>
                                        <a href="index.php?controller=constituents&action=view&id=<?php echo $member['constituent_id']; ?>"
                                            class="btn btn-sm btn-primary mr-2">View Details</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No members found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Families in Household Card -->
    <div class="shadow-sm border">
        <div class="card-header bg-dark text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="font-weight-bold m-0">Families in Household</h4>
                <div class="d-flex">
                    <a href="index.php?controller=family&action=createHouseholdFamily&household_id=<?= $household['id'] ?>"
                        class="btn btn-primary btn-sm mr-2"><span><i class="fas fa-plus"></i></span> New Family</a>
                    <input type="text" id="familySearch" class="form-control form-control-sm" placeholder="Search family...">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Family Name</th>
                            <th>Head of Family</th>
                            <th>Members</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($families)): ?>
                            <?php foreach ($families as $index => $family): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($family['family_name']) ?></td>
                                    <td><?= htmlspecialchars($family['head_full_name']) ?></td>
                                    <td>
                                        <ul class="list-unstyled mb-0">
                                            <?php foreach ($family['members'] as $member): ?>
                                                <li><?= htmlspecialchars($member['full_name']) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No families found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('memberSearch');
        const tableRows = document.querySelectorAll('table tbody tr');

        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase().trim();

            tableRows.forEach(row => {
                const fullName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const age = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const sex = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                const civilStatus = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                const contact = row.querySelector('td:nth-child(6)').textContent.toLowerCase();

                const matches = fullName.includes(searchTerm) ||
                    age.includes(searchTerm) ||
                    sex.includes(searchTerm) ||
                    civilStatus.includes(searchTerm) ||
                    contact.includes(searchTerm);

                row.style.display = matches ? '' : 'none';
            });
        });
    });
</script>
<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
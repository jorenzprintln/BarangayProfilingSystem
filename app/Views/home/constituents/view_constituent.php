<?php
$content = ob_start();
?>
<div class="container-fluid px-4 mt-3">
    <div class="d-flex justify-content-between mb-2">
        <a href="index.php?controller=constituents&action=index" class="btn btn-secondary">Back</a>
        <a href="index.php?controller=forms&action=rbi_form_B&id=<?php echo $constituent['id']; ?>"
            class="btn btn-primary" target="_blank">Generate RBI Form B</a>
    </div>
    <div class="d-flex justify-content-center align-items-center mb-2 bg-dark rounded p-2 mb-3">
        <h4 class="text-light font-weight-bold m-0">Personal Information</h4>
    </div>
    <div class="d-flex flex-column px-2">
        <div class="d-flex flex-row align-items-center mb-2">
            <div class="w-25">
                <strong class="mr-2">Full Name</strong>
            </div>
            <span><?php echo htmlspecialchars(': ' . $constituent['first_name'] ?? '') . ' ' . htmlspecialchars(' ' . $constituent['middle_name'] ?? '') . ' ' . htmlspecialchars(' ' . $constituent['last_name'] ?? ''); ?></span>
        </div>

        <div class="d-flex flex-row align-items-center mb-2">
            <div class="w-25">
                <strong class="mr-2">Sex</strong>
            </div>
            <div class="w-25">
                <span class="mr-5"><?php echo htmlspecialchars(': ' . $constituent['sex'] ?? ''); ?></span>
            </div>
            <strong class="mr-2">Birthdate</strong>
            <span><?php echo htmlspecialchars(': ' . $constituent['birthdate'] ?? ''); ?></span>
        </div>

        <div class="d-flex flex-row align-items-center mb-2">
            <div class="w-25">
                <strong class="mr-2">Birthplace</strong>
            </div>
            <div class="w-25">
                <span class="mr-5"><?php echo htmlspecialchars(': ' . $constituent['birthplace'] ?? ''); ?></span>
            </div>
            <strong class="mr-2">Registered Voter? : </strong>
            <span
                class="<?php echo $constituent['registered_voter'] === 'YES' ? 'bg-success' : 'bg-danger'; ?> text-white px-2 badge rounded-pill"><?php echo htmlspecialchars($constituent['registered_voter'] ?? ''); ?></span>
        </div>

        <div class="d-flex flex-row align-items-center mb-2">
            <div class="w-25">
                <strong class="mr-2">Civil Status</strong>
            </div>
            <div class="w-25">
                <span class="mr-5"><?php echo htmlspecialchars(': ' . $constituent['civil_status'] ?? ''); ?></span>
            </div>
        </div>

        <div class="d-flex flex-row align-items-center mb-2">
            <div class="w-25">
                <strong class="mr-2">Religion</strong>
            </div>
            <div class="w-25">
                <span class="mr-5"><?php echo htmlspecialchars(': ' . $constituent['religion'] ?? ''); ?></span>
            </div>
            <strong class="mr-2">Citizenship</strong>
            <span><?php echo htmlspecialchars(': ' . $constituent['citizenship'] ?? ''); ?></span>
        </div>


        <div class="d-flex flex-row align-items-center mb-2">
            <div class="w-25">
                <strong class="mr-2">Occupation</strong>
            </div>
            <div class="w-25">
                <span class="mr-5"><?php echo htmlspecialchars(': ' . $constituent['occupation'] ?? 'NA'); ?></span>
            </div>
        </div>

        <div class="d-flex flex-row align-items-center mb-2">
            <div class="w-25">
                <strong class="mr-2">Contact Number</strong>
            </div>
            <div class="w-25">
                <span class="mr-5"><?php echo htmlspecialchars(': ' . $constituent['contact'] ?? ''); ?></span>
            </div>
        </div>

        <div class="d-flex flex-row align-items-center mb-2">
            <div class="w-25">
                <strong class="mr-2">Email Address</strong>
            </div>
            <div class="w-50">
                <span
                    class="mr-5"><?php echo htmlspecialchars(': ' . strtolower($constituent['email'] ?? '')); ?></span>
            </div>
        </div>

        <div class="d-flex flex-row align-items-center mb-2">
            <div class="w-25">
                <strong class="mr-2">Highest Educational Attainment</strong>
            </div>
            <div class="w-25">
                <span class="mr-5"><?php
                $education = '';
                $education = match ($constituent['education_attainment'] ?? '') {
                    '1' => 'DAYCARE',
                    '2' => 'NURSERY SCHOOL',
                    '3' => 'KINDERGARTEN',
                    '4' => 'ELEMENTARY',
                    '5' => 'ALS HIGH SCHOOL',
                    '6' => 'HIGH SCHOOL',
                    '7' => 'JUNIOR HIGH',
                    '8' => 'SENIOR HIGH',
                    '9' => 'VOCATIONAL',
                    '10' => 'COLLEGE',
                    '11' => 'POST-GRAD',
                    default => 'NA',
                };
                echo htmlspecialchars(': ' . $education);
                ?></span>
            </div>
            <strong class="mr-2">Is Graduate? : </strong>
            <span
                class="<?php echo $constituent['is_graduate'] === 'YES' ? 'bg-success' : 'bg-danger'; ?> text-white px-2 badge rounded-pill"><?php echo htmlspecialchars($constituent['is_graduate'] ?? ''); ?></span>
        </div>

        <div class="d-flex flex-row align-items-center mb-2">
            <div class="w-25">
                <strong class="mr-2">Classifications</strong>
            </div>
            <div class="d-flex flex-wrap w-50">
                <?php if (!empty($classifications)): ?>
                    <div class="d-flex flex-wrap px-2">
                        <?php foreach ($classifications as $classification): ?>
                            <div class="mb-2 mr-1">
                                <span
                                    class="<?php echo $classification['code'] === 'PWD' ? 'bg-info' : 'bg-info'; ?> text-white px-2 badge rounded-pill d-flex"><?php echo htmlspecialchars($classification['code'] ?? ''); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>



    <div class="d-flex justify-content-center align-items-center mb-2 bg-dark rounded p-2 mb-3">
        <h4 class="text-light font-weight-bold m-0">Household Information</h4>
    </div>

    <div class="d-flex flex-column px-2 mb-5">
        <div class="d-flex flex-row align-items-center mb-2">
            <div>
                <strong class="mr-2">Household Number :</strong>
            </div>
            <span><?php echo htmlspecialchars($constituentHousehold['household_number'] ?? 'Not Assigned'); ?></span>
        </div>
        
        
        <div class="d-flex flex-column mb-2">
            <strong class="mr-2">Address :</strong>
            <div class="d-flex flex-column px-4">
                <div class="d-flex flex-row align-items-center justify-content-between mb-2">
                    <div>
                        <strong class="mr-2">Region :</strong>
                        <span><?php echo htmlspecialchars($constituentHousehold['region'] ?? 'Not Assigned'); ?></span>
                    </div>
                    <div>
                        <strong class="mr-2">Province :</strong>
                        <span><?php echo htmlspecialchars($constituentHousehold['province'] ?? 'Not Assigned'); ?></span>
                    </div>
                    <div>
                        <strong class="mr-2">City/Municipality :</strong>
                        <span><?php echo htmlspecialchars($constituentHousehold['city_municipality'] ?? 'Not Assigned'); ?></span>
                    </div>
                    <div>
                        <strong class="mr-2">Barangay :</strong>
                        <span><?php echo htmlspecialchars($constituentHousehold['barangay_name'] ?? 'Not Assigned'); ?></span>
                    </div>
                </div>
                
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                    <div>
                        <strong class="mr-2">Street :</strong>
                        <span><?php echo empty($constituentHousehold['street_name']) ? 'NOT SPECIFIED' : htmlspecialchars($constituentHousehold['street_name']); ?></span>
                    </div>
                    <div>
                        <strong class="mr-2">Zip Code :</strong>
                        <span><?php echo empty($constituentHousehold['zip_code']) ? 'NOT SPECIFIED' : htmlspecialchars($constituentHousehold['zip_code']); ?></span>
                    </div>
                    <div>
                        <strong class="mr-2">Purok :</strong>
                        <span><?php echo empty($constituentHousehold['purok']) ? 'NOT SPECIFIED' : htmlspecialchars($constituentHousehold['purok']); ?></span>
                    </div>
                    <div>
                        <strong class="mr-2">Block :</strong>
                        <span><?php echo empty($constituentHousehold['block_number']) ? 'NOT SPECIFIED' : htmlspecialchars($constituentHousehold['block_number']); ?></span>
                    </div>
                </div>
                
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                    <div>
                        <strong class="mr-2">Lot :</strong>
                        <span><?php echo empty($constituentHousehold['lot_number']) ? 'NOT SPECIFIED' : htmlspecialchars($constituentHousehold['lot_number']); ?></span>
                    </div>
                    <div>
                        <strong class="mr-2">House :</strong>
                        <span><?php echo empty($constituentHousehold['house_number']) ? 'NOT SPECIFIED' : htmlspecialchars($constituentHousehold['house_number']); ?></span>
                    </div>
                    <div>
                        <strong class="mr-2">Unit :</strong>
                        <span><?php echo empty($constituentHousehold['unit']) ? 'NOT SPECIFIED' : htmlspecialchars($constituentHousehold['unit']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
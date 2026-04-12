<?php
$content = ob_start();
$standardCitizenships   = ['FILIPINO', 'OTHERS'];

$useData = !empty($errors);

$citizenshipVal     = $useData ? ($data['citizenship'] ?? '') : ($constituent['citizenship'] ?? '');
$isOthers           = !in_array($citizenshipVal, $standardCitizenships);
$citizenshipOthersValue = $isOthers ? $citizenshipVal : '';


$educationLevels = [
    '1'  => 'Daycare',
    '2'  => 'Nursery',
    '3'  => 'Kindergarten',
    '4'  => 'Elementary',
    '5'  => 'ALS',
    '6'  => 'High School',
    '7'  => 'Junior High School',
    '8'  => 'Senior High School',
    '9'  => 'Vocational',
    '10' => 'College',
    '11' => 'Post Graduate',
];
?>

<link rel="stylesheet" href="public/assets/css/constituent_create.css">
<link rel="stylesheet" href="public/assets/css/constituent_edit.css">

<div class="container-fluid px-4 mt-3">
    <div class="content-wrapper">

        <!-- Back Button -->
        <div style="display:flex; margin-bottom:1.5rem;">
            <a href="index.php?controller=constituents&action=index" class="btn-back">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
                </svg>
                Back to List
            </a>
        </div>

        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex align-items-center mt-3">
                <svg width="36" height="36" fill="white" viewBox="0 0 20 20" class="mr-3">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                </svg>
                <div>
                    <h2 class="font-weight-bold mb-0">Edit Constituent</h2>
                    <p class="mb-0 mt-1" style="opacity:0.9;font-size:0.95rem;">Update constituent information below</p>
                </div>
            </div>
        </div>

        <!-- Server-side error → JS modal trigger -->
        <?php if (isset($errors['duplicate_org_id'])): ?>
        <script>
            var SERVER_ERROR = {
                type:           'duplicate_org_id',
                org_id:         <?= json_encode($data['duplicate_org_id'] ?? '') ?>,
                classification: <?= json_encode($data['duplicate_classification_name'] ?? 'this classification') ?>
            };
        </script>
        <?php elseif (isset($errors['update_constituent'])): ?>
        <script>
            var SERVER_ERROR = {
                type:    'general',
                message: <?= json_encode($errors['update_constituent']) ?>
            };
        </script>
        <?php endif; ?>

        <!-- Main Form -->
        <form action="index.php?controller=constituents&action=update&id=<?= $constituent['id'] ?>" method="post" id="constituentForm" novalidate>

            <!-- Personal Information -->
            <div class="form-card">
                <div class="section-header">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    <h5>Personal Information</h5>
                </div>

                <?php
                // Helper: prefer $data (re-render after error) over $constituent (first load)
                $f = function(string $field) use ($useData, $data, $constituent) {
                    return $useData
                        ? ($data[$field] ?? $constituent[$field] ?? '')
                        : ($constituent[$field] ?? '');
                };
                ?>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="psn">PhilSys Card No.</label>
                            <input type="text" class="form-control <?= isset($errors['psn']) ? 'is-invalid' : '' ?>"
                                id="psn" name="psn" placeholder="Enter 16-digit PhilSys Number"
                                value="<?= htmlspecialchars($f('psn')) ?>" pattern="\d{16}" maxlength="16">
                            <div class="invalid-feedback"><?= $errors['psn'] ?? 'Please enter a valid 16-digit PhilSys Number' ?></div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Registered Voter? <span class="required">*</span></label>
                            <div class="custom-radio-group" style="margin-top:0;">
                                <div class="custom-radio">
                                    <input type="radio" name="registered_voter" id="voter_yes" value="YES"
                                        <?= $f('registered_voter') === 'YES' ? 'checked' : '' ?> required>
                                    <label for="voter_yes">Yes</label>
                                </div>
                                <div class="custom-radio">
                                    <input type="radio" name="registered_voter" id="voter_no" value="NO"
                                        <?= $f('registered_voter') === 'NO' ? 'checked' : '' ?> required>
                                    <label for="voter_no">No</label>
                                </div>
                            </div>
                            <?php if (isset($errors['registered_voter'])): ?>
                                <div class="invalid-feedback d-block"><?= $errors['registered_voter'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="last_name">Last Name <span class="required">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>"
                                id="last_name" name="last_name" placeholder="Dela Cruz" required
                                value="<?= htmlspecialchars($f('last_name')) ?>" minlength="2" pattern="[A-Za-z\s\-']{2,}">
                            <div class="invalid-feedback"><?= $errors['last_name'] ?? 'Last name must be at least 2 characters (letters only)' ?></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="first_name">Given Name <span class="required">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
                                id="first_name" name="first_name" placeholder="Juan" required
                                value="<?= htmlspecialchars($f('first_name')) ?>" minlength="2" pattern="[A-Za-z\s\-']{2,}">
                            <div class="invalid-feedback"><?= $errors['first_name'] ?? 'Given name must be at least 2 characters (letters only)' ?></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" class="form-control <?= isset($errors['middle_name']) ? 'is-invalid' : '' ?>"
                                id="middle_name" name="middle_name" placeholder="Santos"
                                value="<?= htmlspecialchars($f('middle_name')) ?>" pattern="[A-Za-z\s\-']*">
                            <div class="invalid-feedback"><?= $errors['middle_name'] ?? 'Middle name must contain letters only' ?></div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="suffix">Suffix</label>
                            <select class="form-control <?= isset($errors['suffix']) ? 'is-invalid' : '' ?>" id="suffix" name="suffix">
                                <option value="" <?= empty($f('suffix')) ? 'selected' : '' ?>>None</option>
                                <?php foreach (['Jr.','Sr.','III','IV','V','VI','VII','VIII','IX','X'] as $sfx): ?>
                                    <option value="<?= $sfx ?>" <?= $f('suffix') === $sfx ? 'selected' : '' ?>><?= $sfx ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sex <span class="required">*</span></label>
                            <div class="custom-radio-group">
                                <div class="custom-radio">
                                    <input type="radio" name="sex" id="male" value="MALE"
                                        <?= $f('sex') === 'MALE' ? 'checked' : '' ?> required>
                                    <label for="male">Male</label>
                                </div>
                                <div class="custom-radio">
                                    <input type="radio" name="sex" id="female" value="FEMALE"
                                        <?= $f('sex') === 'FEMALE' ? 'checked' : '' ?> required>
                                    <label for="female">Female</label>
                                </div>
                            </div>
                            <?php if (isset($errors['sex'])): ?>
                                <div class="invalid-feedback d-block"><?= $errors['sex'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="birthdate">Birthdate <span class="required">*</span></label>
                            <input type="date" class="form-control <?= isset($errors['birthdate']) ? 'is-invalid' : '' ?>"
                                id="birthdate" name="birthdate" required
                                value="<?= htmlspecialchars($f('birthdate')) ?>" max="<?= date('Y-m-d') ?>">
                            <div class="invalid-feedback"><?= $errors['birthdate'] ?? 'Please enter a valid birthdate' ?></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="birthplace">Birth Place <span class="required">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['birthplace']) ? 'is-invalid' : '' ?>"
                                id="birthplace" name="birthplace" placeholder="Tacloban City" required
                                value="<?= htmlspecialchars($f('birthplace')) ?>">
                            <div class="invalid-feedback"><?= $errors['birthplace'] ?? 'Please enter a valid birth place' ?></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="civil_status">Civil Status <span class="required">*</span></label>
                            <select class="form-control <?= isset($errors['civil_status']) ? 'is-invalid' : '' ?>"
                                id="civil_status" name="civil_status" required>
                                <option value="">Select civil status</option>
                                <?php foreach (['SINGLE'=>'Single','MARRIED'=>'Married','WIDOWED'=>'Widowed','SEPARATED'=>'Separated','DIVORCED'=>'Divorced'] as $val => $label): ?>
                                    <option value="<?= $val ?>" <?= $f('civil_status') === $val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback"><?= $errors['civil_status'] ?? 'Please select your civil status' ?></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="religion">Religion <span class="required">*</span></label>
                            <select class="form-control <?= isset($errors['religion']) ? 'is-invalid' : '' ?>"
                                id="religion" name="religion" required>
                                <option value="">Select religion</option>
                                <?php
                                $religions = [
                                    'ROMAN CATHOLIC'        => 'Roman Catholic',
                                    'PROTESTANT'            => 'Protestant',
                                    'IGLESIA NI CRISTO'     => 'Iglesia ni Cristo',
                                    'BORN AGAIN'            => 'Born Again',
                                    'SEVENTH DAY ADVENTIST' => 'Seventh Day Adventist',
                                    'ISLAM'                 => 'Islam',
                                    'BAPTIST'               => 'Baptist',
                                    "JEHOVAH'S WITNESS"     => "Jehovah's Witness",
                                    'BUDDHISM'              => 'Buddhism',
                                    'HINDUISM'              => 'Hinduism',
                                    'OTHERS'                => 'Others',
                                ];
                                foreach ($religions as $val => $label):
                                ?>
                                    <option value="<?= $val ?>" <?= $f('religion') === $val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback"><?= $errors['religion'] ?? 'Please select your religion' ?></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="citizenship">Citizenship <span class="required">*</span></label>
                            <select class="form-control <?= isset($errors['citizenship']) ? 'is-invalid' : '' ?>"
                                id="citizenship" name="citizenship" required onchange="toggleCitizenshipOthers(this)">
                                <option value="">Select citizenship</option>
                                <option value="FILIPINO" <?= $citizenshipVal === 'FILIPINO' ? 'selected' : '' ?>>Filipino</option>
                                <option value="OTHERS"   <?= $isOthers ? 'selected' : '' ?>>Others</option>
                            </select>
                            <div class="invalid-feedback"><?= $errors['citizenship'] ?? 'Please select your citizenship' ?></div>

                            <div class="mt-2 <?= $isOthers ? '' : 'd-none' ?>" id="citizenship-others-container">
                                <input type="text"
                                    class="form-control <?= isset($errors['citizenship_others']) ? 'is-invalid' : '' ?>"
                                    id="citizenship_others" name="citizenship_others"
                                    placeholder="Please specify citizenship"
                                    value="<?= htmlspecialchars($citizenshipOthersValue) ?>">
                                <div class="invalid-feedback"><?= $errors['citizenship_others'] ?? 'Please specify your citizenship' ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="form-card">
                <div class="section-header">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                    </svg>
                    <h5>Contact Information</h5>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contact">Contact Number</label>
                            <input type="text" class="form-control <?= isset($errors['contact']) ? 'is-invalid' : '' ?>"
                                id="contact" name="contact"
                                value="<?= htmlspecialchars($f('contact')) ?>"
                                placeholder="09123456789" pattern="(\+63|0)\d{10}">
                            <div class="invalid-feedback"><?= $errors['contact'] ?? 'Please enter a valid Philippine phone number' ?></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                id="email" name="email"
                                value="<?= htmlspecialchars($f('email')) ?>"
                                placeholder="juan@example.com">
                            <div class="invalid-feedback"><?= $errors['email'] ?? 'Please enter a valid email address' ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Education & Employment -->
            <div class="form-card">
                <div class="section-header">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                    </svg>
                    <h5>Education & Employment</h5>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="education_attainment">Highest Educational Attainment <span class="required">*</span></label>
                            <select class="form-control <?= isset($errors['education_attainment']) ? 'is-invalid' : '' ?>"
                                id="education_attainment" name="education_attainment" required>
                                <option value="">Select educational attainment</option>
                                <?php
                                // FIX: cast both sides to string — PDO can return int from DB,
                                // causing strict === to silently fail and show blank selection.
                                $currentEdu = (string)$f('education_attainment');
                                foreach ($educationLevels as $val => $label):
                                ?>
                                    <option value="<?= $val ?>" <?= $currentEdu === (string)$val ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback"><?= $errors['education_attainment'] ?? 'Please select your educational attainment' ?></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Are you a graduate? <span class="required">*</span></label>
                            <div class="custom-radio-group">
                                <div class="custom-radio">
                                    <input type="radio" name="is_graduate" id="is_graduate_yes" value="YES"
                                        <?= $f('is_graduate') === 'YES' ? 'checked' : '' ?> required>
                                    <label for="is_graduate_yes">Yes</label>
                                </div>
                                <div class="custom-radio">
                                    <input type="radio" name="is_graduate" id="is_graduate_no" value="NO"
                                        <?= $f('is_graduate') === 'NO' ? 'checked' : '' ?> required>
                                    <label for="is_graduate_no">No</label>
                                </div>
                            </div>
                            <?php if (isset($errors['is_graduate'])): ?>
                                <div class="invalid-feedback d-block"><?= $errors['is_graduate'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="occupation">Occupation</label>
                            <select class="form-control <?= isset($errors['occupation']) ? 'is-invalid' : '' ?>"
                                id="occupation" name="occupation">
                                <option value="">Select occupation</option>
                                <?php
                                $occupations = [
                                    'Government Employee',
                                    'Private Employee',
                                    'Barangay Official',
                                    'Barangay Volunteers',
                                    'OFW',
                                    'Business',
                                    'Carpenter',
                                    'Laborer/Construction',
                                    'Driver',
                                    'Self-Employed',
                                    'Student',            // ADDED
                                    'Homemaker/Housewife', // ADDED
                                ];
                                foreach ($occupations as $occ):
                                ?>
                                    <option value="<?= $occ ?>" <?= $f('occupation') === $occ ? 'selected' : '' ?>><?= $occ ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback"><?= $errors['occupation'] ?? 'Please select your occupation' ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classifications -->
            <div class="form-card">
                <div class="section-header">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                    <h5>Classifications</h5>
                </div>

                <div class="classification-grid">
                    <?php foreach ($classifications as $classification): ?>
                        <?php
                        $isChecked       = isset($classificationData[$classification['id']]);
                        $orgId           = $isChecked ? ($classificationData[$classification['id']]['org_id'] ?? '') : '';
                        $hasOrganization = !empty($classification['organization']);
                        $itemClass       = $isChecked ? 'classification-item checked' : 'classification-item';
                        ?>
                        <div class="<?= $itemClass ?>" id="classification-item-<?= $classification['id'] ?>">
                            <div class="classification-checkbox">
                                <input type="checkbox" name="classifications[]"
                                    id="classification_<?= $classification['id'] ?>"
                                    value="<?= $classification['id'] ?>"
                                    <?= $isChecked ? 'checked' : '' ?>
                                    <?php if ($hasOrganization): ?>
                                        onchange="toggleClassificationInput(this, <?= $classification['id'] ?>)"
                                    <?php endif; ?>>
                                <label for="classification_<?= $classification['id'] ?>">
                                    <?= htmlspecialchars($classification['name']) ?>
                                </label>
                            </div>

                            <?php if ($hasOrganization): ?>
                                <div class="classification-id-input <?= $isChecked ? '' : 'd-none' ?>"
                                    id="classification-input-<?= $classification['id'] ?>">
                                    <input type="text"
                                        class="form-control form-control-sm <?= isset($errors['classification_org_ids'][$classification['id']]) ? 'is-invalid' : '' ?>"
                                        name="classification_org_ids[<?= $classification['id'] ?>]"
                                        placeholder="<?= htmlspecialchars($classification['code']) ?> ID Number"
                                        value="<?= htmlspecialchars($orgId) ?>">
                                    <?php if (isset($errors['classification_org_ids'][$classification['id']])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errors['classification_org_ids'][$classification['id']]) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Submit -->
            <div class="submit-section">
                <button type="submit" class="btn btn-submit" id="submitBtn">
                    Update Constituent
                </button>
            </div>

        </form>
    </div>
</div>

<!-- ── Error Modal ── -->
<div class="error-modal-overlay" id="errorModal" role="dialog" aria-modal="true" aria-labelledby="errorModalTitle">
    <div class="error-modal">
        <div class="error-modal-header">
            <div class="error-modal-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div class="error-modal-header-text">
                <h6 id="errorModalTitle">Error</h6>
                <span>Please review the details below</span>
            </div>
            <button class="error-modal-close" onclick="closeErrorModal()" aria-label="Close">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="error-modal-body">
            <p id="errorModalMessage"></p>
            <p id="errorModalDetail" class="error-modal-detail" style="display:none;"></p>
        </div>
        <div class="error-modal-footer">
            <button class="error-modal-btn" onclick="closeErrorModal()">
                Got it
            </button>
        </div>
    </div>
</div>

<script src="public/assets/js/constituent_edit.js"></script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
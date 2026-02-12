<?php
$content = ob_start();
?>
<div class="container-fluid px-4 mt-3">
    <div class="d-flex align-items-center mb-3">
        <a href="index.php?controller=constituents&action=index" class="mr-2">
            <img src="public/assets/icons/back.icon.png" alt="Back" style="width: 32px; height: 32px;">
        </a>
        <h3 class="font-weight-bold mb-0">Add Constituent</h3>
    </div>

    <div class="shadow-sm border">
        <div class="card-body">
            <?php if (isset($errors['create_constituent'])): ?>
                <div class="alert alert-danger">
                    <?= $errors['create_constituent'] ?>
                </div>
            <?php endif; ?>

            <form action="index.php?controller=constituents&action=create" method="post" id="constituentForm"
                novalidate>
                <div class="row">
                    <div class="col-md-6">
                        <label for="psn">PhilSys Card No.</label>
                        <input type="text" class="form-control <?= isset($errors['psn']) ? 'is-invalid' : '' ?>"
                            id="psn" name="psn" placeholder="ex. 1234567890123456" value="<?= $data['psn'] ?? '' ?>"
                            required pattern="\d{16}" title="Please enter a valid 16-digit PhilSys Number"
                            maxlength="16">
                        <div class="invalid-feedback" id="psn-feedback">
                            <?= $errors['psn'] ?? 'Please enter a valid 16-digit PhilSys Number' ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="registered_voter">Registered Voter? *</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check mr-3">
                                <input class="form-check-input" type="radio" name="registered_voter" id="yes"
                                    value="YES" <?= (isset($data['registered_voter']) && $data['registered_voter'] === 'YES') ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="registered_voter" id="no" value="NO"
                                    <?= (isset($data['registered_voter']) && $data['registered_voter'] === 'NO') ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="no">No</label>
                            </div>
                        </div>
                        <?php if (isset($errors['registered_voter'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= $errors['registered_voter'] ?>
                            </div>
                        <?php endif; ?>
                        <div class="invalid-feedback" id="registered_voter-feedback">
                            Please select if constituent is a registered voter
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="last_name">Last Name *</label>
                        <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>"
                            id="last_name" name="last_name" placeholder="ex. Dela Cruz" required
                            value="<?= $data['last_name'] ?? '' ?>" minlength="2" pattern="[A-Za-z\s\-']{2,}"
                            title="Last name must be at least 2 characters (letters only)">
                        <div class="invalid-feedback" id="last_name-feedback">
                            <?= $errors['last_name'] ?? 'Last name must be at least 2 characters (letters only)' ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="first_name">Given Name *</label>
                        <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
                            id="first_name" name="first_name" placeholder="ex. Juan" required
                            value="<?= $data['first_name'] ?? '' ?>" minlength="2" pattern="[A-Za-z\s\-']{2,}"
                            title="Given name must be at least 2 characters (letters only)">
                        <div class="invalid-feedback" id="first_name-feedback">
                            <?= $errors['first_name'] ?? 'Given name must be at least 2 characters (letters only)' ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" class="form-control <?= isset($errors['middle_name']) ? 'is-invalid' : '' ?>"
                            id="middle_name" name="middle_name" value="<?= $data['middle_name'] ?? '' ?>"
                            pattern="[A-Za-z\s\-']*" title="Middle name must contain letters only">
                        <div class="invalid-feedback" id="middle_name-feedback">
                            <?= $errors['middle_name'] ?? 'Middle name must contain letters only' ?>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label for="suffix">Suffix</label>
                        <select class="form-control <?= isset($errors['suffix']) ? 'is-invalid' : '' ?>" id="suffix"
                            name="suffix">
                            <option value="" <?= empty($data['suffix'] ?? '') ? 'selected' : '' ?>></option>
                            <option value="Jr." <?= ($data['suffix'] ?? '') === 'Jr.' ? 'selected' : '' ?>>Jr.</option>
                            <option value="Sr." <?= ($data['suffix'] ?? '') === 'Sr.' ? 'selected' : '' ?>>Sr.</option>
                            <option value="III" <?= ($data['suffix'] ?? '') === 'III' ? 'selected' : '' ?>>III</option>
                            <option value="IV" <?= ($data['suffix'] ?? '') === 'IV' ? 'selected' : '' ?>>IV</option>
                            <option value="V" <?= ($data['suffix'] ?? '') === 'V' ? 'selected' : '' ?>>V</option>
                            <option value="VI" <?= ($data['suffix'] ?? '') === 'VI' ? 'selected' : '' ?>>VI</option>
                            <option value="VII" <?= ($data['suffix'] ?? '') === 'VII' ? 'selected' : '' ?>>VII</option>
                            <option value="VIII" <?= ($data['suffix'] ?? '') === 'VIII' ? 'selected' : '' ?>>VIII</option>
                            <option value="IX" <?= ($data['suffix'] ?? '') === 'IX' ? 'selected' : '' ?>>IX</option>
                            <option value="X" <?= ($data['suffix'] ?? '') === 'X' ? 'selected' : '' ?>>X</option>
                        </select>
                        <div class="invalid-feedback" id="suffix-feedback">
                            <?= $errors['suffix'] ?? 'Please select a valid suffix' ?>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label for="sex">Sex *</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check mr-2">
                                <input class="form-check-input" type="radio" name="sex" id="male" value="Male"
                                    <?= (isset($data['sex']) && $data['sex'] === 'MALE') ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="male">Male</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sex" id="female" value="Female"
                                    <?= (isset($data['sex']) && $data['sex'] === 'FEMALE') ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="female">Female</label>
                            </div>
                        </div>
                        <?php if (isset($errors['sex'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= $errors['sex'] ?>
                            </div>
                        <?php endif; ?>
                        <div class="invalid-feedback" id="sex-feedback">
                            Required
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="birthdate">Birthdate</label>
                        <input type="date" class="form-control <?= isset($errors['birthdate']) ? 'is-invalid' : '' ?>"
                            id="birthdate" name="birthdate" required value="<?= $data['birthdate'] ?? '' ?>"
                            max="<?= date('Y-m-d') ?>">
                        <div class="invalid-feedback" id="birthdate-feedback">
                            <?= $errors['birthdate'] ?? 'Please enter a valid birthdate (not in the future)' ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="birthplace">Birth Place</label>
                        <input type="text" class="form-control <?= isset($errors['birthplace']) ? 'is-invalid' : '' ?>"
                            id="birthplace" name="birthplace" placeholder="TACLOBAN CITY" required
                            value="<?= $data['birthplace'] ?? '' ?>">
                        <div class="invalid-feedback" id="birthplace-feedback">
                            <?= $errors['birthplace'] ?? 'Please enter a valid birth place' ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="civil_status">Civil Status</label>
                        <select class="form-control <?= isset($errors['civil_status']) ? 'is-invalid' : '' ?>"
                            id="civil_status" name="civil_status" required>
                            <option value="" <?= empty($data['civil_status'] ?? '') ? 'selected' : '' ?>></option>
                            <option value="SINGLE" <?= ($data['civil_status'] ?? '') === 'SINGLE' ? 'selected' : '' ?>>
                                SINGLE
                            </option>
                            <option value="MARRIED" <?= ($data['civil_status'] ?? '') === 'MARRIED' ? 'selected' : '' ?>>
                                MARRIED</option>
                            <option value="WIDOWED" <?= ($data['civil_status'] ?? '') === 'WIDOWED' ? 'selected' : '' ?>>
                                WIDOWED</option>
                            <option value="SEPARATED" <?= ($data['civil_status'] ?? '') === 'SEPARATED' ? 'selected' : '' ?>>
                                SEPARATED</option>
                            <option value="DIVORCED" <?= ($data['civil_status'] ?? '') === 'DIVORCED' ? 'selected' : '' ?>>
                                DIVORCED</option>
                        </select>
                        <div class="invalid-feedback" id="civil_status-feedback">
                            <?= $errors['civil_status'] ?? 'Please select your civil status' ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="religion">Religion</label>
                        <select class="form-control <?= isset($errors['religion']) ? 'is-invalid' : '' ?>" id="religion"
                            name="religion" required>
                            <option value="" <?= empty($data['religion'] ?? '') ? 'selected' : '' ?>></option>
                            <option value="ROMAN CATHOLIC" <?= ($data['religion'] ?? '') === 'ROMAN CATHOLIC' ? 'selected' : '' ?>>ROMAN CATHOLIC</option>
                            <option value="PROTESTANT" <?= ($data['religion'] ?? '') === 'PROTESTANT' ? 'selected' : '' ?>>
                                PROTESTANT</option>
                            <option value="IGLESIA NI CRISTO" <?= ($data['religion'] ?? '') === 'IGLESIA NI CRISTO' ? 'selected' : '' ?>>IGLESIA NI CRISTO</option>
                            <option value="BORN AGAIN" <?= ($data['religion'] ?? '') === 'BORN AGAIN' ? 'selected' : '' ?>>
                                BORN AGAIN</option>
                            <option value="SEVENTH DAY ADVENTIST" <?= ($data['religion'] ?? '') === 'SEVENTH DAY ADVENTIST' ? 'selected' : '' ?>>SEVENTH DAY ADVENTIST</option>
                            <option value="ISLAM" <?= ($data['religion'] ?? '') === 'ISLAM' ? 'selected' : '' ?>>ISLAM
                            </option>
                            <option value="BAPTIST" <?= ($data['religion'] ?? '') === 'BAPTIST' ? 'selected' : '' ?>>
                                BAPTIST</option>
                            <option value="JEHOVAH'S WITNESS" <?= ($data['religion'] ?? '') === 'JEHOVAH\'S WITNESS' ? 'selected' : '' ?>>JEHOVAH'S WITNESS</option>
                            <option value="BUDDHISM" <?= ($data['religion'] ?? '') === 'BUDDHISM' ? 'selected' : '' ?>>
                                BUDDHISM</option>
                            <option value="HINDUISM" <?= ($data['religion'] ?? '') === 'HINDUISM' ? 'selected' : '' ?>>
                                HINDUISM</option>
                            <option value="OTHERS" <?= ($data['religion'] ?? '') === 'OTHERS' ? 'selected' : '' ?>>OTHERS
                            </option>
                        </select>
                        <div class="invalid-feedback" id="religion-feedback">
                            <?= $errors['religion'] ?? 'Please select your religion' ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="citizenship">Citizenship</label>
                        <select class="form-control <?= isset($errors['citizenship']) ? 'is-invalid' : '' ?>"
                            id="citizenship" name="citizenship" required>
                            <option value="FILIPINO" <?= ($data['citizenship'] ?? '') === 'FILIPINO' ? 'selected' : '' ?>>
                                FILIPINO</option>
                            <option value="OTHERS" <?= ($data['citizenship'] ?? '') === 'OTHERS' ? 'selected' : '' ?>>
                                OTHERS
                            </option>
                        </select>
                        <div class="invalid-feedback" id="citizenship-feedback">
                            <?= $errors['citizenship'] ?? 'Please select your citizenship' ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="contact">Contact Number</label>
                        <input type="text" class="form-control <?= isset($errors['contact']) ? 'is-invalid' : '' ?>"
                            id="contact" name="contact" value="<?= $data['contact'] ?? '' ?>"
                            placeholder="ex. 09123456789" pattern="(\+63|0)\d{10}"
                            title="Please enter a valid Philippine phone number (e.g., 09123456789 or +639123456789)">
                        <div class="invalid-feedback" id="contact-feedback">
                            <?= $errors['contact'] ?? 'Please enter a valid Philippine phone number (e.g., 09123456789 or +639123456789)' ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="email">Email</label>
                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                            id="email" name="email" value="<?= $data['email'] ?? '' ?>">
                        <div class="invalid-feedback" id="email-feedback">
                            <?= $errors['email'] ?? 'Please enter a valid email address' ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="education_attainment">Highest Educational Attainment</label>
                        <select class="form-control <?= isset($errors['education_attainment']) ? 'is-invalid' : '' ?>"
                            id="education_attainment" name="education_attainment" required>
                            <option value="" <?= empty($data['education_attainment'] ?? '') ? 'selected' : '' ?>>
                            </option>
                            <option value="1" <?= ($data['education_attainment'] ?? '') === '1' ? 'selected' : '' ?>>
                                DAYCARE
                            </option>
                            <option value="2" <?= ($data['education_attainment'] ?? '') === '2' ? 'selected' : '' ?>>
                                NURSERY
                                SCHOOL</option>
                            <option value="3" <?= ($data['education_attainment'] ?? '') === '3' ? 'selected' : '' ?>>
                                KINDERGARTEN
                            </option>
                            <option value="4" <?= ($data['education_attainment'] ?? '') === '4' ? 'selected' : '' ?>>
                                ELEMENTARY
                            </option>
                            <option value="5" <?= ($data['education_attainment'] ?? '') === '5' ? 'selected' : '' ?>>ALS
                                HIGH
                                SCHOOL
                            </option>
                            <option value="6" <?= ($data['education_attainment'] ?? '') === '6' ? 'selected' : '' ?>>HIGH
                                SCHOOL</option>
                            <option value="7" <?= ($data['education_attainment'] ?? '') === '7' ? 'selected' : '' ?>>JUNIOR
                                HIGH</option>
                            <option value="8" <?= ($data['education_attainment'] ?? '') === '8' ? 'selected' : '' ?>>SENIOR
                                HIGH</option>
                            <option value="9" <?= ($data['education_attainment'] ?? '') === '9' ? 'selected' : '' ?>>
                                VOCATIONAL
                            </option>
                            <option value="10" <?= ($data['education_attainment'] ?? '') === '10' ? 'selected' : '' ?>>
                                COLLEGE
                            </option>
                            <option value="11" <?= ($data['education_attainment'] ?? '') === '11' ? 'selected' : '' ?>>
                                POST-GRAD</option>
                        </select>
                        <div class="invalid-feedback" id="education_attainment-feedback">
                            <?= $errors['education_attainment'] ?? 'Please select your educational attainment' ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="is_graduate">Are you a graduate?</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check mr-3">
                                <input class="form-check-input" type="radio" name="is_graduate" id="is_graduate_yes"
                                    value="YES" <?= (isset($data['is_graduate']) && $data['is_graduate'] === 'YES') ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="is_graduate_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_graduate" id="is_graduate_no"
                                    value="NO" <?= (isset($data['is_graduate']) && $data['is_graduate'] === 'NO') ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="is_graduate_no">No</label>
                            </div>
                        </div>
                        <?php if (isset($errors['is_graduate'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= $errors['is_graduate'] ?>
                            </div>
                        <?php endif; ?>
                        <div class="invalid-feedback" id="is_graduate-feedback">
                            Please select if constituent is a graduate
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="occupation">Occupation</label>
                        <select class="form-control <?= isset($errors['occupation']) ? 'is-invalid' : '' ?>"
                            id="occupation" name="occupation">
                            <option value="" <?= empty($data['occupation'] ?? '') ? 'selected' : '' ?>></option>
                            <option value="Government Employee" <?= ($data['occupation'] ?? '') === 'Government Employee' ? 'selected' : '' ?>>Government Employee</option>
                            <option value="Private Employee" <?= ($data['occupation'] ?? '') === 'Private Employee' ? 'selected' : '' ?>>Private Employee</option>
                            <option value="Barangay Official" <?= ($data['occupation'] ?? '') === 'Barangay Official' ? 'selected' : '' ?>>Barangay Official</option>
                            <option value="Barangay Volunteers" <?= ($data['occupation'] ?? '') === 'Barangay Volunteers' ? 'selected' : '' ?>>Barangay Volunteers</option>
                            <option value="OFW" <?= ($data['occupation'] ?? '') === 'OFW' ? 'selected' : '' ?>>OFW</option>
                            <option value="Business" <?= ($data['occupation'] ?? '') === 'Business' ? 'selected' : '' ?>>
                                Business</option>
                            <option value="Carpenter" <?= ($data['occupation'] ?? '') === 'Carpenter' ? 'selected' : '' ?>>
                                Carpenter</option>
                            <option value="Laborer/Construction" <?= ($data['occupation'] ?? '') === 'Laborer/Construction' ? 'selected' : '' ?>>Laborer/Construction</option>
                            <option value="Driver" <?= ($data['occupation'] ?? '') === 'Driver' ? 'selected' : '' ?>>Driver
                            </option>
                            <option value="Sari-Sari Store" <?= ($data['occupation'] ?? '') === 'Sari-Sari Store' ? 'selected' : '' ?>>Sari-Sari Store</option>
                            <option value="Self-Employed" <?= ($data['occupation'] ?? '') === 'Self-Employed' ? 'selected' : '' ?>>Self-Employed</option>
                        </select>
                        <div class="invalid-feedback" id="occupation-feedback">
                            <?= $errors['occupation'] ?? 'Please select your occupation' ?>
                        </div>
                    </div>
                </div>

                <div class="border rounded p-5 mt-3">
                    <h5 class="font-weight-bold mb-3">Classifications</h5>
                    <div class="row">
                        <?php foreach ($classifications as $classification): ?>
                            <div class="form-check col-md-6">
                                <div class="d-flex flex-column">
                                    <?php
                                    $isChecked = isset($data['classifications']) && in_array($classification['id'], $data['classifications']);
                                    $inputDisplayClass = $isChecked ? '' : 'd-none';
                                    ?>
                                    <input class="form-check-input" type="checkbox" name="classifications[]"
                                        id="<?= strtolower($classification['id']) ?>" value="<?= $classification['id'] ?>"
                                        <?= $isChecked ? 'checked' : '' ?>
                                        onchange="toggleInputField(this, '<?= strtolower($classification['id']) ?>InputId');">
                                    <label class="form-check-label" for="<?= strtolower($classification['id']) ?>">
                                        <?= $classification['name'] ?>
                                    </label>
                                    <div>
                                        <input type="text"
                                            class="form-control mb-2 <?= $inputDisplayClass ?> <?= isset($errors['classification_org_ids'][$classification['id']]) ? 'is-invalid' : '' ?>"
                                            id="<?= strtolower($classification['id']) ?>InputId"
                                            name="classification_org_ids[<?= $classification['id'] ?>]"
                                            placeholder="<?= $classification['code'] ?> ID No."
                                            value="<?= $data['classification_org_ids'][$classification['id']] ?? '' ?>">
                                        <?php if (isset($errors['classification_org_ids'][$classification['id']])): ?>
                                            <div class="invalid-feedback">
                                                <?= $errors['classification_org_ids'][$classification['id']] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary px-4">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('constituentForm');
            const formInputs = form.querySelectorAll('input, select');

            // Setup validation for each input
            formInputs.forEach(input => {
                input.addEventListener('input', function () {
                    validateInput(input);
                });

                input.addEventListener('change', function () {
                    validateInput(input);
                });

                // Initial validation on page load
                if (input.value) {
                    validateInput(input);
                }
            });

            // PSN specific validation (numbers only)
            const psnInput = document.getElementById('psn');
            psnInput.addEventListener('input', function (e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                validateInput(this);
            });

            // Name fields validation (letters, spaces, hyphens, apostrophes only)
            const nameFields = ['last_name', 'first_name', 'middle_name'];
            nameFields.forEach(field => {
                const input = document.getElementById(field);
                if (input) {
                    input.addEventListener('input', function () {
                        if (this.value && !/^[A-Za-z\s\-']+$/.test(this.value)) {
                            this.value = this.value.replace(/[^A-Za-z\s\-']/g, '');
                        }
                        validateInput(this);
                    });
                }
            });

            // Contact field validation
            const contactInput = document.getElementById('contact');
            contactInput.addEventListener('input', function (e) {
                this.value = this.value.replace(/[^0-9+]/g, '');
                validateInput(this);
            });

            // Form submission validation
            form.addEventListener('submit', function (event) {
                let isValid = true;

                // Validate all inputs before submission
                formInputs.forEach(input => {
                    if (!validateInput(input)) {
                        isValid = false;
                    }
                });

                // Prevent form submission if validation fails
                if (!isValid) {
                    event.preventDefault();
                    // Scroll to the first invalid field
                    const firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalid.focus();
                    }
                }
            });

            // Helper function to validate a single input
            function validateInput(input) {
                let isValid = true;

                // Clear previous validation state
                input.classList.remove('is-invalid', 'is-valid');

                // Skip validation for non-required empty fields
                if (!input.required && !input.value) {
                    return true;
                }

                // Check required fields
                if (input.required && !input.value) {
                    isValid = false;
                }

                // Validate patterns for text inputs
                if (input.pattern && input.value) {
                    const pattern = new RegExp(input.pattern);
                    if (!pattern.test(input.value)) {
                        isValid = false;
                    }
                }

                // Radio button validation
                if (input.type === 'radio' && input.required) {
                    const radioGroup = document.querySelectorAll(`input[name="${input.name}"]`);
                    isValid = Array.from(radioGroup).some(radio => radio.checked);

                    // Apply the validation result to all radios in the group
                    if (isValid) {
                        radioGroup.forEach(radio => {
                            radio.classList.remove('is-invalid');
                            radio.classList.add('is-valid');
                        });

                        // Hide feedback for the entire radio group
                        const feedbackElement = document.getElementById(`${input.name}-feedback`);
                        if (feedbackElement) {
                            feedbackElement.style.display = 'none';
                        }
                        return true;
                    } else {
                        radioGroup.forEach(radio => {
                            radio.classList.add('is-invalid');
                        });

                        // Show feedback for the entire radio group
                        const feedbackElement = document.getElementById(`${input.name}-feedback`);
                        if (feedbackElement) {
                            feedbackElement.style.display = 'block';
                        }
                        return false;
                    }
                }

                // Email validation
                if (input.type === 'email' && input.value) {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(input.value)) {
                        isValid = false;
                    }
                }

                // Date validation
                if (input.type === 'date' && input.value) {
                    const selectedDate = new Date(input.value);
                    const today = new Date();
                    if (selectedDate > today) {
                        isValid = false;
                    }
                }

                // Add appropriate feedback
                if (!isValid) {
                    input.classList.add('is-invalid');

                    // For radio buttons, show feedback - This is now handled in the radio button validation section above
                    if (input.type === 'radio') {
                        return false; // Return early as we now handle radio buttons above
                    }
                } else {
                    input.classList.add('is-valid');
                }

                return isValid;
            }
        });

        function toggleInputField(checkbox, inputId) {
            const inputField = document.getElementById(inputId);
            if (checkbox.checked) {
                inputField.classList.remove('d-none');
                // Validate the input field if it becomes visible
                validateInput(inputField);
            } else {
                inputField.classList.add('d-none');
                // Clear validation state when hidden
                inputField.classList.remove('is-invalid', 'is-valid');
            }
        }

        // Helper function for the toggleInputField function
        function validateInput(input) {
            if (!input) return true;

            let isValid = true;

            // Clear previous validation state
            input.classList.remove('is-invalid', 'is-valid');

            // Skip validation for non-required empty fields
            if (!input.required && !input.value) {
                return true;
            }

            // Check required fields
            if (input.required && !input.value) {
                isValid = false;
            }

            // Validate patterns
            if (input.pattern && input.value) {
                const pattern = new RegExp(input.pattern);
                if (!pattern.test(input.value)) {
                    isValid = false;
                }
            }

            // Add appropriate feedback
            if (!isValid) {
                input.classList.add('is-invalid');
            } else {
                input.classList.add('is-valid');
            }

            return isValid;
        }
    </script>

    <?php
    $content = ob_get_clean();
    require_once 'app/Views/layouts/main.php';
    ?>
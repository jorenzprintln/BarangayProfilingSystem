(function () {

    // ── Error Modal ──
    function showErrorModal(title, message, detail) {
        var modal   = document.getElementById('errorModal');
        var mTitle  = document.getElementById('errorModalTitle');
        var mMsg    = document.getElementById('errorModalMessage');
        var mDetail = document.getElementById('errorModalDetail');
        if (!modal) return;

        mTitle.textContent = title;
        mMsg.textContent   = message;

        if (detail && mDetail) {
            mDetail.textContent   = detail;
            mDetail.style.display = 'block';
        } else if (mDetail) {
            mDetail.style.display = 'none';
        }

        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    window.closeErrorModal = function () {
        var modal = document.getElementById('errorModal');
        if (!modal) return;
        modal.classList.remove('is-active');
        document.body.style.overflow = '';
    };

    // Close on backdrop click
    document.addEventListener('click', function (e) {
        if (e.target === document.getElementById('errorModal')) {
            window.closeErrorModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.closeErrorModal();
    });

    // ── Helpers ──
    function setFeedback(input, message) {
        var feedback = input.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            input.parentNode.appendChild(feedback);
        }
        feedback.textContent = message;
    }

    // ── PSN ──
    function validatePsn(input) {
        input.classList.remove('is-invalid', 'is-valid');
        if (!input.value) return;
        if (!/^\d{16}$/.test(input.value)) {
            input.classList.add('is-invalid');
            setFeedback(input, 'PSN must be exactly 16 digits.');
        } else {
            input.classList.add('is-valid');
        }
    }

    // ── Contact ──
    function validateContact(input) {
        input.classList.remove('is-invalid', 'is-valid');
        if (!input.value) return;
        var phRegex = /^(09\d{9}|\+639\d{9})$/;
        if (!phRegex.test(input.value)) {
            input.classList.add('is-invalid');
            setFeedback(input, 'Enter a valid PH number: 09XXXXXXXXX or +639XXXXXXXXX');
        } else {
            input.classList.add('is-valid');
        }
    }

    // ── Email ──
    function validateEmail(input) {
        input.classList.remove('is-invalid', 'is-valid');
        if (!input.value) return;
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
        if (!emailRegex.test(input.value)) {
            input.classList.add('is-invalid');
            setFeedback(input, 'Enter a valid email address (e.g. juan@example.com)');
        } else {
            input.classList.add('is-valid');
        }
    }

    // ── Name fields ──
    function validateName(input, required) {
        input.classList.remove('is-invalid', 'is-valid');
        if (!input.value && !required) return;
        if (required && (!input.value || input.value.trim().length < 2)) {
            input.classList.add('is-invalid');
            setFeedback(input, 'Must be at least 2 characters (letters only).');
        } else if (input.value && input.value.trim().length < 2) {
            input.classList.add('is-invalid');
            setFeedback(input, 'Must be at least 2 characters (letters only).');
        } else if (input.value) {
            input.classList.add('is-valid');
        }
    }

    // ── Birthdate ──
    function validateBirthdate(input) {
        input.classList.remove('is-invalid', 'is-valid');
        if (!input.value) {
            input.classList.add('is-invalid');
            setFeedback(input, 'Birthdate is required.');
            return;
        }
        var selected = new Date(input.value);
        var today    = new Date();
        if (selected > today) {
            input.classList.add('is-invalid');
            setFeedback(input, 'Birthdate cannot be in the future.');
        } else {
            input.classList.add('is-valid');
        }
    }

    // ── Senior Citizen auto-check ──
    // NOTE: Only auto-ADD the SC classification based on age.
    // Never auto-REMOVE it if it was already saved/approved (data-saved="1").
    function handleSeniorCitizenAutoCheck(birthdateValue) {
        if (!birthdateValue) return;
        var birthDate = new Date(birthdateValue);
        var today     = new Date();
        var age = today.getFullYear() - birthDate.getFullYear();
        var monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) age--;

        document.querySelectorAll('input[name="classifications[]"]').forEach(function (checkbox) {
            var label = checkbox.closest('.classification-checkbox')
                ?.querySelector('label')?.textContent?.trim().toLowerCase();
            if (!label || !(label.includes('senior citizen') || label.includes('senior'))) return;

            var id       = checkbox.value;
            var inputDiv = document.getElementById('classification-input-' + id);
            var item     = document.getElementById('classification-item-' + id);

            if (age >= 60) {
                checkbox.checked  = true;
                checkbox.disabled = true;
                item.classList.add('checked');
                if (inputDiv) inputDiv.classList.remove('d-none');
                if (!document.getElementById('sc-auto-notice')) {
                    var notice = document.createElement('small');
                    notice.id = 'sc-auto-notice';
                    notice.style.cssText = 'color:#4361ee;font-size:0.8rem;display:block;margin-top:0.25rem;';
                    notice.textContent = 'Automatically classified as Senior Citizen based on age';
                    item.appendChild(notice);
                }
            } else {
                // FIX: Only remove SC auto-classification if it was NOT saved from an approved record
                if (checkbox.getAttribute('data-saved') !== '1') {
                    checkbox.checked  = false;
                    checkbox.disabled = false;
                    item.classList.remove('checked');
                    if (inputDiv) {
                        inputDiv.classList.add('d-none');
                        var inp = inputDiv.querySelector('input');
                        if (inp) { inp.value = ''; inp.required = false; }
                    }
                    var notice = document.getElementById('sc-auto-notice');
                    if (notice) notice.remove();
                }
            }
        });
    }

    // ── Labor/OFW/Student auto-check ──
    // FIX: The else branches now check data-saved="1" before removing a classification.
    // This prevents wiping classifications that were manually checked by the constituent
    // and approved by the admin (e.g. Student occupation + Labor/Employed manually checked).
    function handleLaborEmployedAutoCheck(occupationValue) {
        var employedOccupations = [
            'Government Employee', 'Private Employee', 'OFW', 'Business', 'Self-Employed',
            'Carpenter', 'Laborer/Construction', 'Driver', 'Sari-Sari Store'
        ];
        var isEmployed = employedOccupations.includes(occupationValue);
        var isOFW      = occupationValue === 'OFW';
        var isStudent  = occupationValue === 'Student';

        document.querySelectorAll('input[name="classifications[]"]').forEach(function (checkbox) {
            var label = checkbox.closest('.classification-checkbox')
                ?.querySelector('label')?.textContent?.trim().toLowerCase();
            var id       = checkbox.value;
            var inputDiv = document.getElementById('classification-input-' + id);
            var item     = document.getElementById('classification-item-' + id);

            // ── Labor/Employed ──
            if (label && (label === 'labor/employed' ||
                (label.includes('labor') && label.includes('employed') && !label.includes('unemployed')))) {
                if (isEmployed) {
                    checkbox.checked = true; checkbox.disabled = true;
                    item.classList.add('checked');
                    if (inputDiv) inputDiv.classList.remove('d-none');
                    if (!document.getElementById('labor-auto-notice')) {
                        var n = document.createElement('small');
                        n.id = 'labor-auto-notice';
                        n.style.cssText = 'color:#4361ee;font-size:0.8rem;display:block;margin-top:0.25rem;';
                        n.textContent = 'Automatically classified as Labor/Employed based on occupation';
                        item.appendChild(n);
                    }
                } else {
                    // FIX: Respect saved/approved state — do not uncheck if data-saved="1"
                    if (checkbox.getAttribute('data-saved') !== '1') {
                        checkbox.checked = false; checkbox.disabled = false;
                        item.classList.remove('checked');
                        if (inputDiv) {
                            inputDiv.classList.add('d-none');
                            var inp = inputDiv.querySelector('input');
                            if (inp) { inp.value = ''; inp.required = false; inp.classList.remove('is-invalid', 'is-valid'); }
                        }
                        var n = document.getElementById('labor-auto-notice');
                        if (n) n.remove();
                    }
                }
            }

            // ── OFW ──
            if (label && (label === 'ofw' || label.includes('overseas filipino worker'))) {
                if (isOFW) {
                    checkbox.checked = true; checkbox.disabled = true;
                    item.classList.add('checked');
                    if (inputDiv) inputDiv.classList.remove('d-none');
                    if (!document.getElementById('ofw-auto-notice')) {
                        var n = document.createElement('small');
                        n.id = 'ofw-auto-notice';
                        n.style.cssText = 'color:#4361ee;font-size:0.8rem;display:block;margin-top:0.25rem;';
                        n.textContent = 'Automatically classified as OFW based on occupation';
                        item.appendChild(n);
                    }
                } else {
                    // FIX: Respect saved/approved state — do not uncheck if data-saved="1"
                    if (checkbox.getAttribute('data-saved') !== '1') {
                        checkbox.checked = false; checkbox.disabled = false;
                        item.classList.remove('checked');
                        if (inputDiv) {
                            inputDiv.classList.add('d-none');
                            var inp = inputDiv.querySelector('input');
                            if (inp) { inp.value = ''; inp.required = false; inp.classList.remove('is-invalid', 'is-valid'); }
                        }
                        var n = document.getElementById('ofw-auto-notice');
                        if (n) n.remove();
                    }
                }
            }

            // ── Student ──
            if (label && (label.trim() === 'student' || (label.includes('student') && !label.includes('out of school')))) {
                if (isStudent) {
                    checkbox.checked = true; checkbox.disabled = true;
                    item.classList.add('checked');
                    if (inputDiv) inputDiv.classList.remove('d-none');
                    if (!document.getElementById('student-auto-notice')) {
                        var n = document.createElement('small');
                        n.id = 'student-auto-notice';
                        n.style.cssText = 'color:#4361ee;font-size:0.8rem;display:block;margin-top:0.25rem;';
                        n.textContent = 'Automatically classified as Student based on occupation';
                        item.appendChild(n);
                    }
                } else {
                    // FIX: Respect saved/approved state — do not uncheck if data-saved="1"
                    if (checkbox.getAttribute('data-saved') !== '1') {
                        checkbox.checked = false; checkbox.disabled = false;
                        item.classList.remove('checked');
                        if (inputDiv) {
                            inputDiv.classList.add('d-none');
                            var inp = inputDiv.querySelector('input');
                            if (inp) { inp.value = ''; inp.required = false; inp.classList.remove('is-invalid', 'is-valid'); }
                        }
                        var n = document.getElementById('student-auto-notice');
                        if (n) n.remove();
                    }
                }
            }
        });
    }

    // ── ID number input (numbers only) ──
    function setupNumberOnlyInput(input) {
        input.setAttribute('inputmode', 'numeric');
        input.setAttribute('pattern', '[0-9]+');
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
            validateIdInput(this);
        });
        input.addEventListener('blur', function () { validateIdInput(this); });
    }

    function validateIdInput(input) {
        input.classList.remove('is-invalid', 'is-valid');
        if (input.required && (!input.value || input.value.trim() === '')) {
            input.classList.add('is-invalid');
            setFeedback(input, 'ID number is required (numbers only).');
        } else if (input.value && !/^[0-9]+$/.test(input.value)) {
            input.classList.add('is-invalid');
            setFeedback(input, 'Numbers only.');
        } else if (input.value) {
            input.classList.add('is-valid');
        }
    }

    // ── Classification checkboxes setup ──
    function setupClassificationInputs() {
        document.querySelectorAll('input[name="classifications[]"]').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                var id       = this.value;
                var inputDiv = document.getElementById('classification-input-' + id);
                var item     = document.getElementById('classification-item-' + id);
                if (this.checked) {
                    item.classList.add('checked');
                    if (inputDiv) {
                        inputDiv.classList.remove('d-none');
                        var inp = inputDiv.querySelector('input');
                        if (inp) { inp.required = false; setupNumberOnlyInput(inp); }
                    }
                } else {
                    item.classList.remove('checked');
                    if (inputDiv) {
                        inputDiv.classList.add('d-none');
                        var inp = inputDiv.querySelector('input');
                        if (inp) { inp.required = false; inp.value = ''; inp.classList.remove('is-invalid', 'is-valid'); }
                    }
                }
            });
            // Already checked on load
            if (checkbox.checked) {
                var inputDiv = document.getElementById('classification-input-' + checkbox.value);
                if (inputDiv) {
                    var inp = inputDiv.querySelector('input');
                    if (inp) { inp.required = false; setupNumberOnlyInput(inp); }
                }
            }
        });
    }

    // ── Citizenship others toggle ──
    window.toggleCitizenshipOthers = function (select) {
        var container = document.getElementById('citizenship-others-container');
        var input     = document.getElementById('citizenship_others');
        if (select.value === 'OTHERS') {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
            input.value = '';
        }
    };

    // ── Classification input toggle (used by inline onclick) ──
    window.toggleClassificationInput = function (checkbox, classificationId) {
        var inputDiv = document.getElementById('classification-input-' + classificationId);
        var item     = document.getElementById('classification-item-' + classificationId);
        if (checkbox.checked) {
            inputDiv.classList.remove('d-none');
            item.classList.add('checked');
        } else {
            inputDiv.classList.add('d-none');
            item.classList.remove('checked');
            var inp = inputDiv.querySelector('input');
            if (inp) inp.value = '';
        }
    };

    // ── DOMContentLoaded ──
    document.addEventListener('DOMContentLoaded', function () {

        // ── Show server-side error modal on page load ──
        if (typeof SERVER_ERROR !== 'undefined') {
            if (SERVER_ERROR.type === 'duplicate_org_id') {
                var inputs = document.querySelectorAll('.classification-id-input:not(.d-none) input');
                inputs.forEach(function (input) {
                    if (input.value.trim() === SERVER_ERROR.org_id) {
                        input.classList.add('is-invalid');
                        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
                showErrorModal(
                    'Duplicate Organization ID',
                    'The ID number "' + SERVER_ERROR.org_id + '" is already registered to another constituent under the "' + SERVER_ERROR.classification + '" classification.',
                    'Please enter a different ID number for this classification before submitting again.'
                );
            } else if (SERVER_ERROR.type === 'general') {
                showErrorModal('Registration Failed', SERVER_ERROR.message, null);
            }
        }

        // PSN duplicate modal
        if (window.CONSTITUENT_PSN_ERROR) {
            var psnInput = document.getElementById('psn');
            if (psnInput) {
                document.getElementById('duplicatePsnValue').textContent = psnInput.value;
                psnInput.classList.add('is-invalid');
                $('#psnDuplicateModal').modal('show');
                $('#psnDuplicateModal').on('hidden.bs.modal', function () {
                    psnInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    psnInput.focus();
                });
            }
        }

        // PSN input
        var psnInput = document.getElementById('psn');
        if (psnInput) {
            psnInput.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
                validatePsn(this);
            });
            psnInput.addEventListener('blur', function () { validatePsn(this); });
        }

        // Contact input
        var contactInput = document.getElementById('contact');
        if (contactInput) {
            contactInput.addEventListener('input', function () {
                var val = this.value;
                this.value = val.startsWith('+')
                    ? '+' + val.slice(1).replace(/[^0-9]/g, '')
                    : val.replace(/[^0-9]/g, '');
                validateContact(this);
            });
            contactInput.addEventListener('blur', function () { validateContact(this); });
        }

        // Email input
        var emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('input', function () { validateEmail(this); });
            emailInput.addEventListener('blur', function () { validateEmail(this); });
        }

        // Name inputs
        ['last_name', 'first_name', 'middle_name'].forEach(function (fieldId) {
            var input = document.getElementById(fieldId);
            if (input) {
                var required = fieldId !== 'middle_name';
                input.addEventListener('input', function () {
                    this.value = this.value.replace(/[^A-Za-z\s\-']/g, '');
                    validateName(this, required);
                });
                input.addEventListener('blur', function () { validateName(this, required); });
            }
        });

        // Birthdate input
        var birthdateInput = document.getElementById('birthdate');
        if (birthdateInput) {
            birthdateInput.addEventListener('change', function () {
                validateBirthdate(this);
                handleSeniorCitizenAutoCheck(this.value);
            });
            if (birthdateInput.value) {
                handleSeniorCitizenAutoCheck(birthdateInput.value);
            }
        }

        // Occupation select
        var occupationSelect = document.getElementById('occupation');
        if (occupationSelect) {
            occupationSelect.addEventListener('change', function () {
                handleLaborEmployedAutoCheck(this.value);
            });
            if (occupationSelect.value) {
                handleLaborEmployedAutoCheck(occupationSelect.value);
            }
        }

        setupClassificationInputs();

        // ── Profile form: disable submit until changes are made ──
        (function () {
            var profileForm = document.getElementById('constituentForm');
            if (!profileForm || !profileForm.action || !profileForm.action.includes('saveProfile')) return;
            var submitBtn = document.getElementById('submitBtn');
            if (!submitBtn) return;

            submitBtn.disabled      = true;
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor  = 'not-allowed';
            submitBtn.title         = 'Make changes before submitting';

            function getFormState() {
                var state = {};
                profileForm.querySelectorAll('input:not([type=checkbox]):not([type=radio]):not([type=submit]), select, textarea').forEach(function (el) {
                    if (el.name) state[el.name + '_' + (el.id || el.name)] = el.value;
                });
                profileForm.querySelectorAll('input[type=radio]').forEach(function (el) {
                    if (el.checked) state['radio_' + el.name] = el.value;
                });
                profileForm.querySelectorAll('input[type=checkbox]').forEach(function (el) {
                    if (el.name) state['cb_' + el.name + '_' + el.value] = el.checked;
                });
                return JSON.stringify(state);
            }

            var initialState = getFormState();

            function checkForChanges() {
                var hasChanged          = getFormState() !== initialState;
                submitBtn.disabled      = !hasChanged;
                submitBtn.style.opacity = hasChanged ? '1'       : '0.5';
                submitBtn.style.cursor  = hasChanged ? 'pointer' : 'not-allowed';
                submitBtn.title         = hasChanged ? ''        : 'Make changes before submitting';
            }

            profileForm.addEventListener('input',  checkForChanges);
            profileForm.addEventListener('change', checkForChanges);
        })();

        // ── Form submit: classification fix + field validation ──
        var form = document.getElementById('constituentForm');
        if (form) {
            form.addEventListener('submit', function (e) {

                // 1. Fix classifications before submit
                document.querySelectorAll('.classification-item input[type="checkbox"]').forEach(function (cb) {
                    if (cb.checked && cb.disabled) {
                        // Auto-classified checkboxes are disabled — inject hidden input so value submits
                        var hidden   = document.createElement('input');
                        hidden.type  = 'hidden';
                        hidden.name  = 'classifications[]';
                        hidden.value = cb.value;
                        form.appendChild(hidden);
                    } else if (!cb.checked) {
                        // Disable org ID inputs for unchecked so they don't submit
                        var container = document.getElementById('classification-input-' + cb.value);
                        if (container) {
                            container.querySelectorAll('input').forEach(function (inp) {
                                inp.disabled = true;
                            });
                        }
                    }
                });

                // 2. Field validation
                var isValid = true;

                if (psnInput && psnInput.value)         { validatePsn(psnInput);        if (psnInput.classList.contains('is-invalid'))     isValid = false; }
                if (contactInput && contactInput.value) { validateContact(contactInput); if (contactInput.classList.contains('is-invalid')) isValid = false; }
                if (emailInput && emailInput.value)     { validateEmail(emailInput);     if (emailInput.classList.contains('is-invalid'))   isValid = false; }

                ['last_name', 'first_name'].forEach(function (id) {
                    var inp = document.getElementById(id);
                    if (inp) { validateName(inp, true); if (inp.classList.contains('is-invalid')) isValid = false; }
                });

                if (birthdateInput) { validateBirthdate(birthdateInput); if (birthdateInput.classList.contains('is-invalid')) isValid = false; }

                document.querySelectorAll('.classification-id-input:not(.d-none) input').forEach(function (inp) {
                    validateIdInput(inp);
                    if (inp.classList.contains('is-invalid')) isValid = false;
                });

                if (!isValid) {
                    e.preventDefault();
                    var firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalid.focus();
                    }
                }
            });
        }

    }); // end DOMContentLoaded

})();
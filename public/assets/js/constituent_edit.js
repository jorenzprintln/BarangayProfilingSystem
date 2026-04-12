(function () {

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

    document.addEventListener('click', function (e) {
        if (e.target === document.getElementById('errorModal')) {
            window.closeErrorModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.closeErrorModal();
    });

    window.toggleClassificationInput = function (checkbox, classificationId) {
        var inputDiv = document.getElementById('classification-input-' + classificationId);
        var item     = document.getElementById('classification-item-' + classificationId);
        if (checkbox.checked) {
            inputDiv.classList.remove('d-none');
            item.classList.add('checked');
            var input = inputDiv.querySelector('input');
            if (input) input.required = true;
        } else {
            inputDiv.classList.add('d-none');
            item.classList.remove('checked');
            var input = inputDiv.querySelector('input');
            if (input) {
                input.value = '';
                input.required = false;
                input.classList.remove('is-invalid', 'is-valid');
            }
        }
    };

    window.toggleCitizenshipOthers = function (select) {
        var container = document.getElementById('citizenship-others-container');
        var input     = document.getElementById('citizenship_others');
        if (select.value === 'OTHERS') {
            container.classList.remove('d-none');
            input.required = true;
        } else {
            container.classList.add('d-none');
            input.required = false;
            input.value = '';
            input.classList.remove('is-invalid', 'is-valid');
        }
    };

    document.addEventListener('DOMContentLoaded', function () {

        var form      = document.getElementById('constituentForm');
        var submitBtn = document.getElementById('submitBtn');
        var hasChanged = false;

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
                showErrorModal('Update Failed', SERVER_ERROR.message, null);
            }
        }

        var formInputs = form.querySelectorAll('input:not([type="radio"]):not([type="checkbox"]), select');
        formInputs.forEach(function (input) {
            input.addEventListener('blur', function () {
                if (!this.required && (!this.value || this.value.trim() === '')) {
                    this.classList.remove('is-invalid', 'is-valid');
                    return;
                }
                if (this.required && (!this.value || this.value.trim() === '')) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                } else if (this.value && this.value.trim() !== '') {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                }
            });
        });

        function getFormState() {
            var entries = [];
            form.querySelectorAll('input, select, textarea').forEach(function (el) {
                if (el.type === 'checkbox') {
                    entries.push(el.name + '=' + (el.checked ? el.value : ''));
                } else if (el.type === 'radio') {
                    if (el.checked) entries.push(el.name + '=' + el.value);
                } else {
                    entries.push(el.name + '=' + el.value);
                }
            });
            return entries.join('|');
        }

        var originalState = getFormState();

        submitBtn.setAttribute('disabled', 'disabled');
        submitBtn.style.opacity    = '0.5';
        submitBtn.style.cursor     = 'not-allowed';
        submitBtn.style.pointerEvents = 'none';

        function checkForChanges() {
            hasChanged = getFormState() !== originalState;

            if (hasChanged) {
                submitBtn.removeAttribute('disabled');
                submitBtn.style.opacity       = '1';
                submitBtn.style.cursor        = 'pointer';
                submitBtn.style.pointerEvents = 'auto';
            } else {
                submitBtn.setAttribute('disabled', 'disabled');
                submitBtn.style.opacity       = '0.5';
                submitBtn.style.cursor        = 'not-allowed';
                submitBtn.style.pointerEvents = 'none';
            }
        }

        form.addEventListener('input',  checkForChanges);
        form.addEventListener('change', checkForChanges);

        form.addEventListener('submit', function (e) {
            if (!hasChanged) {
                e.preventDefault();
                return;
            }
            submitBtn.setAttribute('disabled', 'disabled');
            submitBtn.textContent = 'Updating...';
        });

    });

})();   
(function () {
    document.addEventListener('DOMContentLoaded', function () {

        const searchInput          = document.getElementById('search-input');
        const submitBtn            = document.getElementById('submitHouseholdForm');
        const householdNumberInput = document.getElementById('household_number');
        const errorMsg             = document.getElementById('household_number_error');

        // ── Reset to clean state ──
        function resetHouseholdNumber() {
            if (!householdNumberInput) return;
            householdNumberInput.classList.remove('is-invalid', 'is-valid');
            if (errorMsg) {
                errorMsg.classList.remove('visible');
                errorMsg.style.display = 'none';
            }
            if (submitBtn) submitBtn.disabled = false;
        }

        // ── Validate only if user typed something ──
        function validateHouseholdNumber() {
            if (!householdNumberInput) return true;
            const value = householdNumberInput.value.trim();

            // Empty = OK, field is optional
            if (value.length === 0) {
                resetHouseholdNumber();
                return true;
            }

            // Must be digits only, max 24 chars
            const isValid = /^\d+$/.test(value) && value.length <= 24;

            if (!isValid) {
                householdNumberInput.classList.add('is-invalid');
                householdNumberInput.classList.remove('is-valid');
                if (errorMsg) {
                    errorMsg.classList.add('visible');
                    errorMsg.style.display = 'block';
                }
                if (submitBtn) submitBtn.disabled = true;
            } else {
                householdNumberInput.classList.remove('is-invalid');
                householdNumberInput.classList.add('is-valid');
                if (errorMsg) {
                    errorMsg.classList.remove('visible');
                    errorMsg.style.display = 'none';
                }
                if (submitBtn) submitBtn.disabled = false;
            }

            return isValid;
        }

        // ── Listen for user input only ──
        if (householdNumberInput) {
            householdNumberInput.addEventListener('input', validateHouseholdNumber);
        }

        // ── Reset EVERY time modal opens ──
        $('#createHouseholdModal').on('show.bs.modal', function () {
            resetHouseholdNumber();
        });

        // ── Also reset when modal is fully hidden ──
        $('#createHouseholdModal').on('hidden.bs.modal', function () {
            resetHouseholdNumber();
        });

        // ── Re-open modal on PHP error ──
        if (window.HOUSEHOLDS_OPEN_MODAL) {
            $('#createHouseholdModal').modal('show');
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('open_modal')) {
            $('#createHouseholdModal').modal('show');
        }

        // ── Submit ──
        if (submitBtn) {
            submitBtn.addEventListener('click', function () {
                let isValid = true;

                if (!validateHouseholdNumber()) isValid = false;

                const zoneInput = document.getElementById('zone');
                if (zoneInput && !zoneInput.value.trim()) {
                    zoneInput.classList.add('is-invalid');
                    isValid = false;
                } else if (zoneInput) {
                    zoneInput.classList.remove('is-invalid');
                }

                if (isValid) {
                    document.getElementById('createHouseholdForm').submit();
                }
            });
        }

        // ── Zone live validation ──
        const zoneInput = document.getElementById('zone');
        if (zoneInput) {
            zoneInput.addEventListener('input', function () {
                if (this.value.trim()) this.classList.remove('is-invalid');
            });
        }

        // ── Search debounce ──
        if (searchInput) {
            let searchTimer;
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    document.getElementById('search-form').submit();
                }, 400);
            });
        }

        // ── Delete modal ──
        $('#deleteConfirmModal').on('show.bs.modal', function (event) {
            const button      = $(event.relatedTarget);
            const householdId = button.data('household-id');
            $(this).find('#confirmDeleteBtn').attr('href',
                'index.php?controller=households&action=delete&household_id=' + householdId);
        });

        // ── Auto-dismiss alerts ──
        setTimeout(function () {
            document.querySelectorAll('.alert').forEach(function (msg) {
                $(msg).alert('close');
            });
        }, 5000);

    });
})();
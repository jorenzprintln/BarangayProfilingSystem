(function () {
    document.addEventListener('DOMContentLoaded', function () {

        const memberCheckboxes = document.querySelectorAll('.member-checkbox');
        const headSelect       = document.getElementById('head_constituent_id');
        const memberCountEl    = document.getElementById('memberCount');
        const memberItems      = document.querySelectorAll('.member-check-item');

        // ── Make entire row clickable ──
        memberItems.forEach(item => {
            item.addEventListener('click', function (e) {
                if (e.target.tagName === 'LABEL' || e.target.tagName === 'INPUT') return;
                const checkbox = item.querySelector('.member-checkbox');
                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change'));
            });
        });

        // ── Checkbox change ──
        memberCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const item = this.closest('.member-check-item');
                if (this.checked) {
                    item.classList.add('is-checked');
                } else {
                    item.classList.remove('is-checked');
                }
                updateHeadOptions();
                updateCounter();
            });
        });

        function updateCounter() {
            const count = Array.from(memberCheckboxes).filter(cb => cb.checked).length;
            memberCountEl.textContent = count;
        }

        function updateHeadOptions() {
            const currentVal = headSelect.value;

            // Clear all except default placeholder
            while (headSelect.options.length > 1) {
                headSelect.remove(1);
            }

            // Re-add options for checked members
            memberCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const item   = checkbox.closest('.member-check-item');
                    const name   = item.dataset.name;
                    const option = new Option(name, checkbox.value);
                    headSelect.add(option);
                }
            });

            // Restore previous selection if still available
            if (currentVal) {
                headSelect.value = currentVal;
            }
        }

    });
})();
(function () {
    const checkboxes       = document.querySelectorAll('.consti-checkbox');
    const submitButton     = document.getElementById('submit-btn');
    const searchInput      = document.getElementById('search-constituents');
    const constituentItems = document.querySelectorAll('.constituent-item');
    const selectedCount    = document.getElementById('selectedCount');
    const noResults        = document.getElementById('noResults');

    function updateCounter() {
        const count = Array.from(checkboxes).filter(cb => cb.checked).length;
        selectedCount.textContent = count;
        submitButton.disabled = count === 0;
    }

    // ── Checkbox change ──
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const item        = this.closest('.constituent-item');
            const roleSection = item.querySelector('.role-section');

            if (this.checked) {
                item.classList.add('is-checked');
                roleSection.style.display = 'block';
            } else {
                item.classList.remove('is-checked');
                roleSection.style.display = 'none';
                const radio = roleSection.querySelector('input[type="radio"]');
                if (radio) radio.checked = false;
            }

            updateCounter();
        });
    });

    // ── Make entire item clickable ──
    constituentItems.forEach(item => {
        item.addEventListener('click', function (e) {
            if (e.target.tagName === 'LABEL' || e.target.tagName === 'INPUT') return;
            const checkbox = item.querySelector('.consti-checkbox');
            checkbox.checked = !checkbox.checked;
            checkbox.dispatchEvent(new Event('change'));
        });
    });

    // ── Search ──
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase().trim();
            let visibleCount = 0;

            constituentItems.forEach(item => {
                const name    = item.dataset.name || '';
                const matches = name.includes(term);
                item.style.display = matches ? '' : 'none';
                if (matches) visibleCount++;
            });

            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        });
    }
})();
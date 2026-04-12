(function () {
    document.addEventListener('DOMContentLoaded', function () {

        // ── Delete modal ──
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id   = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                document.getElementById('officialName').textContent = name;
                document.getElementById('confirmDelete').href =
                    `index.php?controller=officials&action=delete&id=${id}`;
            });
        });

        // ── Search (main table) ──
        document.getElementById('search-input').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#officials-table-body tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        });

        // ── Search (modal constituents) ──
        document.getElementById('modal-search-input').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#constituents-table tr').forEach(row => {
                const name = row.querySelector('td')?.textContent.toLowerCase();
                if (name) row.style.display = name.includes(term) ? '' : 'none';
            });
        });

        // ── Select constituent ──
        document.querySelectorAll('.select-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id   = button.dataset.id;
                const name = button.dataset.name;

                if (document.getElementById(`selected-${id}`)) {
                    alert('This constituent has already been selected.');
                    return;
                }

                // Hide empty msg
                const emptyMsg = document.getElementById('empty-selection-msg');
                if (emptyMsg) emptyMsg.style.display = 'none';

                let roleOptions = '';
                if (!HAS_PUNONG_BARANGAY) {
                    roleOptions += '<option value="PUNONG BARANGAY">Punong Barangay</option>';
                }
                roleOptions += `
                    <option value="SECRETARY">Secretary</option>
                    <option value="TREASURER">Treasurer</option>
                    <option value="KONSEHAL" selected>Konsehal</option>
                `;

                const today = new Date().toISOString().split('T')[0];

                const card = document.createElement('div');
                card.id = `selected-${id}`;
                card.className = 'selected-card';
                card.innerHTML = `
                    <div class="selected-card-info">
                        <div class="selected-card-name">${name}</div>
                        <div class="selected-card-fields">
                            <label>
                                Role
                                <select name="constituents[${id}][role]" class="role-select" required>
                                    ${roleOptions}
                                </select>
                            </label>
                            <label>
                                Start Term
                                <input type="date" name="constituents[${id}][start_term]" required value="${today}">
                            </label>
                            <input type="hidden" name="constituents[${id}][id]" value="${id}">
                        </div>
                    </div>
                    <button type="button" class="btn-remove-card"
                        onclick="document.getElementById('selected-${id}').remove(); validateRoles(); checkEmpty();">
                        ✕
                    </button>
                `;
                document.getElementById('selected-constituents').appendChild(card);
                validateRoles();
            });
        });

        function checkEmpty() {
            const container = document.getElementById('selected-constituents');
            const emptyMsg  = document.getElementById('empty-selection-msg');
            if (!emptyMsg) return;
            const cards = container.querySelectorAll('.selected-card');
            emptyMsg.style.display = cards.length === 0 ? 'block' : 'none';
        }

        function validateRoles() {
            const roleCounts = { 'PUNONG BARANGAY': 0, SECRETARY: 0, TREASURER: 0, KONSEHAL: 0 };
            document.querySelectorAll('.role-select').forEach(select => {
                const role = select.value;
                if (role in roleCounts) roleCounts[role]++;
            });
            document.querySelectorAll('.role-select').forEach(select => {
                const currentRole = select.value;
                select.querySelectorAll('option').forEach(option => {
                    if (option.value in roleCounts) {
                        option.disabled = roleCounts[option.value] >= 1 && option.value !== currentRole;
                    }
                });
            });
        }

        document.addEventListener('change', event => {
            if (event.target.classList.contains('role-select')) validateRoles();
        });

        document.getElementById('appointBtn').addEventListener('click', function () {
            document.getElementById('appointForm').submit();
        });

        // ── Auto-dismiss alerts ──
        setTimeout(function () {
            document.querySelectorAll('#flash-success, #flash-error').forEach(msg => $(msg).alert('close'));
        }, 5000);

    });
})();
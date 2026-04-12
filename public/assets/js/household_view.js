(function () {
    document.addEventListener('DOMContentLoaded', function () {

        // ── Member search ──
        const memberSearch = document.getElementById('memberSearch');
        if (memberSearch) {
            memberSearch.addEventListener('input', function () {
                const term = this.value.toLowerCase().trim();
                document.querySelectorAll('#membersTable tbody tr:not(.empty-row)').forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
                });
            });
        }

        // ── Family search ──
        const familySearch = document.getElementById('familySearch');
        if (familySearch) {
            familySearch.addEventListener('input', function () {
                const term = this.value.toLowerCase().trim();
                document.querySelectorAll('#familiesTable tbody tr:not(.empty-row)').forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
                });
            });
        }

    });

    // ── Delete family (from outside modal) ──
    window.confirmDeleteFamily = function (familyId, householdId) {
        document.getElementById('confirmDeleteFamilyBtn').href =
            'index.php?controller=family&action=delete&family_id=' + familyId + '&household_id=' + householdId;
        $('#deleteFamilyModal').modal('show');
    };

    // ── Remove household member ──
    window.confirmRemoveMember = function (constituentId, householdId) {
        document.getElementById('confirmRemoveMemberBtn').href =
            'index.php?controller=households&action=removeMember&constituent_id=' + constituentId + '&household_id=' + householdId;
        $('#removeMemberModal').modal('show');
    };

    // ── Manage family members modal ──
    let _currentFamilyCtx = null;

    window.openManageFamilyModal = function (familyId, householdId, familyName, headConstituentId) {
        _currentFamilyCtx = { familyId, householdId, familyName, headConstituentId };

        document.getElementById('manageFamilyName').textContent = familyName;

        const listContainer = document.getElementById('manageFamilyMembersList');
        listContainer.innerHTML = '<p class="text-center text-muted">Loading members...</p>';

        $('#manageFamilyModal').modal('show');

        fetch('index.php?controller=family&action=getMembersJson&family_id=' + familyId)
            .then(response => response.json())
            .then(data => {
                if (!data.success || data.members.length === 0) {
                    listContainer.innerHTML = '<p class="text-center text-muted fst-italic">No members found.</p>';
                    return;
                }

                const headId = data.members[0].head_constituent_id;

                let html = '<ul class="list-group" style="padding:0;">';
                data.members.forEach(member => {
                    const isHead = headId !== null && parseInt(member.id) === parseInt(headId);

                    html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center"
                            style="border-radius:0.75rem;margin-bottom:0.5rem;padding:0.6rem 0.875rem;border:1.5px solid ${isHead ? 'rgba(67,97,238,0.3)' : '#e2e8f0'};background:${isHead ? '#f0f3ff' : 'white'};">
                            <div style="display:flex;align-items:center;gap:0.5rem;min-width:0;">
                                ${isHead ? '<span class="badge-head" style="flex-shrink:0;">Head</span>' : ''}
                                <span style="font-weight:600;color:#2d3748;font-size:0.875rem;">${member.full_name}</span>
                            </div>
                            <div class="modal-member-actions">
                                ${isHead
                                    ? `<span style="font-size:0.72rem;color:#a0aec0;font-style:italic;white-space:nowrap;">Current head</span>`
                                    : `
                                        <button type="button"
                                            onclick="confirmSetAsHead(${member.id}, ${familyId}, ${householdId}, '${member.full_name}')"
                                            class="btn-set-head">
                                            Set as Head
                                        </button>
                                        <a href="javascript:void(0)"
                                            onclick="confirmRemoveFromFamily(${member.id}, ${familyId}, ${householdId}, '${member.full_name}')"
                                            class="btn-delete" style="font-size:0.72rem;padding:0.2rem 0.6rem;border-radius:2rem;">
                                            Remove
                                        </a>
                                    `
                                }
                            </div>
                        </li>`;
                });
                html += '</ul>';
                listContainer.innerHTML = html;
            })
            .catch(() => {
                listContainer.innerHTML = '<p class="text-center text-danger">Failed to load members.</p>';
            });
    };

    // ── Set as head of family ──
    window.confirmSetAsHead = function (constituentId, familyId, householdId, memberName) {
        document.getElementById('newHeadName').textContent = memberName;
        document.getElementById('confirmSetHeadBtn').href =
            'index.php?controller=family&action=setFamilyHead' +
            '&constituent_id=' + constituentId +
            '&family_id=' + familyId +
            '&household_id=' + householdId;

        $('#manageFamilyModal').modal('hide');
        $('#manageFamilyModal').one('hidden.bs.modal', function () {
            $('#setHeadModal').modal('show');
        });
    };

    // ── Remove from family ──
    window.confirmRemoveFromFamily = function (constituentId, familyId, householdId, memberName) {
        document.getElementById('removeMemberName').textContent = memberName;
        document.getElementById('confirmRemoveFromFamilyBtn').href =
            'index.php?controller=family&action=removeMemberFromFamily' +
            '&constituent_id=' + constituentId +
            '&family_id=' + familyId +
            '&household_id=' + householdId;

        $('#manageFamilyModal').modal('hide');
        $('#manageFamilyModal').one('hidden.bs.modal', function () {
            $('#removeFromFamilyModal').modal('show');
        });
    };

    // ── Delete family from inside manage modal ──
    window.confirmDeleteFamilyFromModal = function () {
        if (!_currentFamilyCtx) return;
        const { familyId, householdId } = _currentFamilyCtx;

        document.getElementById('confirmDeleteFamilyBtn').href =
            'index.php?controller=family&action=delete&family_id=' + familyId + '&household_id=' + householdId;

        $('#manageFamilyModal').modal('hide');
        $('#manageFamilyModal').one('hidden.bs.modal', function () {
            $('#deleteFamilyModal').modal('show');
        });
    };

    // ── Set household head ──
    window.confirmSetHouseholdHead = function (constituentId, householdId, memberName) {
        document.getElementById('newHouseholdHeadName').textContent = memberName;
        document.getElementById('confirmSetHouseholdHeadBtn').href =
            'index.php?controller=households&action=setHouseholdHead&constituent_id=' + constituentId + '&household_id=' + householdId;
        $('#setHouseholdHeadModal').modal('show');
    };

})();
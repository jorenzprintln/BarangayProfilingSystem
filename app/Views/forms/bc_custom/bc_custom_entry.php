<?php
ob_start();
?>

<style>
    :root {
        --primary:      #4361ee;
        --primary-dark: #3651d4;
        --danger:       #ef476f;
        --success:      #06d6a0;
        --shadow-sm:    0 0.125rem 0.5rem rgba(0,0,0,0.07);
        --shadow-blue:  0 4px 20px rgba(67,97,238,0.22);
    }

    /* ── Back Button (above header) ── */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 1rem;
        background: white;
        border: 1.5px solid #e2e8f0;
        border-radius: 0.5rem;
        color: #4a5568;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.07);
        margin-bottom: 1rem;
        font-family: 'Montserrat', sans-serif;
        line-height: 1;
    }

    .back-link:hover {
        background: #2d3748;
        color: white;
        border-color: #2d3748;
        text-decoration: none;
        transform: translateX(-2px);
    }

    /* ── Page Header ── */
    .page-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 1.75rem 2rem;
        margin: 0 0 1.75rem 0;
        border-radius: 0.75rem;
        box-shadow: var(--shadow-blue);
        animation: fadeUp .4s ease .05s both;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .page-title-icon {
        width: 46px; height: 46px;
        background: rgba(255,255,255,0.18);
        border-radius: 0.6rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .page-title-icon svg { width: 22px; height: 22px; }

    .page-header h3 {
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 0.15rem;
        letter-spacing: -0.01em;
    }

    .page-header p {
        opacity: 0.82;
        font-size: 0.82rem;
        margin: 0;
    }

    /* ── Form Card ── */
    .form-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        border: 1px solid #edf0f7;
        animation: fadeUp .4s ease .12s both;
    }

    .form-card-header {
        padding: 0.9rem 1.5rem;
        border-bottom: 1px solid #edf0f7;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        background: #fafbfc;
    }

    .form-card-header-label {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
    }

    .form-card-body { padding: 1.75rem 1.75rem 2rem; }

    /* ── Field Group ── */
    .field-group { margin-bottom: 1.35rem; }
    .field-group:last-of-type { margin-bottom: 0; }

    .field-group label {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.45rem;
    }

    .field-group label .req {
        color: var(--danger);
        font-size: 0.85rem;
        line-height: 1;
    }

    .field-group .form-control {
        border: 1.5px solid #e5e9f0;
        border-radius: 0.5rem;
        padding: 0.65rem 0.9rem;
        font-size: 0.875rem;
        height: auto;
        line-height: 1.5;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        font-family: 'Montserrat', sans-serif;
        background: #f9fafb;
        width: 100%;
        color: #1e293b;
    }

    .field-group .form-control:focus {
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 0.2rem rgba(67,97,238,0.13);
        outline: none;
    }

    .field-group .form-control::placeholder { color: #b0bac9; }
    .field-group textarea.form-control { resize: vertical; min-height: 120px; }

    .field-hint {
        font-size: 0.74rem;
        color: #94a3b8;
        margin-top: 0.3rem;
        display: flex;
        align-items: flex-start;
        gap: 0.3rem;
        line-height: 1.45;
    }

    /* ── Card Footer ── */
    .form-card-footer {
        padding: 1rem 1.75rem;
        border-top: 1px solid #edf0f7;
        background: #fafbfc;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    /* ── Generate Button ── */
    .btn-generate {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        padding: 0.6rem 1.75rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 4px 12px rgba(67,97,238,0.3);
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-family: 'Montserrat', sans-serif;
    }

    .btn-generate:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(67,97,238,0.4);
        color: white;
        text-decoration: none;
    }

    .btn-generate:active { transform: translateY(0); }

    /* ── Animations ── */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        .page-header { padding: 1.25rem; }
        .form-card-body { padding: 1.25rem; }
        .btn-generate { width: 100%; justify-content: center; }
    }
</style>

<div class="container-fluid px-4 mt-3">

    <!-- Back Button -->
    <a href="index.php?controller=home&action=forms" class="back-link">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        Back to Forms
    </a>

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title-icon">
            <svg fill="white" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <h3>Barangay Certificate (Custom)</h3>
            <p>Fill in the details below to generate the certificate</p>
        </div>
    </div>

    <form action="index.php?controller=forms&action=processBcCustomEntry" target="_blank" method="POST">
        <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">

        <div class="form-card">
            <div class="form-card-header">
                <svg width="14" height="14" fill="#4361ee" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span class="form-card-header-label">Transaction Details</span>
            </div>

            <div class="form-card-body">
                <div class="field-group">
                    <label for="requesting_party">
                        Requesting Party
                        <span class="req">*</span>
                    </label>
                    <input type="text" class="form-control" id="requesting_party" name="requesting_party"
                        placeholder="Enter full name of requesting party" required>
                    <div class="field-hint">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;margin-top:1px">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Full legal name of the person requesting this certificate
                    </div>
                </div>

                <div class="field-group">
                    <label for="purpose">
                        Purpose
                        <span class="req">*</span>
                    </label>
                    <textarea class="form-control" id="purpose" name="purpose" rows="5"
                        placeholder="e.g. For employment purposes, loan application, school requirements..." required></textarea>
                    <div class="field-hint">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;margin-top:1px">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Briefly describe why the certificate is being requested
                    </div>
                </div>
            </div>

            <div class="form-card-footer">
                <button type="submit" class="btn-generate">
                    <svg width="15" height="15" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
                    </svg>
                    Generate Certificate
                </button>
            </div>

        </div>

    </form>
</div>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
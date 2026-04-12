<?php
$content = ob_start();
$documentTypeKey = $documentTypeKey ?? '';
$documentTypeLabel = $documentTypeLabel ?? 'Document';
$oldPurpose = $oldPurpose ?? '';
$errors = $errors ?? [];
?>

<link rel="stylesheet" href="public/assets/css/bc_general_entry.css">

<div class="container-fluid px-4 mt-3">

    <a href="index.php?controller=constituent&action=requestDocument" class="back-link">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        Back to Document Selection
    </a>

    <div class="page-header">
        <div class="page-title-icon">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <h3><?= htmlspecialchars($documentTypeLabel) ?></h3>
            <p>Provide the purpose for this document request</p>
        </div>
    </div>

    <form action="index.php?controller=constituent&action=submitDocumentRequest" method="POST">
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
                    <label for="selected_document_type">Selected Document</label>
                    <input type="text" id="selected_document_type" class="form-control" value="<?= htmlspecialchars($documentTypeLabel) ?>" readonly>
                    <input type="hidden" name="document_type" value="<?= htmlspecialchars($documentTypeKey) ?>">
                </div>

                <div class="field-group">
                    <label for="purpose">
                        Purpose of Requesting
                        <span class="req">*</span>
                    </label>
                    <textarea
                        class="form-control <?= isset($errors['purpose']) ? 'is-invalid' : '' ?>"
                        id="purpose"
                        name="purpose"
                        rows="5"
                        placeholder="e.g. For employment purposes, loan application, school requirements..."
                        required><?= htmlspecialchars($oldPurpose) ?></textarea>
                    <?php if (isset($errors['purpose'])): ?>
                        <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['purpose']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-card-footer">
                <button type="submit" class="btn-generate">
                    <svg width="15" height="15" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
                    </svg>
                    Submit Document Request
                </button>
            </div>

        </div>

    </form>
</div>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>

<?php
$content = ob_start();
$documentTypes = $documentTypes ?? [];

$excludeTypes = ['bc_business', 'co_solo_parent', 'bc_ofw'];

$documentCardStyle = [
    'bc_general'      => ['icon' => 'fa-file-alt',          'color' => 'blue'],
    'co_indigency'    => ['icon' => 'fa-hand-holding-heart', 'color' => 'orange'],
    'bc_good_moral'   => ['icon' => 'fa-check-circle',       'color' => 'green'],
    'bc_ofw'          => ['icon' => 'fa-globe-asia',         'color' => 'sky'],
    'bc_unemployment' => ['icon' => 'fa-user-clock',         'color' => 'rose'],
];
?>

<link rel="stylesheet" href="public/assets/css/constituent_request_document.css?v=<?= time() ?>">

<div class="container-fluid px-4 mt-3 request-doc-page">
    <?php if (Session::hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> <?= Session::getFlash('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (Session::hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> <?= Session::getFlash('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="request-doc-header">
        <div class="request-doc-header-inner">
            <div class="request-doc-icon"><i class="fas fa-file-signature"></i></div>
            <div>
                <h3>Request Document</h3>
                <p>Select a document type to continue.</p>
            </div>
        </div>
    </div>

    <div class="request-doc-panel">
        <div class="form-group mb-2">
            <label class="font-weight-bold mb-2">Document Type <span class="text-danger">*</span></label>
            <div class="forms-grid request-forms-grid" id="requestFormsGrid">
                <?php foreach ($documentTypes as $key => $label): ?>
                    <?php if (in_array($key, $excludeTypes)) continue; ?>
                    <?php $cardStyle = $documentCardStyle[$key] ?? ['icon' => 'fa-file-alt', 'color' => 'blue']; ?>
                    
                        <a href="index.php?controller=constituent&action=requestDocumentPurpose&type=<?= urlencode($key) ?>"
                        class="form-card request-form-card"
                        aria-label="Select <?= htmlspecialchars($label) ?>">
                        <div class="form-card-icon <?= htmlspecialchars($cardStyle['color']) ?>">
                            <i class="fas <?= htmlspecialchars($cardStyle['icon']) ?>"></i>
                        </div>
                        <span class="form-card-label"><?= htmlspecialchars($label) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Removed View My Document Requests button as requested -->
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>

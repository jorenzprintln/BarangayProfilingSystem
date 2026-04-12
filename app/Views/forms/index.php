<?php
$content = ob_start();
?>

<link rel="stylesheet" href="public/assets/css/forms_index.css">

<div class="container-fluid px-4 mt-3">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <svg fill="white" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="font-weight-bold mb-0">Forms</h3>
                <p class="mb-0 mt-1" style="opacity: 0.9; font-size: 0.9rem;">Select a form to generate</p>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="controls-container">
        <div class="search-wrapper">
            <svg class="search-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
            </svg>
            <input type="text" id="searchBar" placeholder="Search forms..." onkeyup="filterCards()">
        </div>
    </div>

    <!-- Forms Grid -->
    <div id="formsGrid" class="forms-grid">

        <a href="index.php?controller=forms&action=bcCustomEntry" class="form-card searchable-card">
            <div class="form-card-icon green">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="form-card-label">Barangay Certificate (Custom)</span>
        </a>

        <a href="index.php?controller=home&action=rbiASelectHousehold" class="form-card searchable-card">
            <div class="form-card-icon blue">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
            </div>
            <span class="form-card-label">RBI Form A (Household)</span>
        </a>

        <a href="index.php?controller=home&action=rbiBSelectConstituent" class="form-card searchable-card">
            <div class="form-card-icon blue">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="form-card-label">RBI Form B (Individual)</span>
        </a>

        <a href="index.php?controller=forms&action=bcGeneralEntry" class="form-card searchable-card">
            <div class="form-card-icon blue">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="form-card-label">Barangay Certificate</span>
        </a>

        <a href="index.php?controller=forms&action=firEntry" class="form-card searchable-card">
            <div class="form-card-icon violet">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
            </div>
            <span class="form-card-label">Family Information Record</span>
        </a>

        <a href="index.php?controller=forms&action=coaEntry" class="form-card searchable-card">
            <div class="form-card-icon teal">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="form-card-label">Certificate of Appearance</span>
        </a>

        <a href="index.php?controller=forms&action=bcbEntry" class="form-card searchable-card">
            <div class="form-card-icon amber">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="form-card-label">Barangay Certificate (Business)</span>
        </a>

        <a href="index.php?controller=forms&action=bcOfwEntry" class="form-card searchable-card">
            <div class="form-card-icon sky">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="form-card-label">Barangay Certificate (OFW)</span>
        </a>

        <a href="index.php?controller=forms&action=bcGoodMoralEntry" class="form-card searchable-card">
            <div class="form-card-icon green">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="form-card-label">Certificate of Good Moral Character</span>
        </a>

        <a href="index.php?controller=forms&action=bcUnemploymentEntry" class="form-card searchable-card">
            <div class="form-card-icon rose">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="form-card-label">Certificate of Unemployment</span>
        </a>

        <a href="index.php?controller=forms&action=coIndigencyEntry" class="form-card searchable-card">
            <div class="form-card-icon orange">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="form-card-label">Certificate of Indigency</span>
        </a>

        <a href="index.php?controller=forms&action=coSoloParentEntry" class="form-card searchable-card">
            <div class="form-card-icon violet">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 4a3 3 0 100 6 3 3 0 000-6zm0 8a7 7 0 00-7 7v1h14v-1a7 7 0 00-7-7zm8-5a2 2 0 100 4 2 2 0 000-4zm0 5a4 4 0 013.999 3.8L21 16v1h-4v-1a6.978 6.978 0 00-1.4-4.2A4 4 0 0117 12z"/>
                </svg>
            </div>
            <span class="form-card-label">Certificate of Solo Parent</span>
        </a>

        <!-- No results message -->
        <div class="no-results" id="noResults">
            <svg fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
            </svg>
            <h6>No forms found</h6>
            <p style="font-size:0.85rem;">Try a different search term</p>
        </div>

    </div>
</div>

<script src="public/assets/js/forms_index.js"></script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
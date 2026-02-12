<?php
$content = ob_start();
?>

<div class="container-fluid px-4 mt-4">
    <h2 class="font-weight-bold">Select a form to generate</h2>

    <!-- Search bar -->
    <input type="text" id="searchBar" class="form-control mb-3 p-3" placeholder="Search form..."
        onkeyup="filterButtons()">

    <div id="buttonGrid" class="row g-3">
        <div class="col-6 col-md-3">
            <a href="index.php?controller=forms&action=bcCustomEntry"
                class="btn btn-success w-100 searchable-button p-3">Barangay Certificate (Custom)</a>
        </div>
        <div class="col-6 col-md-3">
            <a href="index.php?controller=home&action=rbiASelectHousehold"
                class="btn btn-primary w-100 searchable-button p-3">RBI Form A (Household)</a>
        </div>
        <div class="col-6 col-md-3">
            <a href="index.php?controller=home&action=rbiBSelectConstituent"
                class="btn btn-primary w-100 searchable-button p-3">RBI Form B (Individual)</a>
        </div>
        <div class="col-6 col-md-3">
            <a href="index.php?controller=forms&action=bcGeneralEntry"
                class="btn btn-primary w-100 searchable-button p-3">Barangay Certificate</a>
        </div>
        <div class="col-6 col-md-3">
            <a href="index.php?controller=forms&action=firEntry"
                class="btn btn-primary w-100 searchable-button p-3">Family Information Record</a>
        </div>
        <div class="col-6 col-md-3">
            <a href="index.php?controller=forms&action=coaEntry"
                class="btn btn-primary w-100 searchable-button p-3">Certificate of Appearance</a>
        </div>
        <div class="col-6 col-md-3">
            <a href="index.php?controller=forms&action=bcbEntry"
                class="btn btn-primary w-100 searchable-button p-3">Barangay Certificate (Business)</a>
        </div>
        <div class="col-6 col-md-3">
            <a href="index.php?controller=forms&action=bcOfwEntry"
                class="btn btn-primary w-100 searchable-button p-3">Barangay Certificate (OFW)</a>
        </div>
        <div class="col-6 col-md-3">
            <a href="index.php?controller=forms&action=bcGoodMoralEntry"
                class="btn btn-primary w-100 searchable-button p-3">Certificate of Good Moral Character</a>
        </div>
        <div class="col-6 col-md-3">
            <a href="index.php?controller=forms&action=bcUnemploymentEntry"
                class="btn btn-primary w-100 searchable-button p-3">Certificate of Unemployment</a>
        </div>
        <div class="col-6 col-md-3">
            <a href="index.php?controller=forms&action=coIndigencyEntry"
                class="btn btn-primary w-100 searchable-button p-3">Certificate of Indigency</a>
        </div>
        <div class="col-6 col-md-3">
            <a href="index.php?controller=forms&action=coSoloParentEntry"
                class="btn btn-primary w-100 searchable-button p-3">Certificate of Solo Parent</a>
        </div>
    </div>
</div>

<style>
    .searchable-button {
        white-space: normal;
        min-height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        height: 100%;
        transition: all 0.2s;
    }

    .col-6 {
        display: flex;
        margin-bottom: 15px;
    }

    .col-6>a,
    .col-6>button {
        flex: 1;
    }
</style>

<script>
    function filterButtons() {
        const searchValue = document.getElementById('searchBar').value.toLowerCase();
        const buttons = document.querySelectorAll('.searchable-button');
        buttons.forEach(button => {
            const text = button.textContent.toLowerCase();
            button.closest('.col-6').style.display = text.includes(searchValue) ? '' : 'none';
        });
    }
</script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
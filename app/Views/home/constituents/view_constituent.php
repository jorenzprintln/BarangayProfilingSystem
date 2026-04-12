<?php
$content = ob_start();

/* ── Helpers ── */
$firstName  = $constituent['first_name']  ?? '';
$middleName = $constituent['middle_name'] ?? '';
$lastName   = $constituent['last_name']   ?? '';
$suffix     = $constituent['suffix']      ?? '';
$fullName   = trim("$firstName $middleName $lastName" . ($suffix ? " $suffix" : ''));
$initials   = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));

$educationMap = [
    '1'  => 'Daycare',
    '2'  => 'Nursery',
    '3'  => 'Kindergarten',
    '4'  => 'Elementary',
    '5'  => 'ALS',
    '6'  => 'High School',
    '7'  => 'Junior High School',
    '8'  => 'Senior High School',
    '9'  => 'Vocational',
    '10' => 'College',
    '11' => 'Post Graduate',
];
$education = $educationMap[$constituent['education_attainment'] ?? ''] ?? '—';

function displayVal($v, $fallback = '—') {
    $str = trim((string)$v);
    return $str !== '' ? htmlspecialchars($str) : $fallback;
}

$isVoter = ($constituent['registered_voter'] ?? '') === 'YES';
$isGrad  = ($constituent['is_graduate']      ?? '') === 'YES';
?>

<link rel="stylesheet" href="public/assets/css/constituent_view.css">


<div class="container-fluid px-4 mt-3">
<div class="content-wrapper">

    <!-- Action Bar -->
    <div class="action-bar">
        <a href="index.php?controller=constituents&action=index" class="btn-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Back to List
        </a>
        <div class="action-group">
            <a href="index.php?controller=forms&action=rbi_form_B&id=<?= $constituent['id'] ?>" class="btn-action-top generate" target="_blank">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Generate RBI Form B
            </a>
        </div>
    </div>

    <!-- Profile Header -->
    <div class="page-header">
        <div class="page-header-inner">
            <div class="avatar-circle"><?= $initials ?></div>
            <div class="header-text">
                <h2><?= htmlspecialchars($fullName) ?></h2>
                <p class="header-sub">
                    <?= displayVal($constituent['sex']) ?> &nbsp;·&nbsp;
                    <?= displayVal($constituent['birthdate']) ?> &nbsp;·&nbsp;
                    <?= displayVal($constituent['citizenship']) ?>
                </p>
                <div class="header-badges">
                    <span class="header-badge <?= $isVoter ? 'voter-yes' : 'voter-no' ?>">
                        <?= $isVoter ? 'Registered Voter' : 'Not a Voter' ?>
                    </span>
                    <?php if (!empty($classifications)): ?>
                        <?php foreach ($classifications as $cl): ?>
                            <span class="header-badge class-tag"><?= htmlspecialchars($cl['code'] ?? '') ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Information -->
    <div class="info-card">
        <div class="info-card-header">
            <div class="info-card-header-left">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                <h5>Personal Information</h5>
            </div>
        </div>
        <div class="info-card-body">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">PhilSys No. (PSN)</span>
                    <span class="info-value <?= empty($constituent['psn']) ? 'empty' : '' ?>">
                        <?= !empty($constituent['psn']) ? displayVal($constituent['psn']) : 'Not provided' ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Civil Status</span>
                    <span class="info-value"><?= displayVal($constituent['civil_status']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Religion</span>
                    <span class="info-value"><?= displayVal($constituent['religion']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Birth Place</span>
                    <span class="info-value"><?= displayVal($constituent['birthplace']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Citizenship</span>
                    <span class="info-value"><?= displayVal($constituent['citizenship']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Registered Voter</span>
                    <span class="info-value">
                        <span class="badge-modern <?= $isVoter ? 'badge-yes' : 'badge-no' ?>">
                            <?= $isVoter ? 'YES' : 'NO' ?>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact & Employment -->
    <div class="info-card">
        <div class="info-card-header">
            <div class="info-card-header-left">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                </svg>
                <h5>Contact & Employment</h5>
            </div>
        </div>
        <div class="info-card-body">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Contact Number</span>
                    <span class="info-value <?= empty($constituent['contact']) ? 'empty' : '' ?>">
                        <?= !empty($constituent['contact']) ? displayVal($constituent['contact']) : 'Not provided' ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email Address</span>
                    <span class="info-value <?= empty($constituent['email']) ? 'empty' : '' ?>">
                        <?= !empty($constituent['email']) ? htmlspecialchars(strtolower($constituent['email'])) : 'Not provided' ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Occupation</span>
                    <span class="info-value <?= empty($constituent['occupation']) ? 'empty' : '' ?>">
                        <?= !empty($constituent['occupation']) ? displayVal($constituent['occupation']) : 'Not specified' ?>
                    </span>
                </div>
            </div>

            <hr class="info-divider">

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Educational Attainment</span>
                    <span class="info-value"><?= htmlspecialchars($education) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Graduate Status</span>
                    <span class="info-value">
                        <span class="badge-modern <?= $isGrad ? 'badge-yes' : 'badge-no' ?>">
                            <?= $isGrad ? 'Graduate' : 'Not a Graduate' ?>
                        </span>
                    </span>
                </div>
                <?php if (!empty($classifications)): ?>
                <div class="info-item">
                    <span class="info-label">Classifications</span>
                    <span class="info-value">
                        <div class="class-tags">
                            <?php foreach ($classifications as $cl): ?>
                                <span class="class-tag"><?= htmlspecialchars($cl['code'] ?? '') ?></span>
                            <?php endforeach; ?>
                        </div>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Household Information -->
    <div class="info-card">
        <div class="info-card-header">
            <div class="info-card-header-left">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                <h5>Household Information</h5>
            </div>
        </div>
        <div class="info-card-body">

            <?php if (!empty($constituentHousehold['household_number'])): ?>
                <div class="household-number">
                    <span class="hn-label">Household No.</span>
                    <span class="hn-value"><?= htmlspecialchars($constituentHousehold['household_number']) ?></span>
                </div>
            <?php else: ?>
                <div class="not-assigned">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/>
                    </svg>
                    No household assigned
                </div>
            <?php endif; ?>

            <?php
            $addressFields = [
                'Region'           => $constituentHousehold['region']            ?? '',
                'Province'         => $constituentHousehold['province']          ?? '',
                'City / Municipal' => $constituentHousehold['city_municipality'] ?? '',
                'Barangay'         => $constituentHousehold['barangay_name']     ?? '',
                'Street'           => $constituentHousehold['street_name']       ?? '',
                'Purok'            => $constituentHousehold['purok']             ?? '',
                'Block'            => $constituentHousehold['block_number']      ?? '',
                'Lot'              => $constituentHousehold['lot_number']        ?? '',
                'House'            => $constituentHousehold['house_number']      ?? '',
                'Unit'             => $constituentHousehold['unit']              ?? '',
                'Zip Code'         => $constituentHousehold['zip_code']          ?? '',
            ];
            ?>
            <div class="address-grid">
                <?php foreach ($addressFields as $label => $value):
                    $isEmpty = trim((string)$value) === '';
                ?>
                <div class="address-tile">
                    <div class="tile-label"><?= $label ?></div>
                    <div class="tile-value <?= $isEmpty ? 'empty' : '' ?>">
                        <?= $isEmpty ? 'Not specified' : htmlspecialchars($value) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

    <!-- Registered Vehicles -->
    <div class="info-card">
        <div class="info-card-header">
            <div class="info-card-header-left">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                    <circle cx="7.5" cy="14.5" r="1.5"/>
                    <circle cx="16.5" cy="14.5" r="1.5"/>
                </svg>
                <h5>Registered Vehicles</h5>
                <?php if (!empty($vehicles)): ?>
                    <span class="vehicles-count-badge"><?= count($vehicles) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="info-card-body" style="padding:0;">

            <?php if (!empty($vehicles)): ?>
                <div class="veh-table-wrap">
                    <table class="veh-table">
                        <thead>
                            <tr>
                                <th>Plate No.</th>
                                <th>Make / Model</th>
                                <th>Type</th>
                                <th class="hide-xs">Year</th>
                                <th class="hide-sm">Color</th>
                                <th class="hide-sm">Fuel</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vehicles as $v): ?>
                                <tr>
                                    <td>
                                        <span class="veh-plate">
                                            <?= htmlspecialchars($v['plate_number'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="veh-makemodel">
                                            <span class="veh-make"><?= htmlspecialchars($v['make'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php if (!empty($v['model'])): ?>
                                                <span class="veh-model"><?= htmlspecialchars($v['model'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="veh-type-col">
                                            <span class="veh-type-badge">
                                                <?= htmlspecialchars($v['vehicle_type'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <span class="veh-use-badge veh-use-<?= strtolower($v['vehicle_use'] ?? 'private') ?>">
                                                <?= ($v['vehicle_use'] ?? 'Private') === 'Public' ? 'Public' : 'Private' ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="hide-xs">
                                        <span class="veh-year"><?= htmlspecialchars($v['year'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td class="hide-sm">
                                        <div class="veh-color-cell">
                                            <span class="veh-color-dot" style="background:<?= htmlspecialchars($v['color_hex'] ?? '#9ca3af', ENT_QUOTES, 'UTF-8') ?>;"></span>
                                            <span class="veh-color-name"><?= htmlspecialchars($v['color'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    </td>
                                    <td class="hide-sm">
                                        <span class="veh-fuel"><?= htmlspecialchars($v['fuel_type'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td>
                                        <div class="veh-actions">
                                            <a href="index.php?controller=vehicles&action=view&id=<?= (int)$v['id'] ?>"
                                                class="veh-btn veh-btn-view" title="View">
                                                <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="veh-btn-label">View</span>
                                            </a>
                                            <a href="index.php?controller=vehicles&action=edit&id=<?= (int)$v['id'] ?>"
                                                class="veh-btn veh-btn-edit" title="Edit">
                                                <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                                </svg>
                                                <span class="veh-btn-label">Edit</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>
                <div class="veh-empty">
                    <div class="veh-empty-icon">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 24 24" style="opacity:.35;">
                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                            <circle cx="7.5" cy="14.5" r="1.5"/>
                            <circle cx="16.5" cy="14.5" r="1.5"/>
                        </svg>
                    </div>
                    <p class="veh-empty-title">No vehicles registered</p>
                    <p class="veh-empty-sub">This constituent has no registered vehicles yet.</p>
                    <a href="index.php?controller=vehicles&action=add&owner_id=<?= (int)$constituent['id'] ?>" class="btn-action-top generate" style="font-size:.78rem;padding:.45rem .9rem;margin-top:.5rem;">
                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        Register First Vehicle
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>
</div>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
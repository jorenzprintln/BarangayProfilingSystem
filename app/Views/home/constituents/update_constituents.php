<?php
// views/home/add_constituents.php - Add Constituents page
$content = ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Page to Update Constituents</h1>
</body>
</html>
<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>
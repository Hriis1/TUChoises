<?php
require_once __DIR__ . '/../config/dbConfig.php';
require_once __DIR__ . '/../config/sessionConfig.php';
require_once __DIR__ . '/../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// 1) Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 0;
    exit;
}

// 2) Check upload
if (!isset($_FILES['fileUpload']) || $_FILES['fileUpload']['error'] !== UPLOAD_ERR_OK) {
    echo 0;
    $_SESSION['alert'] = [
        "type" => "danger",
        "text" => "Error with uploading file"
    ];
    exit;
}

// 3) Validate extension
$allowedExt = ['xls', 'xlsx'];
$filename = $_FILES['fileUpload']['name'];
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExt, true)) {
    echo 0;
    $_SESSION['alert'] = [
        "type" => "danger",
        "text" => "You can only upload excel files"
    ];
    exit;
}

try {
    $spreadsheet = IOFactory::load($_FILES['fileUpload']['tmp_name']);
} catch (\Exception $e) {
    // failed to read file
    error_log("Failed to load spreadsheet: " . $e->getMessage());
    $_SESSION['alert'] = [
        "type" => "danger",
        "text" => "Failed to read the file"
    ];
    echo 0;
    exit;
}

$sheet = $spreadsheet->getActiveSheet();
$highestRow = $sheet->getHighestDataRow();
$highestColumn = $sheet->getHighestDataColumn();
$colCount = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

// 5) Validate exactly 2 columns
if ($colCount !== 2) {
    echo 0;
    $_SESSION['alert'] = [
        "type" => "danger",
        "text" => "File was not formatted right"
    ];
    exit;
}

// 6) Validate header row
$h1 = trim((string) $sheet->getCell('A1')->getValue());
$h2 = trim((string) $sheet->getCell('B1')->getValue());
if (strtolower($h1) !== 'name' || strtolower($h2) !== 'short') {
    echo 0;
    $_SESSION['alert'] = [
        "type" => "danger",
        "text" => "File was not formatted right"
    ];
    exit;
}

// Prepare statements
// Check short exists
$chkStmt = $mysqli->prepare("SELECT COUNT(*) FROM faculties WHERE short = ?");
$insStmt = $mysqli->prepare("INSERT INTO faculties (`name`,`short`) VALUES (?,?)");

if (!$chkStmt || !$insStmt) {
    $_SESSION['alert'] = [
        "type" => "danger",
        "text" => "Mysql error"
    ];
    echo 0;
    exit;
}

//Start a transaction
$mysqli->begin_transaction();

$attempted = 0;
$inserted = 0;

// 8) Loop rows
for ($row = 2; $row <= $highestRow; $row++) {
    $name = trim((string) $sheet->getCell("A{$row}")->getValue());
    $short = trim((string) $sheet->getCell("B{$row}")->getValue());

    $attempted++;

    // Skip blank rows
    if ($name === '' && $short === '') {
        continue;
    }

    // Validate non-empty and length ≤100
    if ($name === '' || $short === '' || mb_strlen($name) > 100 || mb_strlen($short) > 100) {
        continue;
    }

    // Check uniqueness of short
    $chkStmt->bind_param('s', $short);
    $chkStmt->execute();
    $chkStmt->bind_result($count);
    $chkStmt->fetch();
    $chkStmt->free_result();

    if ($count > 0) {
        // already exists → skip
        continue;
    }

    // Insert
    $insStmt->bind_param('ss', $name, $short);
    if ($insStmt->execute()) {
        $inserted++;
    }
}

// 9) Clean up
$mysqli->commit();
$chkStmt->close();
$insStmt->close();

// 10) Success
echo 1;
$_SESSION['alert'] = [
    'type' => 'success',
    'text' => "Inserted {$inserted} out of {$attempted} faculties."
];
exit;

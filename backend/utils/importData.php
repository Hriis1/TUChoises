<?php
require_once __DIR__ . '/../config/dbConfig.php';
require_once __DIR__ . '/../config/sessionConfig.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
function importExcel(
    mysqli $mysqli,
    string $filePath,
    string $table,
    array $fields,
    array $uniqueIndices,
    string $types,
    string $deletedFlagCol = 'deleted'
) {
    try {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestDataRow();
        $highestCol = $sheet->getHighestDataColumn();
        $colCount = Coordinate::columnIndexFromString($highestCol);

        if ($colCount !== count($fields)) {
            return [0, "File structure error! Expected " . count($fields) . " columns, got {$colCount}"];
        }
        for ($i = 0; $i < count($fields); $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($i + 1);
            $h = trim((string) $sheet->getCell("{$colLetter}1")->getValue());
            if (strtolower($h) !== strtolower($fields[$i])) {
                return [0, "File structure error! Header mismatch in column {$colLetter}: expected '{$fields[$i]}', got '{$h}'"];
            }
        }

        // prepare unique-check statements
        $uniqueStmts = [];
        foreach ($uniqueIndices as $key => $idx) {
            if (is_array($idx)) {
                $cols = array_map(fn($i) => "`{$fields[$i]}` = ?", $idx);
                $sql = "SELECT COUNT(*) FROM `{$table}` WHERE " . implode(' AND ', $cols) . " AND `{$deletedFlagCol}` = 0";
                $stmt = $mysqli->prepare($sql);
                if (!$stmt)
                    return [0, "MySQL Error 1"];
                $uniqueStmts[$key] = ['stmt' => $stmt, 'indices' => $idx];
            } else {
                $field = $fields[$idx];
                $stmt = $mysqli->prepare(
                    "SELECT COUNT(*) FROM `{$table}` WHERE `{$field}` = ? AND `{$deletedFlagCol}` = 0"
                );
                if (!$stmt)
                    return [0, "MySQL Error 1"];
                $uniqueStmts[$key] = ['stmt' => $stmt, 'indices' => [$idx]];
            }
        }

        // prepare insert
        $cols = implode('`,`', $fields);
        $placeholders = implode(',', array_fill(0, count($fields), '?'));
        $insertSql = "INSERT INTO `{$table}` (`{$cols}`) VALUES ({$placeholders})";
        $insertStmt = $mysqli->prepare($insertSql);
        if (!$insertStmt)
            return [0, "MySQL Error 2"];

        $mysqli->begin_transaction();
        $attempted = $inserted = 0;

        for ($row = 2; $row <= $highestRow; $row++) {
            $values = [];
            $allEmpty = true;
            for ($i = 0; $i < count($fields); $i++) {
                $colLetter = Coordinate::stringFromColumnIndex($i + 1);
                $val = trim((string) $sheet->getCell("{$colLetter}{$row}")->getValue());
                if ($val !== '')
                    $allEmpty = false;
                $values[] = $val;
            }
            if ($allEmpty)
                continue;
            $attempted++;

            // uniqueness
            foreach ($uniqueStmts as ['stmt' => $stmt, 'indices' => $inds]) {
                $params = array_map(fn($i) => $values[$i], $inds);
                $stmt->bind_param(str_repeat('s', count($params)), ...$params);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->free_result();
                if ($count > 0)
                    continue 2;
            }

            // bind & execute
            $typesStr = '';
            $params = [];
            for ($i = 0; $i < count($fields); $i++) {
                $t = $types[$i] ?? 's';
                $typesStr .= $t;
                switch ($t) {
                    case 'i':
                        $params[] = (int) $values[$i];
                        break;
                    case 'd':
                        $params[] = (float) $values[$i];
                        break;
                    default:
                        $params[] = $values[$i];
                }
            }
            $bind = array_merge([$typesStr], $params);
            $refs = [];
            foreach ($bind as $k => &$v) {
                $refs[$k] = &$bind[$k];
            }
            call_user_func_array([$insertStmt, 'bind_param'], $refs);

            if ($insertStmt->execute()) {
                $inserted++;
            }

        }

        $mysqli->commit();
        foreach ($uniqueStmts as $u) {
            $u['stmt']->close();
        }
        $insertStmt->close();

        return [1, "Inserted {$inserted} out of {$attempted} {$table}."];
    } catch (\Exception $e) {
        return [0, $e->getMessage()];
    }
}


// ---- Handler ----

// 1) Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 0;
    exit;
}

// 2) Check upload
if (!isset($_FILES['fileUpload']) || $_FILES['fileUpload']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['alert'] = ["type" => "danger", "text" => "Error with uploading file"];
    echo 0;
    exit;
}

// 3) Validate extension
$allowedExt = ['xls', 'xlsx'];
$ext = strtolower(pathinfo($_FILES['fileUpload']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExt, true)) {
    $_SESSION['alert'] = ["type" => "danger", "text" => "You can only upload excel files"];
    echo 0;
    exit;
}

// 4) Call importer
$res = null;
if ($_POST["action"] == "importFaculties") {
    $res = importExcel(
        $mysqli,
        $_FILES['fileUpload']['tmp_name'],
        'faculties',
        ['name', 'short'],
        [1],
        'ss'
    );
} else if ($_POST["action"] == "importMajors") {
    $res = importExcel(
        $mysqli,
        $_FILES['fileUpload']['tmp_name'],
        'majors',
        ['name', 'short', 'faculty'],
        [1],
        'sss'
    );
} else if ($_POST["action"] == "importDistributions") {
    $res = importExcel(
        $mysqli,
        $_FILES['fileUpload']['tmp_name'],
        'distributions',
        ['name', 'ident', 'semester_applicable', 'major', 'faculty', 'type'],
        [1],
        'sssssi'
    );
} else if ($_POST["action"] == "importGrades") {
    $res = importExcel(
        $mysqli,
        $_FILES['fileUpload']['tmp_name'],
        'student_grades',
        ['student_fn', 'grade', 'semester'],
        [[0, 2]],
        'sdi'
    );
}

//If it recognized the action
if ($res != null) {
    $_SESSION["alert"] = [
        "type" => $res[0] == 1 ? "success" : "danger",
        "text" => $res[1]
    ];
    echo $res[0];
    exit;
}

//It didnt recognize the action
$_SESSION["alert"] = [
    "type" => "danger",
    "text" => "Unrecognized action"
];
echo 0;
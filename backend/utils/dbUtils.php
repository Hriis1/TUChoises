<?php
function getFromDBByID($table_name, $id, $mysqli, $idRowName = 'id')
{
    // Determine the type: 'i' for integers, 's' for strings
    $type = is_numeric($id) ? "i" : "s";

    $stmt = $mysqli->prepare("SELECT * FROM " . $table_name . " WHERE " . $idRowName . " = ?");
    $stmt->bind_param($type, $id);

    $stmt->execute();

    $result = $stmt->get_result();
    $arr = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return !empty($arr) ? $arr[0] : null;
}


function getNonDeletedFromDB($table_name, $mysqli, $hasDeleted = true, $searchableByID = false, $order = "")
{
    $check = $hasDeleted ? " WHERE deleted = 0" : "";
    $stmt = $mysqli->prepare("SELECT * FROM " . $table_name . $check . " " . $order);
    $stmt->execute();

    $result = $stmt->get_result();
    $arr = [];

    if ($searchableByID) { //if this is set to true the data will be accessiblee like $arr[$id] = ['name' => 'Goshko', 'email' => 'goshko@abv.bg'....]
        while ($row = $result->fetch_assoc()) {
            $arr[$row['id']] = $row;
        }
    } else { //if not it will just return array of arrays representing all the data
        $arr = $result->fetch_all(MYSQLI_ASSOC);
    }

    $stmt->close();

    return $arr;
}

function getFromDBCondition($table_name, $condition, $mysqli, $searchableByID = false)
{
    $stmt = $mysqli->prepare("SELECT * FROM " . $table_name . " " . $condition);
    $stmt->execute();

    $result = $stmt->get_result();
    $arr = [];

    if ($searchableByID) { //if this is set to true the data will be accessiblee like $arr[$id] = ['name' => 'Goshko', 'email' => 'goshko@abv.bg'....]
        while ($row = $result->fetch_assoc()) {
            $arr[$row['id']] = $row;
        }
    } else { //if not it will just return array of arrays representing all the data
        $arr = $result->fetch_all(MYSQLI_ASSOC);
    }

    $stmt->close();

    return $arr;

}

function deleteFromDB($table_name, $id, $mysqli, $idRowName = 'id')
{
    $stmt = $mysqli->prepare("DELETE FROM " . $table_name . " WHERE " . $idRowName . " = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

function setDeletedDB($table_name, $id, $mysqli, $idRowName = 'id')
{
    $stmt = $mysqli->prepare("UPDATE `$table_name` SET deleted = 1 WHERE `$idRowName` = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

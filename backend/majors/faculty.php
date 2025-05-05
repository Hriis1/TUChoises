<?php

require_once "Major.php";
class Faculty
{
    private $id;
    private $name;
    private $short;

    public function __construct($id, mysqli $mysqli)
    {
        $this->id = $id;

        $stmt = $mysqli->prepare("SELECT name, short FROM faculties WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $mysqli->error);
        }

        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $stmt->bind_result($name, $short);
        if (!$stmt->fetch()) {
            throw new Exception('Faculty not found for ID ' . $this->id);
        }
        $stmt->close();

        $this->name = $name;
        $this->short = $short;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getShort()
    {
        return $this->short;
    }

    public function getMajors(mysqli $mysqli)
    {
        $stmt = $mysqli->prepare("SELECT id FROM majors WHERE faculty = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $mysqli->error);
        }
        
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $majors = [];
        while ($row = $result->fetch_assoc()) {
            $majors[] = new Major($row['id'], $mysqli);
        }
        
        $stmt->close();
        return $majors;
    }
}

<?php
class Major
{
    private $id;
    private $name;
    private $short;
    private $facultyShort;

    public function __construct($id, mysqli $mysqli)
    {
        $this->id = $id;

        $stmt = $mysqli->prepare("SELECT name, short, faculty FROM majors WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $mysqli->error);
        }

        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $stmt->bind_result($name, $short, $facultyShort);
        if (!$stmt->fetch()) {
            throw new Exception('Major not found for ID ' . $this->id);
        }
        $stmt->close();

        $this->name = $name;
        $this->short = $short;
        $this->facultyShort = $facultyShort;
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

    public function getFacultyShort()
    {
        return $this->facultyShort;
    }

    public function getFacultyID($mysqli)
    {
        $stmt = $mysqli->prepare("SELECT id FROM faculties WHERE short = ?");
        $stmt->bind_param("s", $this->facultyShort);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['id'] ?? 0;
    }

}
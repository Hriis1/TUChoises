<?php 
class Major
{
    private $id;
    private $name;
    private $short;
    private $facultyId;

    public function __construct($id, mysqli $mysqli)
    {
        $this->id = $id;

        $stmt = $mysqli->prepare("SELECT name, short, faculty FROM majors WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $mysqli->error);
        }

        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $stmt->bind_result($name, $short, $facultyId);
        if (!$stmt->fetch()) {
            throw new Exception('Major not found for ID ' . $this->id);
        }
        $stmt->close();

        $this->name = $name;
        $this->short = $short;
        $this->facultyId = $facultyId;
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

    public function getFacultyId()
    {
        return $this->facultyId;
    }
}
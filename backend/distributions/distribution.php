<?php
class Distribution
{
    private $id;
    private $name;
    private $ident;
    private $semesterApplicable;
    private $majorShort;

    private $facultyShort;
    private $type;

    public function __construct(int $id, mysqli $mysqli)
    {
        $this->id = $id;

        $stmt = $mysqli->prepare(
            "SELECT name, ident, semester_applicable, major, faculty, type
             FROM distributions
             WHERE id = ?"
        );
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $mysqli->error);
        }

        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $stmt->bind_result(
            $name,
            $ident,
            $semesterApplicable,
            $majorShort,
            $facultyShort,
            $type
        );
        if (!$stmt->fetch()) {
            throw new Exception('Distribution not found for ID ' . $this->id);
        }
        $stmt->close();

        $this->name = $name;
        $this->ident = $ident;
        $this->semesterApplicable = $semesterApplicable;
        $this->majorShort = $majorShort;
        $this->facultyShort = $facultyShort;
        $this->type = $type;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getIdent()
    {
        return $this->ident;
    }

    public function getSemesterApplicable()
    {
        return $this->semesterApplicable;
    }

    public function getMajorShort()
    {
        return $this->majorShort;
    }

    public function getMajorID($mysqli)
    {
        $stmt = $mysqli->prepare("SELECT id FROM majors WHERE short = ?");
        $stmt->bind_param("s", $this->majorShort);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['id'] ?? 0;
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

    public function getType()
    {
        return $this->type;
    }
}
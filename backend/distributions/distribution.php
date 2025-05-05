<?php
class Distribution
{
    private $id;
    private $name;
    private $ident;
    private $semesterApplicable;
    private $majorId;

    private $facultyId;
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
            $majorId,
            $facultyId,
            $type
        );
        if (!$stmt->fetch()) {
            throw new Exception('Distribution not found for ID ' . $this->id);
        }
        $stmt->close();

        $this->name = $name;
        $this->ident = $ident;
        $this->semesterApplicable = $semesterApplicable;
        $this->majorId = $majorId;
        $this->facultyId = $facultyId;
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

    public function getMajorId()
    {
        return $this->majorId;
    }

    public function getFacultyId()
    {
        return $this->facultyId;
    }

    public function getType()
    {
        return $this->type;
    }
}
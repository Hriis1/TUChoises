<?php

require_once __DIR__ . "/../utils/dbUtils.php";
require_once __DIR__ . "/../users/User.php";
require_once __DIR__ . "/DistributionChoise.php";

class Distribution
{
    private $id;
    private $name;
    private $ident;
    private $semesterApplicable;
    private $majorShort;

    private $facultyShort;
    private $type;
    private $active;

    public function __construct(int $id, mysqli $mysqli)
    {
        $this->id = $id;

        $stmt = $mysqli->prepare(
            "SELECT name, ident, semester_applicable, major, faculty, type, active
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
            $type,
            $active
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
        $this->active = $active;
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

    public function getFacultyID(mysqli $mysqli)
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

    public function getTypeText()
    {
        if ($this->type == 1) {
            return "Избираема дисциплина";
        } else if ($this->type == 2) {
            return "Дипломен ръководител";
        }

        return "";
    }

    public function isActive()
    {
        return $this->active == 1;
    }

    public function getChoices(mysqli $mysqli)
    {
        $id = $this->id;
        $choices = [];
        $choicesDB = getFromDBCondition("distribution_choices", "WHERE distribution = $id AND deleted = 0", $mysqli);
        foreach ($choicesDB as $curr) {
            $choices[] = new DistributionChoice($curr["id"], $mysqli);
        }

        return $choices;
    }

    public function canView(User $user, mysqli $mysqli)
    {
        $role = $user->getRole();
        if ($role == 1) { //student
            //return true if student has access
            //if user is a high enogh semester to view
            $condition = $user->getSemester() - $this->semesterApplicable >= -1;
            //if users faculty or major matches the distributions based on its type
            if ($this->type == 1) {//izbiraema disciplina
                $condition = $condition && $this->majorShort == $user->getMajorShort();
            } else if ($this->type == 2) { //diplom
                $condition = $condition && $this->facultyShort == $user->getFacultyShort();
            }

            return $condition;
        } else if ($role == 2) { //teacher
            //return true if teacher is in the choices
            $choices = $this->getChoices($mysqli);
            foreach ($choices as $curr) {
                if ($curr->getInstructorId() == $user->getId())
                    return true;
            }
            return false;
        } else if ($role == 3) { //admin
            return true;
        }

        return false;
    }

    public function canChoose(User $user)
    {
        if ($user->getRole() != 1) //if user is not student
            return false;

        //if user is this or the prev semester of the dist
        $semesterDif = $user->getSemester() - $this->semesterApplicable;
        $condition = $semesterDif >= -1 && $semesterDif <= 0;

        //if users faculty or major matches the distributions based on its type
        if ($this->type == 1) {//izbiraema disciplina
            $condition = $condition && $this->majorShort == $user->getMajorShort();
        } else if ($this->type == 2) { //diplom
            $condition = $condition && $this->facultyShort == $user->getFacultyShort();
        }

        return $condition;
    }

    public function canEditChoice(User $user, mysqli $mysqli)
    {
        if ($user->getRole() != 2) //if user is not teacher
            return false;

        $choices = $this->getChoices($mysqli);

        foreach ($choices as $curr) {
            if ($curr->getInstructorId() == $user->getId())
                return true;
        }

        return false;
    }
}
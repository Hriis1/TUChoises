<?php
class User
{
    private $id;
    private $username;
    private $names;
    private $email;
    private $role;
    private $fn;
    private $majorShort;
    private $facultyShort;
    private $startYear;

    public function __construct($id, mysqli $mysqli)
    {
        $this->id = $id;

        $stmt = $mysqli->prepare(
            "SELECT username, names, email, role, fn, major, faculty, start_year
             FROM users
             WHERE id = ?
             LIMIT 1"
        );
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $mysqli->error);
        }

        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $stmt->bind_result(
            $username,
            $names,
            $email,
            $role,
            $fn,
            $majorShort,
            $facultyShort,
            $startYear
        );
        if (!$stmt->fetch()) {
            throw new Exception('User not found for ID ' . $this->id);
        }
        $stmt->close();

        $this->username = $username;
        $this->names = $names;
        $this->email = $email;
        $this->role = $role;
        $this->fn = $fn;
        $this->majorShort = $majorShort;
        $this->facultyShort = $facultyShort;
        $this->startYear = $startYear;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getNames()
    {
        return $this->names;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getRoleName()
    {
        switch ($this->role) {
            case 1:
                return "student";
            case 2:
                return "teacher";
            case 3:
                return "admin";
            default:
                return "";
        }
    }

    public function getFn()
    {
        return $this->fn;
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

    public function getStartYear()
    {
        return $this->startYear;
    }

    public function getSemester()
    {
        //If user is not a student
        if ($this->role != 1) {
            return 0;
        }

        $userSemester = 1;
        $year = (int) date('Y');
        $month = (int) date('n');

        if ($month == 1) {
            $year--;
        }

        //User has not started yet
        if ($year < $this->startYear) {
            return 0;
        }

        $userSemester += ($year - $this->startYear) * 2;
        if ($month >= 2 && $month < 9) {
            $userSemester--;
        }

        return $userSemester;
    }

    //Static
    public static function getSemesterByYear($startYear)
    {
        $userSemester = 1;
        $year = (int) date('Y');
        $month = (int) date('n');

        if ($month == 1) {
            $year--;
        }

        if ($year < $startYear) {
            return 0;
        }

        $userSemester += ($year - $startYear) * 2;
        if ($month >= 2 && $month < 9) {
            $userSemester--;
        }

        return $userSemester;
    }

}
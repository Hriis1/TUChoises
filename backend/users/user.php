<?php
class User
{
    private $id;
    private $username;
    private $names;
    private $email;
    private $role;
    private $fn;
    private $stream;
    private $majorId;
    private $startYear;

    public function __construct($id, mysqli $mysqli)
    {
        $this->id = $id;

        $stmt = $mysqli->prepare(
            "SELECT username, names, email, role, fn, stream, major, start_year
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
            $stream,
            $majorId,
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
        $this->stream = $stream;
        $this->majorId = $majorId;
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

    public function getFn()
    {
        return $this->fn;
    }

    public function getStream()
    {
        return $this->stream;
    }

    public function getMajorId()
    {
        return $this->majorId;
    }

    public function getStartYear()
    {
        return $this->startYear;
    }
}
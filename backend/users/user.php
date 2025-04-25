<?php
class User {
    private $id;
    private $username;
    private $names;
    private $email;
    private $role;
    private $fn;
    private $stream;
    private $majorId;
    private $start_year;

    public function __construct(int $id, mysqli $mysqli) {
        $this->id = $id;
        if ($stmt = $mysqli->prepare("SELECT username, names, email, role, fn, stream, major, start_year FROM users WHERE id = ? LIMIT 1")) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($username, $names, $email, $role, $fn, $stream, $majorId, $start_year);
            if ($stmt->fetch()) {
                $this->username   = $username;
                $this->names      = $names;
                $this->email      = $email;
                $this->role       = $role;
                $this->fn         = $fn;
                $this->stream     = $stream;
                $this->majorId      = $majorId;
                $this->start_year = $start_year;
            }
            $stmt->close();
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getNames() {
        return $this->names;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getRole() {
        return $this->role;
    }

    public function getFn() {
        return $this->fn;
    }

    public function getStream() {
        return $this->stream;
    }

    public function getMajorId() {
        return $this->majorId;
    }

    public function getStartYear() {
        return $this->start_year;
    }
}
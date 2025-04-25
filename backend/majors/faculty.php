<?php

class Faculty
{
    private $mysqli;
    private $id;
    private $name;
    private $short;

    public function __construct(mysqli $mysqli, int $id)
    {
        $this->mysqli = $mysqli;
        $this->id = $id;

        $stmt = $this->mysqli->prepare("SELECT name, short FROM faculties WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $this->mysqli->error);
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

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShort(): string
    {
        return $this->short;
    }
}

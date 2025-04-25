<?php

class DistributionChoice
{
    private $id;
    private $name;
    private $ident;
    private $distributionId;
    private $instructorId;
    private $description;

    public function __construct(int $id, mysqli $mysqli)
    {
        $this->id = $id;

        $stmt = $mysqli->prepare(
            "SELECT name, ident, distribution, instructor, description
             FROM distribution_choices
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
            $distributionId,
            $instructorId,
            $description
        );
        if (!$stmt->fetch()) {
            throw new Exception('DistributionChoice not found for ID ' . $this->id);
        }
        $stmt->close();

        $this->name           = $name;
        $this->ident          = $ident;
        $this->distributionId = $distributionId;
        $this->instructorId   = $instructorId;
        $this->description    = $description;
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

    public function getDistributionId()
    {
        return $this->distributionId;
    }

    public function getInstructorId()
    {
        return $this->instructorId;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
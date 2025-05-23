<?php

class DistributionChoice
{
    private $id;
    private $name;
    private $distributionId;
    private $instructorId;
    private $description;
    private $min;
    private $max;
    private $minMaxEditable;

    public function __construct(int $id, mysqli $mysqli)
    {
        $this->id = $id;

        $stmt = $mysqli->prepare(
            "SELECT name, distribution, instructor, description, min, max, min_max_editble
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
            $distributionId,
            $instructorId,
            $description,
            $min,
            $max,
            $minMaxEditable
        );
        if (!$stmt->fetch()) {
            throw new Exception('DistributionChoice not found for ID ' . $this->id);
        }
        $stmt->close();

        $this->name = $name;
        $this->distributionId = $distributionId;
        $this->instructorId = $instructorId;
        $this->description = $description;
        $this->min = $min;
        $this->max = $max;
        $this->minMaxEditable = $minMaxEditable;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
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

    public function getMin()
    {
        return $this->min;
    }

    public function getMax()
    {
        return $this->max;
    }

    public function getMinMaxEditable()
    {
        return $this->minMaxEditable;
    }
}
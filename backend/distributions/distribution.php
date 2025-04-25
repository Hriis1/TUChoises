<?php
class Distribution
{
    private $id;
    private $name;
    private $ident;
    private $yearApplicable;
    private $majorId;
    private $type;

    public function __construct(int $id, mysqli $mysqli)
    {
        $this->id = $id;

        $stmt = $mysqli->prepare(
            "SELECT name, ident, year_applicable, major, type
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
            $yearApplicable,
            $majorId,
            $type
        );
        if (!$stmt->fetch()) {
            throw new Exception('Distribution not found for ID ' . $this->id);
        }
        $stmt->close();

        $this->name = $name;
        $this->ident = $ident;
        $this->yearApplicable = $yearApplicable;
        $this->majorId = $majorId;
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

    public function getYearApplicable()
    {
        return $this->yearApplicable;
    }

    public function getMajorId()
    {
        return $this->majorId;
    }

    public function getType()
    {
        return $this->type;
    }
}
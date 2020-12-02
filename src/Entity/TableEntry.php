<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PointsTable")
 * @ORM\Table(name="table_entry")
 */
class TableEntry
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var Match
     * @ORM\ManyToOne(targetEntity="Match")
     * @ORM\JoinColumn(name="match_id", referencedColumnName="id")
     */
    private $match;
    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $user;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $played = 0;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $pints = 0;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $bonusPoints = 0;
    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $points = 0;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $currentPosition;


    public function getUser(): string
    {
        return $this->user;
    }

    public function getPoints(): float
    {
        return $this->points;
    }

    public function getCurrentPosition(): int
    {
        return $this->currentPosition;
    }

    public function getPints(): int
    {
        return $this->pints;
    }

    public function setMatch(Match $match): TableEntry
    {
        $this->match = $match;
        return $this;
    }

    public function setUser(string $user): TableEntry
    {
        $this->user = $user;
        return $this;
    }

    public function setPlayed(int $played): TableEntry
    {
        $this->played = $played;
        return $this;
    }

    public function setPints(int $pints): TableEntry
    {
        $this->pints = $pints;
        return $this;
    }

    public function setBonusPoints(int $bonusPoints): TableEntry
    {
        $this->bonusPoints = $bonusPoints;
        return $this;
    }

    public function setPoints(float $points): TableEntry
    {
        $this->points = $points;
        return $this;
    }

    public function setCurrentPosition(int $currentPosition): TableEntry
    {
        $this->currentPosition = $currentPosition;
        return $this;
    }

}
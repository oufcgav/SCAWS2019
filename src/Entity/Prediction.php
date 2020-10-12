<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PredictionRepository")
 */
class Prediction
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $position;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $time;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $atMatch = false;

    /**
     * @var Match
     * @ORM\ManyToOne(targetEntity="Match")
     * @ORM\JoinColumn(name="match_id", referencedColumnName="id")
     */
    private $match;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $points = 0.0;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $niceTime = 'no';

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Score", mappedBy="prediction")
     */
    private $scores;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $reset = false;

    public function __construct()
    {
        $this->scores = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function setTime(string $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getMatch(): ?Match
    {
        return $this->match;
    }

    public function setMatch(Match $match): self
    {
        $this->match = $match;

        return $this;
    }

    public function getPoints(): ?float
    {
        return $this->points;
    }

    public function setPoints(float $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getNiceTime(): ?string
    {
        return $this->niceTime;
    }

    public function setNiceTime(string $niceTime): self
    {
        $this->niceTime = $niceTime;

        return $this;
    }

    public function getAtMatch(): ?bool
    {
        return $this->atMatch;
    }

    public function setAtMatch(bool $atMatch): self
    {
        $this->atMatch = $atMatch;

        return $this;
    }

    public function hasScored(): bool
    {
        return $this->scores->count() > 0;
    }

    public function addScore(Score $score): self
    {
        $this->scores->add($score);
        $this->points += $score->getPoints();

        return $this;
    }

    public function setReset(): self
    {
        $this->reset = true;

        return $this;
    }

    public function getScores(): array
    {
        return $this->scores->toArray();
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('position', new NotBlank());
        $metadata->addPropertyConstraint('time', new NotBlank());
        $metadata->addPropertyConstraint('atMatch', new NotBlank());
        $metadata->addPropertyConstraint('niceTime', new NotBlank());
    }
}

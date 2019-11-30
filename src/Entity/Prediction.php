<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PredictionRepository")
 */
class Prediction
{
    /**
     * @var integer
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
    private $atMatch;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $match_id;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $points = 0.0;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $nice_time;

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

    public function getMatchId(): ?int
    {
        return $this->match_id;
    }

    public function setMatchId(int $match_id): self
    {
        $this->match_id = $match_id;

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
        return $this->nice_time;
    }

    public function setNiceTime(string $nice_time): self
    {
        $this->nice_time = $nice_time;

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

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('position', new NotBlank());
        $metadata->addPropertyConstraint('time', new NotBlank());
        $metadata->addPropertyConstraint('atMatch', new NotBlank());
        $metadata->addPropertyConstraint('nice_time', new NotBlank());
    }
}

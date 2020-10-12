<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="\App\Repository\FixtureList")
 * @ORM\Table(name="`match`")
 */
class Match
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $opponent = '';
    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="date_immutable")
     */
    private $date;
    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $location = '';
    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $competition = '';
    /**
     * @var Season
     * @ORM\ManyToOne(targetEntity="Season")
     * @ORM\JoinColumn(name="season_id", referencedColumnName="id")
     */
    private $season;

    public function __construct()
    {
        $this->date = new \DateTimeImmutable();
    }

    public function getOpponent(): string
    {
        return $this->opponent;
    }

    public function setOpponent(string $opponent): self
    {
        $this->opponent = $opponent;

        return $this;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getCompetition(): string
    {
        return $this->competition;
    }

    public function setCompetition(string $competition): self
    {
        $this->competition = $competition;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setSeason(Season $season): Match
    {
        $this->season = $season;

        return $this;
    }

    public function qualifiesForBonusPoint()
    {
        return $this->location !== 'Home' || $this->competition !== 'League';
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('opponent', new NotBlank());
        $metadata->addPropertyConstraint('date', new NotBlank());
        $metadata->addPropertyConstraint('date', new Type(\DateTimeImmutable::class));
        $metadata->addPropertyConstraint('location', new NotBlank());
        $metadata->addPropertyConstraint('competition', new NotBlank());
    }
}

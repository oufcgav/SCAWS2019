<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PintRepository")
 */
class Pint
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
     * @var int
     * @ORM\Column(type="integer")
     */
    private $count = 0;

    /**
     * @var MatchDay
     * @ORM\ManyToOne(targetEntity="MatchDay")
     * @ORM\JoinColumn(name="match_id", referencedColumnName="id")
     */
    private $match;

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

    public function getMatch(): ?MatchDay
    {
        return $this->match;
    }

    public function setMatch(MatchDay $match): self
    {
        $this->match = $match;

        return $this;
    }

    public function addPintDrunk(): self
    {
        $this->count++;

        return $this;
    }
}

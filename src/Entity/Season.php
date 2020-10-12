<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\App\Repository\SeasonList")
 * @ORM\Table(name="season")
 */
class Season
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
    private $label;
    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="date_immutable")
     */
    private $startDate;
    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="date_immutable")
     */
    private $endDate;

    public function setLabel(string $label): Season
    {
        $this->label = $label;

        return $this;
    }

    public function setStartDate(\DateTimeImmutable $startDate): Season
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function setEndDate(\DateTimeImmutable $endDate): Season
    {
        $this->endDate = $endDate;

        return $this;
    }
}

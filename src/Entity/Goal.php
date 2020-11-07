<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GoalRepository")
 * @ORM\Table(name="goal")
 */
class Goal
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
    private $scorer = '';
    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $timing = '';
    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $position = '';
    /**
     * @var Match
     * @ORM\ManyToOne(targetEntity="Match", inversedBy="goals")
     * @ORM\JoinColumn(name="match_id", referencedColumnName="id")
     */
    private $match;
    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Score", mappedBy="goal")
     */
    private $scores;

    public function __construct()
    {
        $this->scores = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getScorer(): string
    {
        return $this->scorer;
    }

    public function getTiming(): string
    {
        return $this->timing;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getMatch(): Match
    {
        return $this->match;
    }

    /**
     * @return Score[]
     */
    public function getScores(): array
    {
        return $this->scores->toArray();
    }

    public function setScorer(string $scorer): Goal
    {
        $this->scorer = $scorer;
        if (preg_match('#\([A-Z]{1,2}\)#', $scorer, $matches) === false) {
            throw new \DomainException('Cannot find position for scorer');
        }
        $positionCode = trim($matches[0], '()');
        switch ($positionCode) {
            case 'GK':
                $this->position = Positions::GOALKEEPER()->getValue();
                break;
            case 'D':
                $this->position = Positions::DEFENDERS()->getValue();
                break;
            case 'M':
                $this->position = Positions::MIDFIELDERS()->getValue();
                break;
            case 'S':
                $this->position = Positions::STRIKERS()->getValue();
                break;
            case 'O':
                $this->position = 'Own goal';
                break;
        }

        return $this;
    }

    public function setTiming(string $timing): Goal
    {
        $this->timing = $timing;

        return $this;
    }

    public function setMatch(Match $match): self
    {
        $this->match = $match;
        $match->addGoal($this);

        return $this;
    }

    public function addScore(Score $score): self
    {
        if (!$this->scores->contains($score)) {
            $this->scores->add($score);
        }

        return $this;
    }

    public function setPosition(string $position)
    {
        $this->position = $position;

        return $this;
    }
}

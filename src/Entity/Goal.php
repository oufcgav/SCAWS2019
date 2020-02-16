<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\App\Repository\GoalRepository")
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
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $matchId;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getScorer(): string
    {
        return $this->scorer;
    }

    /**
     * @return string
     */
    public function getTiming(): string
    {
        return $this->timing;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getMatchId(): int
    {
        return $this->matchId;
    }

    /**
     * @param string $scorer
     * @return Goal
     */
    public function setScorer(string $scorer): Goal
    {
        $this->scorer = $scorer;
        if (preg_match('#\([A-Z]{1,2}\)#', $scorer, $matches) === false) {
            throw new \DomainException('Cannot find position for scorer');
        }
        $positionCode = trim($matches[0], '()');
        switch ($positionCode) {
            case 'GK':
                $this->position = 'Goalkeeper';
                break;
            case 'D':
                $this->position = 'Defenders';
                break;
            case 'M':
                $this->position = 'Midfielders';
                break;
            case 'S':
                $this->position = 'Strikers';
                break;
            case 'O':
                $this->position = 'Own goal';
                break;
        }
        return $this;
    }

    /**
     * @param string $timing
     * @return Goal
     */
    public function setTiming(string $timing): Goal
    {
        $this->timing = $timing;
        return $this;
    }

    public function setMatchId(int $matchId): self
    {
        $this->matchId = $matchId;

        return $this;
    }
}

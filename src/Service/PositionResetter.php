<?php

namespace App\Service;

use App\Entity\MatchDay;
use App\Entity\Season;
use App\Repository\FixtureList;

class PositionResetter
{
    /**
     * @var FixtureList
     */
    private $fixtureList;

    public function __construct(FixtureList $fixtureList)
    {
        $this->fixtureList = $fixtureList;
    }

    public function reset(Season $season, MatchDay $match): MatchDay
    {
        $matchesInSeason = $this->fixtureList->findPreviousMatches($season, $match);
        if (empty($matchesInSeason)) {
            $match->setReset();

            return $match;
        }
        $lastReset = array_reduce($matchesInSeason, function (?MatchDay $lastReset, MatchDay $match) {
            if (!$lastReset) {
                return $match->resetPositionChoices() ? $match : null;
            }
            if (!$match->resetPositionChoices()) {
                return $lastReset;
            }
            if ($match->getDate() > $lastReset->getDate()) {
                return $match;
            }

            return $lastReset;
        }, null);
        $matchesSinceReset = array_filter($matchesInSeason, function (MatchDay $match) use ($lastReset) {
            return !$lastReset || $match->getDate() > $lastReset->getDate();
        });
        if (count($matchesSinceReset) >= 2) {
            $match->setReset();
        }

        return $match;
    }
}

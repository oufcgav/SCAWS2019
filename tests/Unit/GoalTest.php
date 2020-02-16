<?php

namespace App\Tests\Unit;

use App\Entity\Goal;
use PHPUnit\Framework\TestCase;

class GoalTest extends TestCase
{
    /**
     * @dataProvider dataForSetPositions
     */
    public function testSetPositionsCorrectly($scorer, $position)
    {
        $goal = new Goal();
        $goal->setScorer($scorer);

        $this->assertEquals($position, $goal->getPosition());
    }

    public function dataForSetPositions()
    {
        return [
            ['Simon Eastwood (GK)', 'Goalkeeper'],
            ['Josh the Truffle Ruffels (D)', 'Defenders'],
            ['Branwen Brannigan III (M)', 'Midfielders'],
            ['Dan! (S)', 'Strikers'],
            ['Andy Clyde (O)', 'Own goal'],
        ];
    }
}
<?php

namespace App\Tests\Web;

class TableTest extends WebTestCase
{
    public function testTableShowsAllSevenUsers()
    {
        $client = $this->login();
        $table = $client->request('GET', '/table');
        $this->assertEquals(7, $table->filter('tbody tr')->count());
        $points = $table->filter('tbody tr .points');
        foreach ($points as $userPoints) {
            $this->assertEquals('0.0', trim($userPoints->textContent));
        }
    }

    public function testTableForPreviousSeasonShowsResults()
    {
        $client = $this->login();
        $table = $client->request('GET', '/7/table');
        $this->assertEquals(7, $table->filter('tbody tr')->count());
        $points = $table->filter('tbody tr .points');
        foreach ($points as $userPoints) {
            $this->assertGreaterThan('0.0', trim($userPoints->textContent));
        }
    }
}

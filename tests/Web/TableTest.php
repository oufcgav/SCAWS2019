<?php

namespace App\Tests\Web;

class TableTest extends WebTestCase
{

    public function testTableShowsAllSevenUsers()
    {
        $client = $this->login();
        $table = $client->request('GET', '/table');
        $this->assertEquals(7, $table->filter('tbody tr')->count());
    }

}

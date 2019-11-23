<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class TableTest extends WebTestCase
{

    public function testTableShowsAllSevenUsers()
    {
        $client = $this->login();
        $table = $client->request('GET', '/table');
        $this->assertEquals(7, $table->filter('tbody tr')->count());
    }

}

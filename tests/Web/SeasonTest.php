<?php

namespace App\Tests\Web;

class SeasonTest extends WebTestCase
{
    public function testListAllSeasons()
    {
        $client = $this->login();
        $index = $client->request('GET', '/');
        $link = $index
            ->filter('a:contains("Seasons")')
            ->eq(0)
            ->link();
        $client->click($link);
        $this->assertContains('Old season', $client->getResponse()->getContent());
        $this->assertContains('Current season', $client->getResponse()->getContent());
    }
}

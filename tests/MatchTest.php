<?php

namespace App\Tests;


class MatchTest extends WebTestCase
{
    public function testNoMatchIsDisplayedByDefault()
    {
        $client = $this->login();
        $client->request('GET', '/');
        $this->assertContains('No current match', $client->getResponse()->getContent());
    }

    public function testAddNewMatch()
    {
        $client = $this->login();
        $index = $client->request('GET', '/');
        $link = $index
            ->filter('a:contains("Add match")')
            ->eq(0)
            ->link()
        ;
        $match = $client->click($link);
        $form = $match->selectButton('Add')->form();

        $form['match[opponent]'] = $opposition = uniqid('team');
        $form['match[date]'] = date('Y-m-d');
        $form['match[location]'] = 'Home';
        $form['match[competition]'] = 'League';

        $client->submit($form);
        $client->followRedirect();
        $this->assertContains($opposition, $client->getResponse()->getContent());
    }
}

<?php

namespace App\Tests\Web;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class GoalTest extends WebTestCase
{
    public function testCannotAddGoalIfNoMatch()
    {
        $client = $this->login();
        $index = $client->request('GET', '/');
        $link = $index
            ->filter('a:contains("Add goal")')
            ->eq(0)
            ->link()
        ;
        $client->click($link);
        $this->assertResponseStatusCodeSame(302);
        $this->assertEquals('/', $client->getResponse()->headers->get('Location'));
    }

    public function testAddGoal()
    {
        $client = $this->login();
        $client = $this->addMatch($client);

        $index = $client->request('GET', '/');
        $link = $index
            ->filter('a:contains("Add goal")')
            ->eq(0)
            ->link();
        $match = $client->click($link);
        $form = $match->selectButton('Add')->form();

        $form['goal[scorer]'] = $player = 'Dan! (S)';
        $form['goal[timing]'] = $timing = 'Second half';

        $client->submit($form);
        $client->followRedirect();
        $this->assertContains($player, $client->getResponse()->getContent());
        $this->assertContains($timing, $client->getResponse()->getContent());
    }

    protected function addMatch(KernelBrowser $client): KernelBrowser
    {
        $index = $client->request('GET', '/');
        $link = $index
            ->filter('a:contains("Add match")')
            ->eq(0)
            ->link();
        $match = $client->click($link);
        $form = $match->selectButton('Add')->form();

        $form['match[opponent]'] = $opposition = uniqid('team');
        $form['match[date]'] = date('Y-m-d');
        $form['match[location]'] = 'Home';
        $form['match[competition]'] = 'League';

        $client->submit($form);

        return $client;
    }
}

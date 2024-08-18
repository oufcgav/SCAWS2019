<?php

namespace App\Tests\Web;

use App\Entity\GoalTimes;

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
        $form['goal[timing]'] = $timing = GoalTimes::SIXTH_FIFTEEN()->getValue();

        $client->submit($form);
        $client->followRedirect();
        $this->assertStringContainsString($player, $client->getResponse()->getContent());
        $this->assertStringContainsString($timing, $client->getResponse()->getContent());
    }
}

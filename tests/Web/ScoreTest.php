<?php

namespace App\Tests\Web;

use App\Entity\GoalTimes;
use App\Entity\Positions;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class ScoreTest extends WebTestCase
{
    public function testScoresAreAddedAfterGoal()
    {
        $client = $this->login();
        $client = $this->addMatch($client);
        $this->addPrediction($client, 'Andy', Positions::DEFENDERS()->getValue(), GoalTimes::SECOND_HALF()->getValue());
        $currentPoints = $this->getCurrentPointsTotal($client, 'Andy');
        $client = $this->addGoal($client, 'Dan! (S)', GoalTimes::SIXTH_FIFTEEN()->getValue());
        $newPoints = $this->getCurrentPointsTotal($client, 'Andy');

        $this->assertGreaterThan($currentPoints, $newPoints);
    }

    private function getCurrentPointsTotal(KernelBrowser $client, $user): int
    {
        $table = $client->request('GET', '/table');

        return (int) $table->filter("tr#$user > td.points")
            ->first()
            ->text()
        ;
    }

    private function addGoal(KernelBrowser $client, $player, $timing): KernelBrowser
    {
        $index = $client->request('GET', '/');
        $link = $index
            ->filter('a:contains("Add goal")')
            ->eq(0)
            ->link();
        $match = $client->click($link);
        $form = $match->selectButton('Add')->form();

        $form['goal[scorer]'] = $player;
        $form['goal[timing]'] = $timing;

        $client->submit($form);
        $client->followRedirect();

        return $client;
    }
}

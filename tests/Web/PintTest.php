<?php

namespace App\Tests\Web;

use App\Entity\Match;
use App\Entity\Prediction;
use App\Repository\FixtureList;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class PintTest extends WebTestCase
{
    public function testCannotAddPintIfNoMatch()
    {
        $client = $this->login();
        $index = $client->request('GET', '/');
        $link = $index
            ->filter('a:contains("Add pint")')
            ->eq(0)
            ->link();
        $client->click($link);
        $this->assertResponseStatusCodeSame(302);
        $this->assertEquals('/', $client->getResponse()->headers->get('Location'));
    }

    public function testAddPint()
    {
        $client = $this->login();
        $client = $this->addMatch($client);

        $this->addPrediction($client, 'Deadly');

        $currentPints = $this->getCurrentPintsTotal($client, 'Deadly');

        $index = $client->request('GET', '/');
        $link = $index
            ->filter('a:contains("Add pint")')
            ->eq(0)
            ->link();
        $match = $client->click($link);
        $form = $match->selectButton('Add')->form();

        $form['pint[user]'] = 'Deadly';

        $client->submit($form);
        $client->followRedirect();
        $newPints = $this->getCurrentPintsTotal($client, 'Deadly');
        $this->assertGreaterThan($currentPints, $newPints);
    }

    public function testAddMyPint()
    {
        $client = $this->login();
        $client = $this->addMatch($client);

        $this->addPrediction($client, 'Andy');

        $currentPints = $this->getCurrentPintsTotal($client, 'Andy');

        $client->request('GET', '/mypint');
        $client->followRedirect();

        $newPints = $this->getCurrentPintsTotal($client, 'Andy');
        $this->assertGreaterThan($currentPints, $newPints);
    }

    private function getCurrentPintsTotal(KernelBrowser $client, $user): int {
        $table = $client->request('GET', '/table');
        return (int)$table->filter("tr#$user > td.pints")
            ->first()
            ->text()
        ;
    }

    private function addPrediction(KernelBrowser $client, $user)
    {
        $container = $client->getContainer();
        $fixtureList = $container->get(FixtureList::class);
        $em = $container->get('doctrine.orm.entity_manager');
        $prediction = (new Prediction())
            ->setUser($user)
            ->setPosition('Defenders')
            ->setTime('Second half')
            ->setMatchId($fixtureList->findNextMatch()->getId())
            ->setAtMatch(true)
            ->setNiceTime('Yes')
        ;
        $em->persist($prediction);
        $em->flush();
    }
}

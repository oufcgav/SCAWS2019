<?php

namespace App\Tests;

use App\Entity\Match;

class PredictionTest extends WebTestCase
{
    public function testCannotAddPredictionIfNoMatch()
    {
        $client = $this->login();
        $index = $client->request('GET', '/');
        $link = $index
            ->filter('a:contains("Add prediction")')
            ->eq(0)
            ->link()
        ;
        $client->click($link);
        $this->assertResponseStatusCodeSame(302);
        $this->assertEquals('/', $client->getResponse()->headers->get('Location'));
    }

    public function testAddNewPrediction()
    {
        $client = $this->login();
        $container = $client->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $prediction = (new Match())
            ->setOpponent('Predictable')
            ->setDate(new \DateTimeImmutable())
            ->setLocation('Home')
            ->setCompetition('League')
        ;
        $em->persist($prediction);
        $em->flush();

        $index = $client->request('GET', '/');
        $link = $index
            ->filter('a:contains("Add prediction")')
            ->eq(0)
            ->link()
        ;
        $prediction = $client->click($link);
        $form = $prediction->selectButton('Add')->form();

        $form['prediction[position]'] = $position = 'Defenders';
        $form['prediction[time]'] = $time = 'First half';
        $form['prediction[atMatch]'] = 'yes';
        $form['prediction[nice_time]'] = 'no';

        $client->submit($form);
        $client->followRedirect();
        $this->assertContains($position, $client->getResponse()->getContent());
        $this->assertContains($time, $client->getResponse()->getContent());
    }
}
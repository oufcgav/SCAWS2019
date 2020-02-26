<?php

namespace App\Tests\Web;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;

class PredictionTest extends WebTestCase
{
    public function testCannotAddPredictionIfNoMatch()
    {
        $client = $this->login();
        $link = $this->getAddPredictionForm($client);
        $this->assertResponseStatusCodeSame(302);
        $this->assertEquals('/', $client->getResponse()->headers->get('Location'));
    }

    public function testAddNewPrediction()
    {
        $client = $this->login();
        $client = $this->addMatch($client);

        $prediction = $this->getAddPredictionForm($client);
        $form = $prediction->selectButton('Add')->form();

        $form['prediction[position]'] = $position = 'Midfielders';
        $form['prediction[time]'] = $time = 'Second half';
        $form['prediction[atMatch]'] = 'yes';
        $form['prediction[nice_time]'] = 'no';

        $client->submit($form);
        $client->followRedirect();
        $this->assertContains($position, $client->getResponse()->getContent());
        $this->assertContains($time, $client->getResponse()->getContent());
    }

    public function testEditPrediction()
    {
        $client = $this->login();
        $client = $this->addMatch($client);

        $prediction = $this->getAddPredictionForm($client);
        $form = $prediction->selectButton('Add')->form();

        $form['prediction[position]'] = $position = 'Midfielders';
        $form['prediction[time]'] = $time = 'Second half';
        $form['prediction[atMatch]'] = 'yes';
        $form['prediction[nice_time]'] = 'no';

        $client->submit($form);
        $client->followRedirect();
        $prediction = $this->getAddPredictionForm($client);

        $form = $prediction->selectButton('Add')->form();
        $this->assertEquals($position, $form['prediction[position]']->getValue());
        $this->assertContains($time, $form['prediction[time]']->getValue());
    }

    protected function getAddPredictionForm(KernelBrowser $client): Crawler
    {
        $index = $client->request('GET', '/');
        $link = $index
            ->filter('a:contains("Add prediction")')
            ->eq(0)
            ->link();
        return $client->click($link);
    }
}
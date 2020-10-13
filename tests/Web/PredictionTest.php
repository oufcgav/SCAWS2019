<?php

namespace App\Tests\Web;

use App\Entity\GoalTimes;
use App\Entity\Positions;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Form;

class PredictionTest extends WebTestCase
{
    public function testCannotAddPredictionIfNoMatch()
    {
        $client = $this->login();
        $this->getAddPredictionForm($client);
        $this->assertResponseStatusCodeSame(302);
        $this->assertEquals('/', $client->getResponse()->headers->get('Location'));
    }

    public function testAddNewPrediction()
    {
        $client = $this->login();
        $client = $this->addMatch($client);

        $form = $this->getAddPredictionForm($client);

        $form['prediction[position]'] = $position = Positions::MIDFIELDERS()->getValue();
        $form['prediction[time]'] = $time = GoalTimes::SECOND_HALF()->getValue();
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

        $form = $this->getAddPredictionForm($client);

        $form['prediction[position]'] = $position = Positions::MIDFIELDERS()->getValue();
        $form['prediction[time]'] = $time = GoalTimes::SECOND_HALF()->getValue();
        $form['prediction[atMatch]'] = 'yes';
        $form['prediction[nice_time]'] = 'no';

        $client->submit($form);
        $client->followRedirect();

        $form = $this->getAddPredictionForm($client);
        $this->assertEquals($position, $form['prediction[position]']->getValue());
        $this->assertContains($time, $form['prediction[time]']->getValue());
    }

    protected function getAddPredictionForm(KernelBrowser $client): ?Form
    {
        $index = $client->request('GET', '/');
        $link = $index
            ->filter('a:contains("Add prediction")')
            ->eq(0)
            ->link();

        $prediction = $client->click($link);
        $button = $prediction->selectButton('Add');

        return count($button) > 0 ? $button->form() : null;
    }
}

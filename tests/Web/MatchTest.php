<?php

namespace App\Tests\Web;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Form;

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
        $form = $this->getAddMatchForm($client);

        $form['match[opponent]'] = $opposition = uniqid('team');
        $form['match[date]'] = date('Y-m-d');
        $form['match[location]'] = 'Home';
        $form['match[competition]'] = 'League';

        $client->submit($form);
        $client->followRedirect();
        $this->assertContains($opposition, $client->getResponse()->getContent());
    }

    public function testEditMatch()
    {
        $client = $this->login();
        $form = $this->getAddMatchForm($client);

        $form['match[opponent]'] = $opposition = uniqid('team');
        $form['match[date]'] = date('Y-m-d');
        $form['match[location]'] = 'Home';
        $form['match[competition]'] = 'League';

        $client->submit($form);
        $client->followRedirect();

        $form = $this->getAddMatchForm($client);
        $this->assertEquals($opposition, $form['match[opponent]']->getValue());
    }

    protected function getAddMatchForm(KernelBrowser $client): ?Form
    {
        $index = $client->request('GET', '/');
        $link = $index
            ->filter('a:contains("Add match")')
            ->eq(0)
            ->link();
        $match = $client->click($link);
        $button = $match->selectButton('Add');

        return count($button) > 0 ? $button->form() : null;
    }
}

<?php

namespace App\Tests\Web;

use App\Entity\Prediction;
use App\Repository\FixtureList;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{

    protected function login(): KernelBrowser
    {
        $client = WebTestCase::createClient();
        $login = $client->request('GET', '/login');
        $form = $login->selectButton('Sign in')->form();

        $form['username'] = 'Andy';
        $form['password'] = 'whing';

        $client->submit($form);
        return $client;
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

    protected function addPrediction(KernelBrowser $client, $user, $position = 'Defenders', $timing = 'Second half')
    {
        $container = $client->getContainer();
        $fixtureList = $container->get(FixtureList::class);
        $em = $container->get('doctrine.orm.entity_manager');
        $prediction = (new Prediction())
            ->setUser($user)
            ->setPosition($position)
            ->setTime($timing)
            ->setMatch($fixtureList->findNextMatch())
            ->setAtMatch(true)
            ->setNiceTime('Yes');
        $em->persist($prediction);
        $em->flush();
    }
}
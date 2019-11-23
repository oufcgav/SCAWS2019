<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TableTest extends WebTestCase
{

    public function testTableShowsAllSevenUsers()
    {
        $client = $this->login();
        $table = $client->request('GET', '/table');
        $this->assertEquals(7, $table->filter('tr')->count());
    }

    private function login(): KernelBrowser
    {
        $client = self::createClient();
        $login = $client->request('GET', '/login');
        $form = $login->selectButton('Sign in')->form();

        $form['username'] = 'Andy';
        $form['password'] = 'whing';

        $client->submit($form);
        return $client;
    }
}

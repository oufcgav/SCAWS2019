<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{

    public function testRedirectedToLoginWhenNotLoggedIn()
    {
        $client = self::createClient();
        $client->request('GET', '/table');

        $this->assertResponseStatusCodeSame(302);
        $this->assertEquals('/login', $client->getResponse()->headers->get('Location'));
    }

    public function testRedirectedToTableWhenLoggedIn()
    {
        $client = self::createClient();
        $login = $client->request('GET', '/login');
        $form = $login->selectButton('Sign in')->form();

        $form['username'] = 'Andy';
        $form['password'] = 'whing';

        $client->submit($form);
        $this->assertResponseStatusCodeSame(302);
        $this->assertEquals('/table', $client->getResponse()->headers->get('Location'));
    }
}

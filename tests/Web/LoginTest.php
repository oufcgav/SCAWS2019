<?php

namespace App\Tests\Web;

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
        $client = $this->login();
        $this->assertResponseStatusCodeSame(302);
        $this->assertEquals('/table', $client->getResponse()->headers->get('Location'));
    }
}

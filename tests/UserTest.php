<?php

namespace App\Tests;


class UserTest extends WebTestCase
{

    public function testViewPageForSpecificUser()
    {
        $client = $this->login();
        $client->request('GET', '/user/Andy');
        $this->assertContains('All predictions for Andy', $client->getResponse()->getContent());
    }
}
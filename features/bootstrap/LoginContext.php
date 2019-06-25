<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This context class contains the definitions of the steps used by the demo 
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 * 
 * @see http://behat.org/en/latest/quick_start.html
 */
class LoginContext implements Context
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Response|null
     */
    private $response;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param string $path
     * @throws Exception
     */
    private function makeRequest(string $path)
    {
        $this->response = $this->kernel->handle(Request::create($path, 'GET'));
    }

    /**
     * @Then the response should be received
     */
    public function theResponseShouldBeReceived()
    {
        if ($this->response === null) {
            throw new \RuntimeException('No response received');
        }
    }

    /**
     * @When I view the table
     */
    public function iViewTheTable()
    {
        $this->makeRequest('/table');
    }

    /**
     * @Then I am redirected to the login page
     */
    public function iAmRedirectedToTheLoginPage()
    {
        assert($this->response->getStatusCode() !== 200, 'Response was ' . $this->response->getStatusCode());
    }

    /**
     * @When I login
     */
    public function iLogin()
    {
        throw new PendingException();
    }

    /**
     * @Then I am able to see the table
     */
    public function iAmAbleToSeeTheTable()
    {
        throw new PendingException();
    }
}

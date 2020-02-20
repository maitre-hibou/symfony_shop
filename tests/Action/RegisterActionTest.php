<?php

declare(strict_types=1);

namespace App\Tests\Action;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RegisterActionTest extends WebTestCase
{
    protected $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testRegistrationPage()
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/register');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Registration")')->count());
    }

    public function testRegistrationFormSubmission()
    {
        $this->client->request(Request::METHOD_GET, '/register');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->client->submitForm('Register now', [
            'register[email]' => 'test.user@example.com',
            'register[firstname]' => 'Test',
            'register[lastname]' => 'User',
            'register[password][first]' => 'Secret@12',
            'register[password][second]' => 'Secret@12',
            'register[conditionsAccepted]' => true,
        ]);

        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("You are now registered.")')->count());
    }

    public function testRegistrationFormSubmissionWithInvalidPassword()
    {
        $this->client->request(Request::METHOD_GET, '/register');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->submitForm('Register now', [
            'register[email]' => 'test.user@example.com',
            'register[firstname]' => 'Test',
            'register[lastname]' => 'User',
            'register[password][first]' => 'Secret12',
            'register[password][second]' => 'Secret²12',
            'register[conditionsAccepted]' => true,
        ]);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Password fields must match.")')->count());

        $crawler = $this->client->submitForm('Register now', [
            'register[email]' => 'test.user@example.com',
            'register[firstname]' => 'Test',
            'register[lastname]' => 'User',
            'register[password][first]' => 'Secret12',
            'register[password][second]' => 'Secret12',
            'register[conditionsAccepted]' => true,
        ]);

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Passwords must be at least 8 characters long")')->count());
    }
}

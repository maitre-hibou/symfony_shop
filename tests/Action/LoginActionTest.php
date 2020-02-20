<?php

declare(strict_types=1);

namespace App\Tests\Action;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoginActionTest extends WebTestCase
{
    protected $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testLoginPage()
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/login');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please sign in")')->count());
    }

    public function testLogin()
    {
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Sign in', [
            '_username' => 'user1@example.com',
            '_password' => 'Secret@1',
        ]);

        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Welcome, Foo")')->count());
    }

    public function testLoginWithAccountLocked()
    {
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Sign in', [
            '_username' => 'user4@example.com',
            '_password' => 'Secret@4',
        ]);

        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Account has expired.")')->count());
    }

    public function testLoginWithNotExistingAccount()
    {
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Sign in', [
            '_username' => 'unknown@example.com',
            '_password' => 'password',
        ]);

        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Invalid credentials.")')->count());
    }
}

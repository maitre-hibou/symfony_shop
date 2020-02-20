<?php

declare(strict_types=1);

namespace App\Tests\Admin;

use App\Tests\WebTestCase;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractAdminTest extends WebTestCase
{
    protected $client;

    protected function setUp(): void
    {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'superadmin@example.com',
            'PHP_AUTH_PW' => 'Secret@12',
            'HTTPS' => true,
        ]);
    }

    protected function listAllowed(string $name)
    {
        $crawler = $this->client->request(Request::METHOD_GET, sprintf('admin/%s/list', $name));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('table.sonata-ba-list')->count());
    }

    protected function createAllowed(string $name, array $data = [])
    {
        $crawler = $this->client->request(Request::METHOD_GET, sprintf('admin/%s/create', $name));
        $form = $crawler->selectButton('Create')->form();

        $this->setFormValues($form, $data);
        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
    }

    protected function editAllowed(string $name, int $id)
    {
        $crawler = $this->client->request('GET', sprintf('/admin/%s/%d/edit', $name, $id));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Update')->form();
        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
    }

    protected function deleteAllowed(string $name, int $id)
    {
        $crawler = $this->client->request('GET', sprintf('/admin/%s/%d/delete', $name, $id));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Yes, delete')->form();
        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
    }

    protected function setFormValue(Form $form, string $name, $value)
    {
        $keys = array_keys($form->getValues());

        foreach ($keys as $id => $key) {
            if (preg_match(sprintf('/^\w+\[%s\]$/', $name), $key)) {
                $form->setValues([$key => $value]);
                break;
            }
        }
    }

    protected function setFormValues(Form $form, array $values)
    {
        foreach ($values as $name => $value) {
            $this->setFormValue($form, $name, $value);
        }
    }
}

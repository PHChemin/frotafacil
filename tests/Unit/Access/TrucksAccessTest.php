<?php

namespace Tests\Unit\Access;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class TrucksAccessTest extends TestCase
{
    private Client $client;

    public function setup(): void
    {
        parent::setUp();
        $this->client = new Client([
            'allow_redirects' => false, // Disable following redirects
            'base_uri' => 'http://web:8080'
        ]);
    }

    public function test_should_not_access_the_index_route_if_not_authenticated(): void
    {
        $response = $this->client->get('/manager/fleets/1');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeaderLine('Location'));
    }

    public function test_should_not_access_the_show_trucks_route_if_not_authenticated(): void
    {
        $response = $this->client->get('/manager/fleets/1/trucks/1');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeaderLine('Location'));
    }

    public function test_should_not_access_the_new_trucks_route_if_not_authenticated(): void
    {
        $response = $this->client->get('/manager/fleets/1/trucks/new');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeaderLine('Location'));
    }

    public function test_should_not_access_the_create_trucks_route_if_not_authenticated(): void
    {
        $response = $this->client->post('/manager/fleets/1/trucks');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeaderLine('Location'));
    }

    public function test_should_not_access_the_edit_trucks_route_if_not_authenticated(): void
    {
        $response = $this->client->get('/manager/fleets/1/trucks/1/edit');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeaderLine('Location'));
    }

    public function test_should_not_access_the_update_trucks_route_if_not_authenticated(): void
    {
        $response = $this->client->put('/manager/fleets/1/trucks/1');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeaderLine('Location'));
    }
}

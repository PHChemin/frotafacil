<?php

namespace Tests\Unit\Access;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class DriversProfileAccessTest extends TestCase
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

    public function test_should_not_access_the_show_profile_route_if_not_authenticated(): void
    {
        $response = $this->client->get('/driver/profile');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeaderLine('Location'));
    }

    public function test_should_not_access_the_update_profile_route_if_not_authenticated(): void
    {
        $response = $this->client->put('/driver/profile/1');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeaderLine('Location'));
    }

    public function test_should_not_access_the_update_avatar_route_if_not_authenticated(): void
    {
        $response = $this->client->post('/driver/profile/avatar');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeaderLine('Location'));
    }
}

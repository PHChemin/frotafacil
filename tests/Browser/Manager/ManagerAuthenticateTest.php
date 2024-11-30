<?php

namespace Tests\Browser\Manager;

use PHPUnit\Framework\TestCase as FrameworkTestCase;

class ManagerAuthenticateTest extends FrameworkTestCase
{
    public function test_should_redirect_if_not_authenticated_to_index(): void
    {
        $page = file_get_contents('http://web/manager');

        $statusCode = $http_response_header[0];
        $location = $http_response_header[10];

        $this->assertEquals('HTTP/1.1 302 Found', $statusCode);
        $this->assertEquals('Location: /login', $location);
    }
}

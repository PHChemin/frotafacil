<?php

namespace Tests\Browser\Driver;

use App\Models\Driver;
use App\Models\User;
use Lib\Authentication\Auth;
use PHPUnit\Framework\TestCase as FrameworkTestCase;

class DriverAuthenticateTest extends FrameworkTestCase
{
    public function test_should_redirect_if_not_authenticated_to_index(): void
    {
        $page = file_get_contents('http://web/driver');

        $statusCode = $http_response_header[0];
        $location = $http_response_header[10];

        $this->assertEquals('HTTP/1.1 302 Found', $statusCode);
        $this->assertEquals('Location: /login', $location);
    }
}

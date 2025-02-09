<?php

namespace Tests\Unit\Controllers;

use App\Models\Driver;
use App\Models\User;
use Core\Constants\Constants;
use GuzzleHttp\Client;
use Symfony\Component\Finder\Finder;

class DriversProfileControllerTest extends ControllerTestCase
{
    private User $user;
    private Driver $driver;
    private string $avatarPath;
    private string $avatarUploadPath;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpDriver();
        $this->avatarPath = Constants::rootPath()->join('tests/files/avatar_test.png');
        $this->avatarUploadPath = Constants::rootPath()
            ->join('public/assets/uploads/drivers/' . $this->driver->id . '/avatar.png');

        $_SESSION['user']['id'] = $this->user->id;
    }

    private function setUpDriver(): void
    {
        $this->user = new User([
            'cpf' => '12345678901',
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);
        $this->user->save();

        $this->driver = new Driver([
            'user_id' => $this->user->id,
        ]);
        $this->driver->save();
    }

    /**
     * ====================== START OF TESTING ===============================================================
     */

    public function test_show(): void
    {
        $response = $this->get(action: 'show', controllerName: 'App\Controllers\DriversProfileController');

        $this->assertStringContainsString('Meu Perfil', $response);
    }

    public function test_successfully_update_profile(): void
    {
        $params = [
            'driver_id' => $this->driver->id,
            'driver' => [
                'gender' => 'F',
                'license_category' => ['A', 'B',]
            ]
        ];

        $response = $this->put(
            action: 'update',
            controllerName: 'App\Controllers\DriversProfileController',
            params: $params
        );

        $this->assertMatchesRegularExpression(
            "/Location: \/driver\/profile/",
            $response
        );
    }

    // Gender e license podem ser nulo então se enviar vazio OK!
    // Porém no front o botão é desabilidato, evitando que o usuário mande o formulário sem valor
    public function test_successfully_update_profile_with_empty_values(): void
    {
        $params = [
            'driver_id' => $this->driver->id,
            'driver' => [
                'gender' => '',
                'license_category' => []
            ]
        ];

        $response = $this->put(
            action: 'update',
            controllerName: 'App\Controllers\DriversProfileController',
            params: $params
        );

        $this->assertMatchesRegularExpression(
            "/Location: \/driver\/profile/",
            $response
        );
    }

    public function test_succesfully_update_avatar(): void
    {
        $cookieJar = new \GuzzleHttp\Cookie\CookieJar();

        $client = new Client([
            'allow_redirects' => false, // Disable following redirects
            'base_uri' => 'http://web:8080'
        ]);

        // Login first
        $resp = $client->post('/login', [
            'form_params' => [
                'user[cpf]' => '12345678901',
                'user[password]' => 'password123'
            ],
            'cookies' => $cookieJar
        ]);

        $response = $client->post('/driver/profile/avatar', [
            'multipart' => [
                [
                    'name' => 'user_avatar',
                    'contents' => fopen($this->avatarPath, 'r'),
                    'filename' => basename($this->avatarPath)
                ]
            ],
            'cookies' => $cookieJar
        ]);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/driver/profile', $response->getHeaderLine('Location'));

        $this->assertTrue(file_exists($this->avatarUploadPath));

        $this->cleanUp();
    }

    private function cleanUp(): void
    {
        unlink($this->avatarUploadPath);
        $driversFolder = Constants::rootPath()->join('public/assets/uploads/drivers');
        $this->removeDirectory($driversFolder);
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        // Get all files and subdirectories inside the directory
        $items = array_diff(scandir($dir), array('.', '..')); // Exclude '.' and '..'

        foreach ($items as $item) {
            $itemPath = $dir . DIRECTORY_SEPARATOR . $item;

            // If it's a directory, call the function recursively
            if (is_dir($itemPath)) {
                $this->removeDirectory($itemPath); // Recursively remove subdirectory
            } else {
                // If it's a file, delete it
                unlink($itemPath);
            }
        }

        // Once all files and subdirectories are deleted, remove the main directory
        rmdir($dir);
    }
}

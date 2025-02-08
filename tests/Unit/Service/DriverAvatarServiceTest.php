<?php

namespace Tests\Unit\Models\Drivers;

use App\Models\Driver;
use App\Models\User;
use App\Services\DriverAvatar;
use Tests\TestCase;

class DriverAvatarServiceTest extends TestCase
{
    private DriverAvatar $driverAvatar;
    private User $user;
    private Driver $driver;

    /** @var array<string, mixed> $image */
    private array $image;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpDriver();

        $tmpFile = tempnam(sys_get_temp_dir(), 'php');
        $this->image = [
            'name' => 'avatar_test.png',
            'full_path' => 'avatar_test.png',
            'type' => 'image/png',
            'tmp_name' => $tmpFile,
            'error' => 0,
            'size' => filesize($tmpFile),
        ];

        $this->driverAvatar = new DriverAvatar($this->driver, [
            'extension' => ['jpg', 'png', 'jpeg'],
            'size' => 2 * 1024 * 1024, // 2MB
        ]);
    }

    private function setUpDriver(): void
    {
        $this->user = new User([
            'cpf' => '12345678901',
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => 'password123',
        ]);
        $this->user->save();

        $this->driver = new Driver([
            'user_id' => $this->user->id,
        ]);
        $this->driver->save();
    }

    /**
     * ============================== START OF TESTING ===================================
     */

    public function testUpload(): void
    {
        // Create a mock using PHPUnit's MockBuilder
        $driverAvatar = $this->getMockBuilder(DriverAvatar::class)
            ->setConstructorArgs([$this->user, [
                'extension' => ['jpg', 'png'],
                'size' => 2 * 1024 * 1024,
            ]])
            ->onlyMethods(['updateFile'])
            ->getMock();

        // Configure the mock to return true for updateFile
        $driverAvatar->expects($this->once())
            ->method('updateFile')
            ->willReturn(true);

        /** @var DriverAvatar $driverAvatar */
        $result = $driverAvatar->update($this->image);
        $this->assertTrue($result);
    }


    public function testUpdateAvatarInvalidExtension(): void
    {
        $this->image['name'] = 'avatar.txt';
        $result = $this->driverAvatar->update($this->image);

        $this->assertFalse($result);
    }

    public function testUpdateAvatarInvalidSize(): void
    {
        $this->image['size'] = 3 * 1024 * 1024; // 3MB
        $result = $this->driverAvatar->update($this->image);

        $this->assertFalse($result);
    }
}

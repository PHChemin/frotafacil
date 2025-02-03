<?php

namespace Tests\Unit\Models\Trucks;

use App\Models\Truck;
use App\Models\Fleet;
use App\Models\Driver;
use App\Models\TruckBrand;
use App\Models\User;
use App\Models\Manager;
use Tests\TestCase;

class TruckTest extends TestCase
{
    private User $user;
    private User $user2;
    private Truck $truck;
    private Fleet $fleet;
    private Driver $driver;
    private TruckBrand $truckBrand;

    public function setUp(): void
    {
        parent::setUp();
        
        // Criando o usuário, manager e fleet
        $this->user = new User([
            'cpf' => '12345678901',
            'name' => 'Manager Test',
            'email' => 'manager@example.com',
            'password' => 'password123'
        ]);
        $this->user->save();

        $manager = new Manager(['user_id' => $this->user->id]);
        $manager->save();

        $this->fleet = new Fleet(['name' => 'Fleet of manager 1', 'manager_id' => $this->user->manager()->id]);
        $this->fleet->save();

        
        $this->user2 = new User([
            'cpf' => '98765432100',
            'name' => 'Driver Test',
            'email' => 'driver@example.com',
            'password' => 'password123'
        ]);
        $this->user2->save();

        $this->driver = new Driver(['user_id' => $this->user2->id]);
        $this->driver->save();

        $this->truckBrand = new TruckBrand(['name' => 'Scania']);
        $this->truckBrand->save();

        $this->truck = new Truck([
            'truck_brand_id' => $this->truckBrand->id,
            'model' => 'Model X',
            'color' => 'Red',
            'plate' => 'ABC1234',
            'fleet_id' => $this->fleet->id,
            'driver_id' => $this->driver->id,
        ]);
        $this->truck->save();
    }

    public function test_should_create_a_new_truck(): void
    {
        $truck = $this->fleet->trucks()->new([
            'truck_brand_id' => $this->truckBrand->id,
            'model' => 'Model Y',
            'color' => 'Blue',
            'plate' => 'DEF5678',
            'driver_id' => $this->driver->id,
        ]);
        $this->assertTrue($truck->save());
        $this->assertCount(2, Truck::all());
    }

    public function test_should_return_all_trucks(): void
    {
        $trucks[] = $this->truck;
        $trucks[] = $this->fleet->trucks()->new([
            'truck_brand_id' => $this->truckBrand->id,
            'model' => 'Model Y',
            'color' => 'Blue',
            'plate' => 'DEF5678',
            'driver_id' => $this->driver->id,
        ]);
        $trucks[1]->save();

        $all = Truck::all();
        $this->assertCount(2, $all);
        $this->assertEquals($trucks, $all);
    }

    public function test_destroy_should_delete_the_truck(): void
    {
        $truck2 = $this->fleet->trucks()->new([
            'truck_brand_id' => $this->truckBrand->id,
            'model' => 'Model Y',
            'color' => 'Blue',
            'plate' => 'DEF5678',
            'driver_id' => $this->driver->id,
        ]);
        $truck2->save();
        $this->assertCount(2, Truck::all());

        $truck2->destroy();
        $this->assertCount(1, Truck::all());
    }

    public function test_should_return_error_if_plate_is_empty(): void
    {
        $truck = $this->fleet->trucks()->new([
            'truck_brand_id' => $this->truckBrand->id,
            'model' => 'Model Z',
            'color' => 'Green',
            'plate' => '',
            'driver_id' => $this->driver->id,
        ]);

        $this->assertFalse($truck->isValid());
        $this->assertFalse($truck->save());
        $this->assertFalse($truck->hasErrors());

        $this->assertEquals('plate cannot be empty!', $truck->errors('plate'));
    }

    public function test_should_return_error_if_plate_is_duplicated(): void
    {
        $truck2 = $this->fleet->trucks()->new([
            'truck_brand_id' => $this->truckBrand->id,
            'model' => 'Model Z',
            'color' => 'Green',
            'plate' => 'ABC1234',
            'driver_id' => $this->driver->id,
        ]);
        $this->assertFalse($truck2->save());
        $this->assertEquals('plate has already been taken!', $truck2->errors('plate'));
    }

    public function test_should_return_error_if_driver_id_not_exist(): void
    {
        $truck = $this->fleet->trucks()->new([
            'truck_brand_id' => $this->truckBrand->id,
            'model' => 'Model Z',
            'color' => 'Green',
            'plate' => 'XYZ9876',
            'driver_id' => 100, // ID inexistente
        ]);

        $this->assertFalse($truck->save());
        $this->assertEquals('driver_id does not exist!', $truck->errors('driver_id'));
    }

    public function test_find_by_id_should_return_the_truck(): void
    {
        $truck2 = $this->fleet->trucks()->new([
            'truck_brand_id' => $this->truckBrand->id,
            'model' => 'Model Z',
            'color' => 'Green',
            'plate' => 'XYZ9876',
            'driver_id' => $this->driver->id,
        ]);
        $truck2->save();

        $this->assertEquals($this->truck, Truck::findById($this->truck->id));
        $this->assertEquals($truck2, Truck::findById($truck2->id));
    }

    public function test_find_by_id_should_return_null(): void
    {
        $this->assertNull(Truck::findById(10)); // ID inexistente
    }
}

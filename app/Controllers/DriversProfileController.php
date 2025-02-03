<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\FlashMessage;

class DriversProfileController extends Controller
{
    public function show(): void
    {
        $this->render('driver/profile/show');
    }

    public function update(Request $request): void
    {
        $params = $request->getParams();
        $driver = $this->current_user->driver();

        $driver->gender = $params['driver']['gender'];
        $driver->license_category = implode(',', $params['driver']['license_category']);

        if ($driver->save()) {
            FlashMessage::success('Perfil editado com sucesso!');
            $this->redirectTo(route('driver.profile.show'));
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor verifique!');
            $this->render('driver/profile/show');
        }
    }

    public function updateAvatar(): void
    {
        $image = $_FILES['user_avatar'];

        $this->current_user->driver()->avatar()->update($image);
        $this->redirectTo(route('driver.profile.show'));
    }
}

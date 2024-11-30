<?php

namespace App\Controllers;

use App\Models\User;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class AuthenticationsController extends Controller
{
    public function new(Request $request): void
    {
        $title = 'Login';
        $this->render('authentications/new', compact('title'), 'authLayout');
    }

    public function authenticate(Request $request): void
    {
        $params = $request->getParam('user');
        $user = User::findBy(['cpf' => $params['cpf']]);

        if ($user && $user->authenticate($params['password'])) {
            Auth::login($user);
            FlashMessage::success('Login realizado com sucesso!');
            if ($user->isManager()) {
                $this->redirectTo(route('manager.index'));
            } else {
                $this->redirectTo(route('driver.index'));
            }
        } else {
            FlashMessage::danger('Email e/ou senha inválidos!');
            $this->redirectTo(route('users.login'));
        }
    }

    public function checkLogin(Request $request): void
    {
        $user = Auth::user();

        if ($user) {
            if ($user->isManager()) {
                $this->redirectTo(route('manager.index'));
            } else {
                $this->redirectTo(route('driver.index'));
            }
        } else {
            $this->redirectTo(route('users.login'));
        }
    }

    public function destroy(): void
    {
        Auth::logout();
        FlashMessage::success('Logout realizado com sucesso!');
        $this->redirectTo(route('users.login'));
    }
}

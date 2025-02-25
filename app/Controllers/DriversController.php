<?php

namespace App\Controllers;

use App\Models\Truck;
use App\Models\User;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;

class DriversController extends Controller
{
    public function index(Request $request): void
    {
        $title = 'Página Inicial Motoristas';
        $this->render('driver/index', compact('title'));
    }

    public function findByCpf(Request $request): void
    {
        $params = $request->getParams();
        $cpf = $params['cpf'];

        if (!preg_match('/^\d{11}$/', $cpf)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'CPF inválido.']);
            return;
        }

        $user = User::findByCpf($cpf);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Nenhum usuário encontrado com esse CPF.']);
            return;
        }

        if (!$user->isDriver()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Este usuário não é um motorista.']);
            return;
        }

        if (Truck::driverHasTruck($user->driver()->id)) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Este motorista já está vinculado a um caminhão.']);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'driver_id' => $user->driver()->id,
            'message' => 'Motorista encontrado e disponível!',
        ]);
    }
}

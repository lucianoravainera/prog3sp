<?php

namespace Controllers;

use Models\User;
use Components\PassManager;
use Components\Token;
use Components\GenericResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginController
{
    public static function login(Request $request, Response $response, $args)
    {
        try {

            $username = $request->getParsedBody()['email'] ?? "";
            $pass =  $request->getParsedBody()['clave'] ?? "";
            $nombre = $request->getParsedBody()['nombre'] ?? "";

            if ((!empty($username) && !empty($pass)) || (!empty($nombre) && !empty($pass))) {
                $pass = PassManager::Hash($pass);
                $retrievedUser = User::whereRaw('LOWER(`email`) LIKE ?', [$username])->where('hash', $pass)->first();
                if(!$retrievedUser)
                $retrievedUser = User::whereRaw('LOWER(`nombre`) LIKE ?', [$nombre])->where('hash', $pass)->first();

                if ($retrievedUser != null) {
                    $token = Token::getToken($username, $retrievedUser->id, $retrievedUser->area);
                    $response->getBody()->write(GenericResponse::obtain(true, 'Bienvenido ' . $username, $token));
                } else {
                    $response->getBody()->write(GenericResponse::obtain(false, 'Credenciales invalidas'));
                }
            } else {
                $response->getBody()->write(GenericResponse::obtain(false, 'Email o password vacio'));
                $response->withStatus(401);
            }
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, "Error de autenticacion", null));
            $response->withStatus(500);
        }

        return $response;
    }

    public function obtenerRol(Request $request, Response $response, $args)
    {
        try {
            $token = $request->getHeaders()['token'] ?? "";
            $role = Token::obtenerRol($token);
            $response->getBody()->write(GenericResponse::obtain(true, '', $role));
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, "No se puede obtener el rol del usuario", null));
        }

        return $response;
    }

    public static function validateToken(Request $request, Response $response, $args)
    {
        $token = $request->getHeaders()['token'] ?? "";

        if (!empty($token)) {
            $isDecoded = Token::validateToken($token);
            $response->getBody()->write(GenericResponse::obtain($isDecoded, $isDecoded ? 'Token valido.' : 'Token ivalido', $token));
        } else {
            $response->getBody()->write(GenericResponse::obtain(false, 'Invalid credentials'));
        }

        return $response;
    }
}

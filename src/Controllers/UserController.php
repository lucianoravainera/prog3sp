<?php

namespace Controllers;

use Models\User;
use Components\PassManager;
use Components\GenericResponse;
use DateTime;
use Enum\UserRole;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    public function traerTodo(Request $request, Response $response, $args)
    {
        try {
            $users = User::all([
                'id',
                'email',
                'nombre',
                'area',
                'created_at',
                'updated_at'
            ]);

            $response->getBody()->write(GenericResponse::obtain(true, "", $users));
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, "Error al obtener la lista de usuarios", null));
            $response->withStatus(500);
        }

        return $response;
    }

    public function deleteOne(Request $request, Response $response, $args)
    {
        try {
            $id = $args['id'] ?? '';

            if (empty($id)) {
                $response->getBody()->write(GenericResponse::obtain(false, "ID no puede estar vacio", null));
                $response->withStatus(401);
            } else {
                $user = User::where('id', $id)->first();

                if ($user) {
                    $user->delete();
                    $response->getBody()->write(GenericResponse::obtain(true, "Usuario borrado", null));
                } else {
                    $response->getBody()->write(GenericResponse::obtain(true, "El usuario no exixte en las DB", null));
                }
            }
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, "Error al obtener lista USR", null));
            $response->withStatus(500);
        }

        return $response;
    }

    public function updateOne(Request $request, Response $response, $args)
    {
        try {
            $id = $args['id'] ?? '';

            if (empty($id)) {
                $response->getBody()->write(GenericResponse::obtain(false, "No se puede modificar usuario, id invalido", null));
                $response->withStatus(401);
            } else {

                $email = $request->getParsedBody()['email'] ?? null;
                $password = $request->getParsedBody()['password'] ?? null;
                $area = $request->getParsedBody()['area'] ?? null;

                if ($area != null && !UserRole::IsValidArea($area)) {
                    $response->getBody()->write(GenericResponse::obtain(true, "Area incorrecta", $area));
                    $response->withStatus(400);
                } else {
                    $user = User::where('id', $id)->first();

                    if (!empty($email))
                        $user->email = $email;

                    if (!empty($password))
                        $user->hash = PassManager::Hash($password);

                    if (!empty($area))
                        $user->area = $area;

                    $user->save();
                    $user->hash = null;

                    $response->getBody()->write(GenericResponse::obtain(true, "", $user));
                }
            }
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, "No se pueden obtener los usuarios", null));
            $response->withStatus(500);
        }

        return $response;
    }

    public function getOne(Request $request, Response $response, $args)
    {
        try {
            $user = User::where('id', $args['id'])->first([
                'id',
                'email',
                'area',
                'created_at',
                'updated_at'
            ]);

            $response->getBody()->write(GenericResponse::obtain(true, "", $user));
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, "No se pueden obtener los usuarios", null));
        }

        return $response;
    }

    public function getMinutes(Request $request, Response $response, $args)
    {
        try {

            $user = User::where('id', $args['id'])->first([
                'id',
                'email',
                'area',
                'created_at',
                'updated_at'
            ]);

            $createdAt = new DateTime($user->created_at);
            $updatedAt = new DateTime($user->updated_at);

            $diff = $updatedAt->getTimestamp() - $createdAt->getTimestamp();

            $response->getBody()->write(GenericResponse::obtain(true, "", $diff / 60));
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, "No se pueden obtener los usuarios", null));
        }

        return $response;
    }


    public function agregar(Request $request, Response $response, $args)
    {
        try {

            $nombre = $request->getParsedBody()['nombre'] ?? '';
            $password = $request->getParsedBody()['clave'] ?? '';
            $rol = $request->getParsedBody()['tipo'] ?? '';
            $email = $request->getParsedBody()['email'] ?? '';
            $area = UserRole::getVal($rol);

            if (!$nombre || User::where('nombre', $nombre)->exists()) {
                $response->getBody()->write(GenericResponse::obtain(true, "Nombre inválido o existente", $area));
                $response->withStatus(400);
            } else if (strpos($nombre, ' ')) {
                $response->getBody()->write(GenericResponse::obtain(true, "Nombre inválido contiene espacios", $area));
                $response->withStatus(400);
            } else if (strlen($password) < 4) {
                $response->getBody()->write(GenericResponse::obtain(true, "Error en la clave", $area));
                $response->withStatus(400);
            } else if (User::where('email', '=', $email)->exists()) {
                $response->getBody()->write(GenericResponse::obtain(true, "El usuario ya existe", $area));
                $response->withStatus(400);
            } else if (!UserRole::IsValidArea($area)) {
                $response->getBody()->write(GenericResponse::obtain(true, "Area invalida", $area));
                $response->withStatus(400);
            } else if (empty($email) || empty($password) || empty($area)) {
                $response->getBody()->write(GenericResponse::obtain(true, "Los datos ingresados son inválidos"));
                $response->withStatus(400);
            } else {
                $user = new User;
                $user->id = 0;
                $user->email = $email;
                $user->nombre = $nombre;
                $user->hash = PassManager::Hash($password);
                $user->area = $area;
                $user->save();
                $user->hash = null;
                $response->getBody()->write(GenericResponse::obtain(true, "Usuario agregado correctamente!!!", $user));
            }
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, "No se puede crear un nuevo usuario", null));
        }

        return $response;
    }
}

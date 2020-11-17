<?php

namespace Controllers;

use Components\Token;
use Models\Materia;
use Models\User;
use Models\Inscripcion;
use Components\GenericResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class InscripcionController
{
    public function agregar(Request $request, Response $response, $args)
    {
        try {
            $idMateria = $args['idMateria'] ?? '';
            $materia = Materia::where('id', $idMateria)->first();
            $idAlumno = Token::getId($token = $request->getHeaders()['token'] ?? "");

            if (empty($idMateria)) {
                $response->getBody()->write(GenericResponse::obtain(true, "idMateria no puede ser vacio"));
                $response->withStatus(400);
            } else if (!$materia) {
                $response->getBody()->write(GenericResponse::obtain(true, "materia inexistente!"));
                $response->withStatus(400);
            } else {

                $count = count(Inscripcion::where('id_materia', $idMateria)->get());

                if (Inscripcion::where('id_materia', $idMateria)->where('id_alumno', $idAlumno)->exists()) {
                    $response->getBody()->write(GenericResponse::obtain(true, "la inscripcion ya existe"));
                    $response->withStatus(400);
                } else if ($materia->cupos > $count) {
                    $inscrip = new Inscripcion();
                    $inscrip->id = 0;
                    $inscrip->id_materia = $idMateria;
                    $inscrip->id_alumno = $idAlumno;
                    $inscrip->save();
                    $response->getBody()->write(GenericResponse::obtain(true, "Materia agregada", $inscrip));
                } else {
                    $response->getBody()->write(GenericResponse::obtain(true, "cupo completo!!"));
                    $response->withStatus(400);
                }
            }
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, "Error creando la inscripcion", null));
        }

        return $response;
    }

    public function traerTodo(Request $request, Response $response, $args)
    {
        try {
            $idMateria = $args['idMateria'] ?? '';
            $materia = Materia::where('id', $idMateria)->first();

            if (empty($idMateria)) {
                $response->getBody()->write(GenericResponse::obtain(true, "idMateria vacio!"));
                $response->withStatus(400);
            } else if (!$materia) {
                $response->getBody()->write(GenericResponse::obtain(true, "materia inexistente"));
                $response->withStatus(400);
            } else {

                $all = User::select('users.id', 'users.nombre', 'users.email', 'users.area')
                                    ->join('inscripciones', 'users.id', '=', 'inscripciones.id_alumno')
                                    ->where([['id_materia', $idMateria]])
                                    ->get();

                    $response->getBody()->write(GenericResponse::obtain(true, "", $all));
          
            }
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, "no se puede obtener los alumnos", null));
        }

        return $response;
    }
}

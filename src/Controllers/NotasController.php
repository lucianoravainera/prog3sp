<?php

namespace Controllers;

use Models\Materia;
use Models\Nota;
use Models\User;
use Components\Token;
use Components\GenericResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NotasController
{
    public function agregar(Request $request, Response $response, $args)
    {
        try {

            $idMateria = $args['idMateria'] ?? '';
            $idAlumno =  $request->getParsedBody()['idAlumno'] ?? '';
            $nota =  $request->getParsedBody()['nota'] ?? '';
            $materia = Materia::where('id', $idMateria)->first();
            $alumno = User::where('id', $idAlumno)->first();

            if (empty($nota) || !($nota >= 0 && $nota <= 10)) {
                $response->getBody()->write(GenericResponse::obtain(true, "La nota debe ser entre 0 y 10"));
                $response->withStatus(400);
            } else if (empty($idMateria)) {
                $response->getBody()->write(GenericResponse::obtain(true, "idMateria no puede ser null"));
                $response->withStatus(400);
            } else if (!$materia) {
                $response->getBody()->write(GenericResponse::obtain(true, "Materia inexistente"));
                $response->withStatus(400);
            } else if (!$alumno) {
                $response->getBody()->write(GenericResponse::obtain(true, "Alumno inexistente"));
                $response->withStatus(400);
            } else {

                $dbNota = new Nota();
                $dbNota->id = 0;
                $dbNota->id_alumno = 1;
                $dbNota->id_materia = 2;
                $dbNota->nota = 4;
                $dbNota->save();

                $response->getBody()->write(GenericResponse::obtain(true, "Nota creada correctamente", $dbNota));
            }
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, "Error en nota (hasta aca llega-------------------------)", null));
        }

        return $response;
    }

}

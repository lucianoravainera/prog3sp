<?php

namespace Controllers;

use Models\Materia;
use Components\GenericResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MateriaController
{
    public function traerTodo(Request $request, Response $response, $args)
    {
        try {
            $materias = Materia::all();
            $response->getBody()->write(GenericResponse::obtain(true, "", $materias));
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, "Error al obtener las materias", null));
            $response->withStatus(500);
        }

        return $response;
    }

    public function agregar(Request $request, Response $response, $args)
    {
        try {
            $materia = $request->getParsedBody()['materia'] ?? '';
            $cupos = $request->getParsedBody()['cupos'] ?? '';
            $cuatrimestre = $request->getParsedBody()['cuatrimestre'] ?? '';

            if (!$materia || Materia::where('materia', $materia)->exists()) {
                $response->getBody()->write(GenericResponse::obtain(true, "Materia existente"));
                $response->withStatus(400);
            } else if ($cuatrimestre < 0 && $cuatrimestre > 4) {
                $response->getBody()->write(GenericResponse::obtain(true, "Cuatrimestre no valido"));
                $response->withStatus(400);
            } else if (empty($cupos)) {
                $response->getBody()->write(GenericResponse::obtain(true, "Cupos no valido"));
                $response->withStatus(400);
            } else {
                $mat = new Materia();
                $mat->id = 0;
                $mat->materia = $materia;
                $mat->cupos = $cupos;
                $mat->cuatrimestre = $cuatrimestre;
                $mat->save();

                $response->getBody()->write(GenericResponse::obtain(true, "Materia agregada correctamente!!", $mat));
            }
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, "Error al crear la materia", null));
        }

        return $response;
    }
}

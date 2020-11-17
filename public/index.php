<?php

use Enum\UserRole;

use Config\Database;
use Slim\Factory\AppFactory;
use Middlewares\JsonMiddleware;
use Slim\Routing\RouteCollectorProxy;
use Middlewares\Authentication\AuthMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Controllers\InscripcionController;
use Controllers\NotasController;
use Controllers\LoginController;
use Controllers\UserController;
use Controllers\MateriaController;
use Middlewares\FormDataMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$conn = new Database();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("API ok!");
    return $response;
});
$app->group('/users', function (RouteCollectorProxy $group) {
    $group->post('[/]', UserController::class . ":agregar");
});
$app->group('/login', function (RouteCollectorProxy $group) {
    $group->post('[/]', LoginController::class . ":login");
    $group->get('[/]', LoginController::class . ":obtenerRol")->add(new AuthMiddleware([UserRole::ADMIN]));
});


$app->group('/inscripcion', function (RouteCollectorProxy $group) {
    $group->post('[/]', InscripcionController::class . ":agregar")->add(new AuthMiddleware([UserRole::ALUMNO]));
    $group->get('/{idMateria}', InscripcionController::class . ":traerTodo")->add(new AuthMiddleware([UserRole::ADMIN, UserRole::PROFESOR]));
});

$app->group('/notas', function (RouteCollectorProxy $group) {
    $group->put('/{idMateria}', NotasController::class . ":agregar")->add(new AuthMiddleware([UserRole::PROFESOR]));


});
$app->group('/materia', function (RouteCollectorProxy $group) {
    $group->post('[/]', MateriaController::class . ":agregar")->add(new AuthMiddleware([UserRole::ADMIN]));
    $group->get('[/]', MateriaController::class . ":traerTodo")->add(new AuthMiddleware([UserRole::ADMIN, UserRole::PROFESOR, UserRole::ALUMNO]));
});




$app->add(new JsonMiddleware());
$app->addBodyParsingMiddleware();

$app->run();

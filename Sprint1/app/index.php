<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
// require_once './middlewares/Logger.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/LoginController.php';
require_once './middlewares/MWPerfil.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Set base path 
//$app->setBasePath('/Programacion3/Clase06/pruebaHeroku/slim-php-mysql-heroku');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
    $group->delete('/{usuarioId}', \UsuarioController::class . ':BorrarUno');
    $group->put('/{usuarioId}', \UsuarioController::class . ':ModificarUno');
  })->add(new MWPerfil());

//)->add(new MWPerfil()

$app->post('/login', \LoginController::class . ':UsuarioLogin');

$app->get('[/]', function (Request $request, Response $response) {    
  $response->getBody()->write("Slim Framework 4 PHP Agnoli Luca");
  return $response;
   /* $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');*/
});

$app->run();

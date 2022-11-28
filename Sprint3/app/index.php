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

require_once './controllers/EmpleadoController.php';
require_once './controllers/LoginController.php';
require_once './controllers/ArticuloController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ComandaController.php';
require_once './controllers/EncargoController.php';
require_once './controllers/EncuestaController.php';

require_once './middlewares/MWPerfilSocio.php';
require_once './middlewares/MWPerfilMozo.php';
require_once './middlewares/MWLogin.php';

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
$app->group('/empleados', function (RouteCollectorProxy $group) {
    $group->get('[/]', \EmpleadoController::class . ':TraerTodos');
    $group->get('/{id}', \EmpleadoController::class . ':TraerUno');
    $group->post('[/]', \EmpleadoController::class . ':CargarUno');
    $group->delete('/{usuarioId}', \EmpleadoController::class . ':BorrarUno');
    $group->put('/{usuarioId}', \EmpleadoController::class . ':ModificarUno');
  })->add(new MWPerfilSocio());

$app->group('/articulos', function (RouteCollectorProxy $group)
{
  $group->post('[/]', \ArticuloController::class . ':CargarUno')->add(new MWPerfilSocio());
  $group->get('[/]', \ArticuloController::class . ':TraerTodos');
  $group->get('/crearArchivoCsvArticulos', \ArticuloController::class . ':crearArchivoCsvArticulos');
  $group->post('/cargarTablaSqlConCsv', \ArticuloController::class . ':cargarTablaSqlConCsv');
});

$app->group('/mesas', function (RouteCollectorProxy $group)
{
  $group->put('/{mesaCodigo}', \MesaController::class . ':actualizarMesa')->add(new MWPerfilMozo());
  $group->put('/cargar/mesa', \MesaController::class . ':cargarUno')->add(new MWPerfilMozo());
  $group->get('/', \MesaController::class . ':traerTodos')->add(new MWPerfilSocio());
  $group->get('/mesaMasUsada', \MesaController::class . ':obtenerMesaMasUtilizada')->add(new MWPerfilSocio());
  $group->put('/cerrar/mesa', \MesaController::class . ':CerrarMesa')->add(new MWPerfilSocio());
});


$app->group('/comandas', function (RouteCollectorProxy $group)
{
  $group->post('/', \ComandaController::class . ':CargarUno')->add(new MWPerfilMozo());
  $group->get('/traerTodos', \ComandaController::class . ':TraerTodos')->add(new MWPerfilMozo());
  $group->put('/entregarCuenta', \ComandaController::class . ':EntregarCuenta')->add(new MWPerfilMozo());
  $group->put('/pedirCuenta', \ComandaController::class . ':PedirCuenta');
  $group->get('/pedirDemora', \ComandaController::class . ':MostrarDatosComandaUsuario');
});

$app->group('/encargos', function (RouteCollectorProxy $group)
{
  $group->post('/altaEncargo', \EncargoController::class . ':CargarUno')->add(new MWPerfilMozo());
  $group->put('/tomarEncargo', \EncargoController::class . ':TomarEncargo');
  $group->put('/terminarEncargo', \EncargoController::class . ':TerminarEncargo');
  $group->put('/entregarEncargos', \EncargoController::class . ':EntregarEncargos')->add(new MWPerfilMozo());
  $group->get('[/]', \EncargoController::class . ':TraerTodos')->add(new MWPerfilSocio());
  $group->get('/traerPendientes', \EncargoController::class . ':obtenerEncargos')->add(new MWPerfilSocio());
  $group->get('/traerPreparacion', \EncargoController::class . ':TraerEnPreparacion')->add(new MWPerfilSocio());
  $group->get('/TraerListoParaServir', \EncargoController::class . ':TraerListoParaServir')->add(new MWPerfilMozo());
})->add(new MWLogin());

$app->group('/encuesta', function (RouteCollectorProxy $group)
{
  $group->get('/obtenerComentariosPositivos', \EncuestaController::class . ':obtenerComentariosPositivos')->add(new MWPerfilSocio());
})->add(new MWLogin());

$app->post('/generarEncuesta', \EncuestaController::class . ':CargarEncuesta');

$app->post('/login', \LoginController::class . ':UsuarioLogin');

$app->get('[/]', function (Request $request, Response $response) {    
  $response->getBody()->write("Slim Framework 4 PHP Agnoli Luca");
  return $response;
   /* $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');*/
});

$app->run();

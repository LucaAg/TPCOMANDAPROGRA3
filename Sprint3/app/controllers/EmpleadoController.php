<?php
require_once './models/Empleado.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');
class EmpleadoController extends Empleado implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($_POST['tipoEmpleado']) && isset($_POST['clave']) && isset($_POST['nombreCompleto']) && isset($_POST['esSocio']))
        { 
          $tipoEmpleado = $parametros['tipoEmpleado'];
          $clave = $parametros['clave'];
          $nombreCompleto = $parametros['nombreCompleto'];
          $esSocio = $parametros['esSocio'];
          
          if(EmpleadoController::checkearTipoEmpleado($tipoEmpleado))
          { 
            $empleado = new Empleado();
            $empleado->tipoEmpleado = $tipoEmpleado;
            $empleado->clave = $clave;
            $empleado->nombreCompleto = $nombreCompleto;
            $empleado->esSocio = $esSocio;
            
            if($empleado->crearEmpleado())
            {
              $payload = json_encode(array("mensaje" => "Empleado creado con exito"));
            }
            else
            {
              $payload = json_encode(array("mensaje" => "Error al crear al Empleado!"));
            }
          }
          else
          {
            $payload = json_encode(array("Error" => "El tipo de empleado permitido es: Socio/Cervecero/Bartender/Mozo"));
          }
         
        }
        
       
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function checkearTipoEmpleado($tipo)
    {
      $todoOk = false;
      if($tipo == "Socio" || $tipo == "Cervecero" || $tipo == "Bartender" || $tipo == "Mozo" || $tipo == "Cocinero")
      {
        $todoOk = true;
      }
      return $todoOk;
    }

    public function TraerUno($request, $response, $args)
    {
        $empleadoId = $args['id'];
        $empleado = Empleado::obtenerEmpleadoId($empleadoId);
        $payload = json_encode($empleado);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Empleado::obtenerTodos();
        $payload = json_encode(array("listaEmpleados" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['tipoEmpleado']) && isset($parametros['clave']) && isset($parametros['nombreCompleto']) && isset($parametros['esSocio']))
        {
          $tipoEmpleado = $parametros['tipoEmpleado'];
          $clave = $parametros['clave'];
          $nombreCompleto = $parametros['nombreCompleto'];
          $esSocio = $parametros['esSocio'];

          $empleado = new empleado();
          $empleado->id = $args['empleadoId'];
          $empleado->tipoEmpleado = $tipoEmpleado;
          $empleado->clave = $clave;
          $empleado->nombreCompleto = $nombreCompleto;
          $empleado->esSocio = $esSocio;
          if(Empleado::modificarEmpleado($empleado))
          {
            $payload = json_encode(array("mensaje" => "Empleado modificado con exito"));
          }
          else{
            $payload = json_encode(array("mensaje" => "Error al modificar al empleado"));
          }
  
        }     

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $empleadoId = $args['empleadoId'];
        if(Empleado::borrarEmpleado($empleadoId))
        {
          $payload = json_encode(array("mensaje" => "Empleado borrado con exito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "Error al intentar borrar al empleado!"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}

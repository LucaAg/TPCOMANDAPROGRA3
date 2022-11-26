<?php
require_once './models/Encargo.php';
require_once './models/Empleado.php';
require_once './interfaces/IApiUsable.php';

class EncargoController extends Encargo implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($_POST['codigoComanda']) && isset($_POST['idArticulo']) && isset($_POST['idEmpleado']))
        { 
          $codigoComanda = $parametros['codigoComanda'];
          if(!is_bool(Comanda::obtenerComandaCodigo($codigoComanda))) //checkear estado comanda
          {
            $idArticulo = $parametros['idArticulo'];
            $idEmpleado = $parametros['idEmpleado'];

            $encargo = new Encargo();
            $encargo->codigoComanda = $codigoComanda;
            $encargo->idArticulo = $idArticulo;
            $encargo->idEmpleado = $idEmpleado;
            
            if($encargo->crearEncargo())
            {
              $payload = json_encode(array("mensaje" => "Encargo creado con exito"));
            }
            else
            {
              $payload = json_encode(array("mensaje" => "Error al crear el encargo!"));
            }
          }
          else
          {
            $payload = json_encode(array("Error" => "La comanda no existe o no esta disponible!"));
          }
          
        }
        
       
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        /*$encargoId = $args['id'];
        $encargo = Encargo::obtenerUno($encargo);
        $payload = json_encode($empleado);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');*/
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Empleado::obtenerTodos();
        $payload = json_encode(array("listaEncargos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['estadoEncargo']) && isset($parametros['tiempoEstimado']) && isset($parametros['codigoComanda']) && isset($parametros['idEmpleado']) 
        && isset($parametros['idArticulo']))
        {
          $estadoEncargo = $parametros['estadoEncargo'];
          $tiempoEstimado = $parametros['tiempoEstimado'];
          $codigoComanda = $parametros['codigoComanda'];
          $idEmpleado = $parametros['idEmpleado'];
          $idArticulo = $parametros['idArticulo'];

          $encargo = new Encargo();
          $encargo->id = $args['encargoId'];
          $encargo->estadoEncargo = $estadoEncargo;
          $encargo->tiempoEstimado = $tiempoEstimado;
          $encargo->codigoComanda = $codigoComanda;
          $encargo->idEmpleado = $idEmpleado;
          $encargo->idArticulo = $idArticulo;
          if(Encargo::modificarEncargo($encargo))
          {
            $payload = json_encode(array("mensaje" => "Encargo modificado con exito"));
          }
          else{
            $payload = json_encode(array("mensaje" => "Error al modificar el encargo"));
          }
  
        }     

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $encargoId = $args['encargoId'];
        if(Encargo::borrarEncargo($encargoId))
        {
          $payload = json_encode(array("mensaje" => "Encargo borrado con exito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "Error al intentar borrar el encargo!"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TomarEncargo($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      $payload = json_encode(array("mensaje" => "Faltan datos!"));
      if(isset($parametros['idEncargo']) && isset($parametros['tiempoEstimado']) && isset($parametros['estadoEncargo']))
      {
        $idEncargo = $parametros['idEncargo'];
        $tiempoEstimado = $parametros['tiempoEstimado'];
        $estadoEncargo = $parametros['estadoEncargo'];
        $encargo = Encargo::obtenerEscargoId($idEncargo);
        $empleado = Empleado::obtenerEmpleadoId($encargo->idEmpleado);
        var_dump($encargo);

        if(Encargo::modificarEstadoEncargoDuracion($encargo,$estadoEncargo,$tiempoEstimado))
        {
          $payload = json_encode(array("mensaje" => "Encargo a cargo de $empleado->nombreCompleto"));
        }else
        {
          $payload = json_encode(array("Error" => "El empleado no pudo tomar el encargo!"));
        }
        
      }
      $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TerminarEncargo($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      $payload = json_encode(array("mensaje" => "Faltan datos!"));
      if(isset($parametros['idEncargo']) && isset($parametros['estadoEncargo']))
      {
        $idEncargo = $parametros['idEncargo'];
        $tiempoEstimado = NULL;
        $estadoEncargo = $parametros['estadoEncargo'];
        $encargo = Encargo::obtenerEscargoId($idEncargo);
        $empleado = Empleado::obtenerEmpleadoId($encargo->idEmpleado);

        if(Encargo::modificarEstadoEncargoDuracion($encargo,$estadoEncargo,$tiempoEstimado))
        {
          $payload = json_encode(array("mensaje" => "Encargo a cargo de $empleado->nombreCompleto"));
        }else
        {
          $payload = json_encode(array("Error" => "El empleado no pudo tomar el encargo!"));
        }
        
      }
      $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}

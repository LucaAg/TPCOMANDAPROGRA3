<?php

use GuzzleHttp\Psr7\Message;

require_once './models/Encargo.php';
require_once './models/Empleado.php';
require_once './models/Articulo.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

class EncargoController extends Encargo implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($_POST['codigoComanda']) && isset($_POST['idArticulo']))
        { 
          $codigoComanda = $parametros['codigoComanda'];
          $comanda = Comanda::obtenerComandaCodigo($codigoComanda);
          if(!is_bool($comanda) && $comanda->estadoComanda != "Finalizado")
          {
            $mesa = Mesa::obtenerMesaCodigo($comanda->codigoMesa);
            $idArticulo = $parametros['idArticulo'];
            $articulo = Articulo::obtenerArticulo($idArticulo);
            if(!is_bool($articulo))
            {
              $encargo = new Encargo();
              $encargo->codigoComanda = $codigoComanda;
              $encargo->idArticulo = $idArticulo;
              
              if(!is_bool($encargo->crearEncargo()))
              {
                $payload = json_encode(array("mensaje" => "Encargo creado con exito", "Articulo" => "Pedido solicitado: $articulo->nombreArticulo"));  
                Mesa::actualizarEstadoMesa($mesa,"Cliente esperando pedido");            
              }
              else
              {
                $payload = json_encode(array("mensaje" => "Error al crear el encargo!"));
              }
            }
            else
            {
              $payload = json_encode(array("Error" => "El articulo ingresado no existe!"));
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
        $lista = Encargo::obtenerTodos();
        $payload = json_encode(array("listaEncargos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $payload = json_encode(array("mensaje" => "Faltan datos!"));
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
        if(Encargo::cancelarEncargo($encargoId))
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

    public function obtenerEncargos($request, $response, $args)
    {
      $idEmpleado = EncargoController::obtenerIdEmpleado($request);
      $empleado = Empleado::obtenerEmpleadoId($idEmpleado);
      if(!is_bool($empleado))
      {
        if($empleado->esSocio == "Si")
        {
          $lista = Encargo::obtenerEncargosPendientes();
          
          $payload = json_encode(array("mensaje" => "El socio tiene acceso a todos los encargos","listaEncargos" => $lista));
          if(empty($lista))
          {
            $payload = json_encode(array("mensaje" => "No hay pendientes disponibles",));
          }
        }
        else
        {
          $lista = Encargo::obtenerEncargosPendientesPorTipo("Pendiente",$empleado->tipoEmpleado);
          $payload = json_encode(array("Perfil"=>"$empleado->tipoEmpleado","listaEncargos" => $lista));
          if(empty($lista))
          {
            $payload = json_encode(array("mensaje" => "No hay pendientes disponibles",));
          }
        }
      }
      else
      {
        $payload = json_encode(array("mensaje" => "Error el empleado no existe!"));
      }

      
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public static function obtenerIdEmpleado($request)
    {
      
      $header = $request->getHeaderLine('Authorization');
      $token = trim(explode("Bearer", $header) [1]);
      $datosToken = AutentificadorJWT::ObtenerData($token);
          
      return $datosToken->id;
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

        $idEmpleado = EncargoController::obtenerIdEmpleado($request);
        $empleado = Empleado::obtenerEmpleadoId($idEmpleado);
        $encargo = Encargo::obtenerEscargoId($idEncargo);
        $articulo = Articulo::obtenerArticuloEncargo($encargo);
        $comanda = Comanda::obtenerComandaEncargo($encargo);
        $horaInicio =date('Y-m-d H:i:s');
        if($encargo->estadoEncargo == "Pendiente")
        {
          if($articulo->cargoEmpleado == $empleado->tipoEmpleado)
          {
            if(Encargo::modificarEncargoConId($encargo,$estadoEncargo,$tiempoEstimado,$idEmpleado,$horaInicio))
            {
              $payload = json_encode(array("mensaje" => "Encargo a cargo de $empleado->nombreCompleto"));
              Comanda::actualizarEstadoComanda($comanda,"En preparacion");
            }
            else
            {
              $payload = json_encode(array("Error" => "El empleado no pudo tomar el encargo!"));
            }
          }
          else
          {
            $payload = json_encode(array("Error" => "El cargo no corresponde al tipo $empleado->tipoEmpleado"));
          }       
        }
        else
        {
          $payload = json_encode(array("Error" => "El cargo no esta pendiente"));
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
        $estadoEncargo = $parametros['estadoEncargo'];
        $encargo = Encargo::obtenerEscargoId($idEncargo);
        $empleadoId = EncargoController::obtenerIdEmpleado($request);
        $empleado = Empleado::obtenerEmpleadoId($empleadoId);
        $articulo = Articulo::obtenerArticuloEncargo($encargo);
        if($articulo->cargoEmpleado == $empleado->tipoEmpleado)
        {
          if($empleado->id == $encargo->idEmpleado)
          {
            if($encargo->estadoEncargo == "En preparacion")
            {
              $horaFin = date('Y-m-d H:i:s');
              if(Encargo::modificarEstadoEncargoEstadoDuracion($encargo,$estadoEncargo,$encargo->tiempoEstimado,$horaFin))
              {
                $payload = json_encode(array("mensaje" => "Encargo finalizado por $empleado->nombreCompleto"));
              }else
              {
                $payload = json_encode(array("Error" => "El empleado no pudo tomar el encargo!"));
              }
            }
            else
            {
              $payload = json_encode(array("Error" => "El encargo no fue dado de alta o ya fue terminado!"));
            }
          }
          else
          {
            $payload = json_encode(array("Error" => "El encargo no corresponde a este empleado"));
          }
        }
        else
        {
          $payload = json_encode(array("Error" => "El cargo no corresponde al tipo $empleado->tipoEmpleado"));
        }
              
      }
      $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function EntregarEncargos($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      $payload = json_encode(array("mensaje" => "Faltan datos!"));
      if(isset($parametros['codigoComanda']) && isset($parametros['estadoEncargo']))
      {
        $codigoComanda = $parametros['codigoComanda'];
        $estado = $parametros['estadoEncargo'];

        $encargosPorCodigo = Encargo::obtenerEncargosPorCodigoComanda($codigoComanda);
        $encargosPorEstadoCodigo = Encargo::obtenerEncargosPorCodigoYEstado($codigoComanda,"Listo para servir");
        $platosRestantes = count($encargosPorCodigo) - count($encargosPorEstadoCodigo);
        if($platosRestantes == 0)
        {
          Encargo::modificarEstadoPorComanda($codigoComanda,$estado);
          $comanda = Comanda::obtenerComandaCodigo($codigoComanda);
          $mesa = Mesa::obtenerMesaCodigo($comanda->codigoMesa);
          Comanda::actualizarEstadoComanda($comanda,"Pedido entregado");
          Mesa::actualizarEstadoMesa($mesa,"Con cliente comiendo");
          $payload = json_encode(array("mensaje" => "Se ha concretado la entrega de la comanda $codigoComanda"));
        }
        else
        {
          $payload = json_encode(array("Error" => "Faltan completar $platosRestantes encargo(s) para entregar el pedido!"));
        }
      }
      $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}

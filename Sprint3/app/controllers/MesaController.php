<?php
require_once './models/Mesa.php';
require_once './models/Empleado.php';
class MesaController extends Mesa
{
    public function cargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $payload = json_encode(array("Error" => "Faltan datos!"));
        if(isset($parametros['codigoMesa']))
        {
            $idMozo = MesaController::obtenerIdMozo($request);
            $codigo = $parametros['codigoMesa'];
            $nuevaMesa = new Mesa();      
            $nuevaMesa->codigoMesa = $codigo;
            $nuevaMesa->idMozo = $idMozo;
            if(!is_bool(Mesa::obtenerMesaCodigo($codigo)))
            {
                if(!is_bool($nuevaMesa->altaMesa()))
                {
                    $empleado = Empleado::obtenerEmpleadoId($idMozo);
                    $payload = json_encode(array("Mesa" => "Mesa dada de alta exitosamente!","Empleado" => "Empleado a cargo: $empleado->nombreCompleto"));
                }
                else
                {
                    $payload = json_encode(array("Error" => "Error al crear la mesa!"));
                }     
            }
            else
            {
                $payload = json_encode(array("Error" => "El codigo de mesa ingresado no existe!"));
            }
            
                
        }
        
       
       
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function obtenerIdMozo($request)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header) [1]);
        $datosToken = AutentificadorJWT::ObtenerData($token);
        return $datosToken->id;
    }

    public function traerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function traerPorId($request, $response, $args)
    {
      $codigo = $args['codigo'];
      $payload = json_encode(array("Error" => "Faltan datos"));
      if(isset($id))
      {          
        if(!is_null($id))
        {
          $mesaBuscada = Mesa::obtenerMesaCodigo(intval($id));
          if($mesaBuscada)
          {
            $payload = json_encode(array("Mesa" => $mesaBuscada));
          }
          else
          {
            $payload = json_encode(array("Error" => "No hay mesas con esta ID"));
          }
        }
        else
        {
          $payload = json_encode(array("Error" => "ID no valida!"));
        }
        
      }
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public static function obtenerMesaEstado($request,$response,$args)
    {
        $parametros = $request->getParsedBody();      
        if(isset($parametros['estadoMesa']))
        {
            $estadoMesa = $args['estadoMesa'];
            if(MesaController::validarEstadoMesa($estadoMesa))
            {
                $mesaBuscada = Mesa::obtenerMesasPorEstado($estadoMesa);
                if($mesaBuscada)
                {
                    $payload = json_encode(array("Mesa" => $mesaBuscada));
                }
                else
                {
                    $payload = json_encode(array("Error" => "No hay mesas con este estado"));
                }
            }
            else
            {
                $payload = json_encode(array("Error" => "Estado de mesa incorrecto"));
            }          
        }
        else
        {
            $payload = json_encode(array("Error" => "Faltan datos!"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public static function actualizarMesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigo = $args['mesaCodigo'];    
        if(isset($parametros['estadoMesa']))
        {
            $estado = $parametros['estadoMesa'];           
            $mesaBuscada = Mesa::obtenerMesaCodigo($codigo);
            if(!is_bool($mesaBuscada))
            {    
                if(Mesa::actualizarEstadoMesa($mesaBuscada,$estado))
                {
                    $payload = json_encode(array( "Actualizar mesa" => "Mesa actualizada exitosamente!"));
                }
                else
                {
                    $payload = json_encode(array( "Actualizar mesa" => "Error al Actualizar la mesa!"));
                } 
            }  
            else
            {
                $payload = json_encode(array("Error" => "Error inesperado"));
            }               
        }
        else
        {
            $payload = json_encode(array("Error" => "Faltan datos!"));
        }    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public static function CerrarMesa($request,$response,$args)
    {
      $parametros = $request->getParsedBody();
      $payload = json_encode(array("Error" => "Faltan datos!"));
      if(isset($parametros['codigoComanda']))
      {
        $codigo = $parametros['codigoComanda'];
        $comanda = Comanda::obtenerComandaCodigo($codigo);
        if(!is_bool($comanda))
        {
          $mesa = Mesa::obtenerMesaCodigo($comanda->codigoMesa);
          if(!is_bool($mesa))
          { 
            if($mesa->estado == "Con cliente pagando")
            {
                $mesa->idMozo = NULL;
                Mesa::actualizarEstadoMesa($mesa,"Cerrado");
                Comanda::actualizarEstadoComanda($comanda,"Finalizada");
                $payload = json_encode(array("Mensaje" => "La mesa $mesa->codigoMesa se ha cerrado satisfactoriamente!"));
            }
            else
            {
                $payload = json_encode(array("Error" => "No se puede cerrar una mesa sin que los clientes paguen la mesa"));
            }
           
          }
          else
          {
            $payload = json_encode(array("Error" => "La mesa no existe!"));
          }
        }
        else
        {
          $payload = json_encode(array("Error" => "La comanda con codigo $codigo no existe"));
        }
      }
      $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    

    public static function validarEstadoMesa($estado)
    {
        $arrayFrases = array("Con cliente esperando pedido","Con cliente comiendo",
        "con cliente pagando","cerrada");
        $todoOk = false;
        if(in_array($estado,$arrayFrases))
        {
            $todoOk = true;
        }
        return $todoOk;
    }

    public static function obtenerMesaMasUtilizada($request,$response,$args)
    {
        $payload = json_encode(array("mensaje" => Comanda::obtenerMesaMasUsada()));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
?>
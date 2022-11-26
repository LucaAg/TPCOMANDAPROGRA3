<?php
require_once './models/Mesa.php';
require_once './models/Empleado.php';
class MesaController extends Mesa
{
   /* public function cargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($_POST['idMozo']) && isset($_POST['estadoMesa']))
        {
            $nuevaMesa = new Mesa();
        
            if($nuevaMesa->crearMesa())
            {
                $payload = json_encode(array("Mesa" => "Mesa dada de alta exitosamente!"));
            }
            else
            {
                $payload = json_encode(array("Error" => "Error al crear la mesa!"));
            }     
        }     
       
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }*/

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
    
   /* public static function modificarEstadoMesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $args['mesaId'];
        if(isset($parametros['estadoMesa']))
        {
            $estado = $parametros['estadoMesa'];
            if(MesaController::validarEstadoMesa($estado))
            {
                $mesaBuscada = Mesa::obtenerMesaId($id);
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
    }*/

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
}
?>
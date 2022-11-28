<?php
require_once './models/Empleado.php';
require_once './models/Mesa.php';
require_once './models/Comanda.php';
require_once './interfaces/IApiUsable.php';

class ComandaController extends Comanda implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $payload = json_encode(array("Error" => "Faltan datos!"));
        if(isset($_POST['codigoMesa'])  && isset($_POST['nombreCliente']))
        { 
          $codigo = Comanda::crearCodigo();
          $codigoMesa = $parametros['codigoMesa'];
          $nombreCliente = $parametros['nombreCliente'];
          $destinoImagen = NULL;
          //$comandas = Comanda::obtenerComandasMesaCodigo($codigoMesa);
          if(ComandaController::checkearComandasFinalizadasPorMesa($codigoMesa))
          {
            $idEmpleado = ComandaController::obtenerIdEmpleado($request);
            if(!is_bool(Mesa::obtenerMesaCodigo($codigoMesa)))
            {
              if(Mesa::CheckearMozoAsignado($idEmpleado,$codigoMesa))
              {
                $comanda = new Comanda();
                if(isset($_FILES['imagenComanda']))
                {
                  $ruta = '../Media/Comandas/';
                  $archivoExtension = pathinfo($_FILES["imagenComanda"]["name"], PATHINFO_EXTENSION);
                  $nombreImagen = Comanda::crearNombreImagenComanda($archivoExtension,$codigo,$codigoMesa,$nombreCliente);               
                  $destinoImagen = Comanda::crearDestinoImagenComanda($nombreImagen,$ruta);
                  $comanda->imagenComanda = $destinoImagen;
                }
                else
                {
                  $comanda->imagenComanda = $destinoImagen;
                }
      
                $comanda->codigo = $codigo;
                $comanda->codigoMesa = $codigoMesa;
                $comanda->nombreCliente = $nombreCliente;
                
                if(!is_bool($comanda->crearComanda()))
                {
                  $payload = json_encode(array("mensaje" => "Comanda creada con exito", "Codigo" => "Comanda: $comanda->codigo"));
                }
                else
                {
                  $payload = json_encode(array("mensaje" => "Error al crear la comanda!"));
                }
              }
              else
              {
                $payload = json_encode(array("Error" => "Debe tener solo un mozo, y debe ser el asignado!"));
              }
            }
            else
            {
              $payload = json_encode(array("Error" => "La mesa no existe!"));
            }
          }
          else
          {
            $payload = json_encode(array("Error" => "Ya hay una comanda activa en este mesa o no esta disponible!"));
          }
          
             
        }      
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function checkearComandasFinalizadasPorMesa($codigoMesa)
    {
      $comandas = COmanda::obtenerComandasMesaCodigo($codigoMesa);
      $todoOk = true;
      if(!empty($comandas))
      {
        foreach($comandas as $comanda)
        {
          if($comanda->estadoComanda != "Finalizada")
          {
            $todoOk = false;
            break;
          }
        }
      }
      return $todoOk;
    }

    public static function obtenerIdEmpleado($request)
    {
      
      $header = $request->getHeaderLine('Authorization');
      $token = trim(explode("Bearer", $header) [1]);
      $datosToken = AutentificadorJWT::ObtenerData($token);
          
      return $datosToken->id;
    }

    public function TraerUno($request, $response, $args)
    {
        $comandaCodigo = $args['codigo'];
        $comanda = Comanda::obtenerComandaCodigo($comandaCodigo);
        if(!is_bool($comanda))
        {
            $payload = json_encode($comanda);
        }
        else
        {
            $payload = json_encode("Error", "La comanda no existe!");
        }
        

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Comanda::obtenerTodos();
        if(!is_bool($lista))
        {
            $payload = json_encode(array("listaEmpleados" => $lista));
        }
        else
        {
            $payload = json_encode("Error", "Error inesperado al traer la lista");
        }
       

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['estado']) && isset($parametros['codigoMesa']) && isset($parametros['nombreCliente']) && isset($parametros['precio']))
        {
          $estado = $parametros['estado'];
          $nombreCliente = $parametros['nombreCliente'];
          $precio = $parametros['precio'];
          $codigoMesa = $parametros['codigoMesa'];
          $imagenComanda = NULL;

          $codigoComanda = $args['codigoComanda'];
          if(!is_bool(Comanda::obtenerComandaCodigo($codigoComanda)))
          {
            $comanda = new Comanda();
            $comanda = $codigoComanda;
            $comanda->codigoMesa = $codigoMesa;
            $comanda->estadoComanda = $estado;
            $comanda->imagenComanda = $imagenComanda;
            $nombreCliente->precioFinal = $precio;
            if(Comanda::actualizarComanda($comanda))
            {
              $payload = json_encode(array("Actualizar" => "Comanda modificada con exito"));
            }
            else{
              $payload = json_encode(array("Error" => "Error al modificar a la comanda"));
            }
          } 
          else
          {
            $payload = json_encode(array("Error" => "La comanda no existe"));
          }
          
  
        }     

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
    }

    public function PedirCuenta($request,$response,$args)
    {
      $parametros = $request->getParsedBody();
      $payload = json_encode(array("Error" => "Faltan datos!"));
      if(isset($parametros['estadoComanda']) && isset($parametros['codigoComanda']) && isset($parametros['nombreCliente']))
      { 
        $codigo = $parametros['codigoComanda'];
        $nombre = isset($parametros['nombreCliente']);
        $comanda = Comanda::obtenerComandaCodigo($codigo);
        if(!is_bool($comanda) && $comanda->estadoComanda == "Pedido entregado" && $comanda->nombreCliente == $nombre)
        {
          Comanda::actualizarEstadoComanda($comanda,"Esperando cuenta");
          $payload = json_encode(array("mensaje" => "Comanda $codigo esperando cuenta"));
        }
        else
        {
          $payload = json_encode(array("Error" => "La comanda no existe o no se encuentra disponible"));
        }
      }
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function EntregarCuenta($request,$response,$args)
    {
      $parametros = $request->getParsedBody();
      $payload = json_encode(array("Error" => "Faltan datos!"));
      if(isset($parametros['estadoComanda']) && isset($parametros['codigoComanda']) )
      { 
        $codigo = $parametros['codigoComanda'];
        $estado = $parametros['estadoComanda'];
        $comanda = Comanda::obtenerComandaCodigo($codigo);  

        if(!is_bool($comanda) && $comanda->estadoComanda == "Esperando cuenta")
        {
          
          $precioFinal = ComandaController::calcularPrecioFinal($codigo);
          $mesa = Mesa::obtenerMesaCodigo($comanda->codigoMesa);
          Comanda::actualizarEstadoPrecioComanda($comanda,$estado,$precioFinal);
          Mesa::actualizarEstadoMesa($mesa,"Con cliente pagando");
          $payload = json_encode(array("mensaje" => "Comanda $codigo ha recibido la cuenta correspondiente", 
        "Cuenta" => "El total de la cuenta es: $precioFinal"));
        }
        else
        {
          $payload = json_encode(array("Error" => "La comanda no existe o no se encuentra disponible"));
        }
      }
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public static function calcularPrecioFinal($codigoComanda)
    {
      $articulos = Articulo::obtenerArticulosPorCodigoComanda($codigoComanda);
      $precioFinal = 0;
      foreach($articulos as $articulo)
      {
        $precioFinal += $articulo->precio;
      }
      return $precioFinal;
    }

    public static function MostrarDatosComandaUsuario($request,$response,$args)
    {
      $payload = json_encode(array("Error" => "Faltan datos!"));
      if(isset($_GET['codigoComanda']) && isset($_GET['codigoMesa']) )
      {
        $codigoMesa = $_GET['codigoMesa'];
        $codigoComanda = $_GET['codigoComanda'];
         $comanda = Comanda::obtenerComandaMesaCodigo($codigoMesa);
         $demora = Comanda::obtenerTiempoPedido($comanda);
         $mesa = Mesa::obtenerMesaCodigo($codigoMesa);
         if(!is_bool($comanda))
         {
            if(!is_bool($mesa))
            {
              if(!is_bool($demora))
              {
               $payload = json_encode(array("mensaje" => "La mesa $codigoMesa tiene una demora de $demora minutos (Comanda: $codigoComanda)"));
              }
              else
              {
               $payload = json_encode(array("Error" => "No se ha encontrado pedidos pendientes!"));
              }              
            }
            else
            {
              $payload = json_encode(array("Error" => "La mesa no existe o no esta disponible!"));
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
}

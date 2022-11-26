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
          $codigoMesa = $parametros['codigoMesa'];
          $nombreCliente = $parametros['nombreCliente'];
          $destinoImagen = NULL;
          if(!is_bool(Mesa::obtenerMesaCodigo($codigoMesa)))
          {
            $comanda = new Comanda();
            if(isset($_FILES['imagenComanda']))
            {
              $ruta = '../Media/Comandas/';
              $archivoExtension = pathinfo($_FILES["imagenComanda"]["name"], PATHINFO_EXTENSION);
              $nombreImagen = Comanda::crearNombreImagenComanda($archivoExtension,$codigoMesa,$nombreCliente);               
              $destinoImagen = Comanda::crearDestinoImagenComanda($nombreImagen,$ruta);
              $comanda->imagenComanda = $destinoImagen;
            }
            else
            {
              $comanda->imagenComanda = $destinoImagen;
            }
  
            
            $comanda->codigoMesa = $codigoMesa;
            $comanda->nombreCliente = $nombreCliente;
            
            if(!is_bool($comanda->crearComanda()))
            {
              $payload = json_encode(array("mensaje" => "Comanda creada con exito"));
            }
            else
            {
              $payload = json_encode(array("mensaje" => "Error al crear la comanda!"));
            }
          }
          else
          {
            $payload = json_encode(array("Error" => "La mesa no existe!"));
          }
          
        }
        
       
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
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
          if(isset($_POST['imagenComanda']))
          {
            $imagenComanda = $parametros['imagenComanda'];
          }
          $codigoComanda = $args['codigoComanda'];
          if(!is_bool(Comanda::obtenerComandaCodigo($codigoComanda)))
          {
            $comanda = new Comanda();
            $comanda = $codigoComanda;
            $comanda->codigoMesa = $codigoMesa;
            $comanda->estadoComanda = $estado;
            $comanda->imagenComanda = $imagenComanda;
            $nombreCliente->imagenComanda = $nombreCliente;
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
}

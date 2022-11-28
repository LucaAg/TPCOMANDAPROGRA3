<?php

use Illuminate\Support\Facades\Artisan;

require_once './models/Articulo.php';
require_once './models/Csv.php';
require_once './interfaces/IApiUsable.php';

class ArticuloController extends Articulo implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $payload = json_encode(array("Error" => "Faltan datos!"));
        if(isset($_POST['nombreArticulo']) && isset($_POST['precio']) && isset($_POST['cargoEmpleado']))
        { 
          $nombreArticulo = $parametros['nombreArticulo'];
          $precio = $parametros['precio'];
          $cargoEmpleado = $parametros['cargoEmpleado'];

          $articulo = new Articulo();
          $articulo->nombreArticulo = $nombreArticulo;
          $articulo->precio = $precio;
          $articulo->cargoEmpleado = $cargoEmpleado;
          
          if($articulo->crearArticulo())
          {
            $payload = json_encode(array("Mensaje" => "Articulo creado con exito"));
          }
          else
          {
            $payload = json_encode(array("Mensaje" => "Error al crear al articulo!"));
          }
        }     
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $articuloId = $args['id'];
        $articulo = Articulo::obtenerArticulo($articuloId);
        $payload = json_encode($articulo);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Articulo::obtenerTodos();
        $payload = json_encode(array("listaArticulo" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $payload = json_encode(array("Error" => "Faltan datos!"));
        if(isset($_POST['nombreArticulo']) && isset($_POST['precio']) && isset($_POST['cargoEmpleado']))
        {
          $nombreArticulo = $parametros['nombreArticulo'];
          $precio = $parametros['precio'];
          $cargoEmpleado = $parametros['cargoEmpleado'];

          $articulo = new Articulo();
          $articulo->id = $args['articuloId'];
          $articulo->nombreArticulo = $nombreArticulo;
          $articulo->precio = $precio;
          $articulo->cargoEmpleado = $cargoEmpleado;
          if(Articulo::actualizarArticulo($articulo))
          {
            $payload = json_encode(array("mensaje" => "Articulo modificado con exito"));
          }
          else{
            $payload = json_encode(array("mensaje" => "Error al modificar el articulo"));
          }
  
        }     

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $articuloId = $args['articuloId'];
        if(Articulo::eliminarArticulo($articuloId))
        {
          $payload = json_encode(array("mensaje" => "Articulo borrado con exito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "Error al intentar borrar el articulo!"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function crearArchivoCsvArticulos($request, $response, $args)
    {
      $payload= json_encode(array("Error" => "Error inesperado al generar el archivo"));
      if(Csv::crearArchivoCsvTablaArticulos())
      {
        $payload = json_encode(array("mensaje" => "Se cargo el csv con datos exitosamente!"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "Error, verifique la informacion ingresada"));
      }
      $response->getBody()->write( $payload);  
      return $response->withHeader('Content-Type', 'application/json');;
    }

    public function cargarTablaSqlConCsv($request, $response, $args)
    {
      try{     
        $archivo = $_FILES["ArchivoCsv"]["tmp_name"];
        if(Csv::cargarTablaArticulosCSv($archivo))
        {       
          readfile($archivo);
          
          $respon = $response->withHeader('Content-Type', 'text/csv');
        }
        else
        {
          $respon = $response->withHeader('Content-Type', 'apllication/json');
          $response->getBody()->write(json_encode(array("Error" => "La informacion ingresada es incorrecta!")));
        }
      }
      catch(Exception $exp){
        printf("Excepcion capturada: {$exp->getMessage()}");
      }
      finally{
        return $respon;
      }
    }
}

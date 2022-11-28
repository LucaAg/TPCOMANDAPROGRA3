<?php

use GuzzleHttp\Psr7\Message;


require_once './models/Articulo.php';
require_once './models/Comanda.php';
require_once './models/Mesa.php';
require_once './models/Encargo.php';
require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

class EncuestaController extends Encuesta
{
    public function CargarEncuesta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $payload = json_encode(array("Error" => "Faltan datos!"));
        if(isset($_POST['codigoComanda']) && isset($_POST['codigoMesa']) && isset($_POST['puntajeMozo']) && isset($_POST['puntajeCocinero']) && isset($_POST['puntajeMesa'])
        && isset($_POST['puntajeResto']) && isset($_POST['descripcion']))
        {          
            $codigoComanda = $parametros['codigoComanda'];
            $codigoMesa = $parametros['codigoMesa'];
            $puntajeMozo = $parametros['puntajeMozo'];
            $puntajeCocinero = $parametros['puntajeCocinero'];
            $puntajeMesa =  $parametros['puntajeMesa'];
            $puntajeResto= $parametros['puntajeResto'];
            $promedio = ($puntajeMozo + $puntajeCocinero + $puntajeMesa + $puntajeResto) / 4;
            $descripcion = $parametros['descripcion'];
            $comanda = Comanda::obtenerComandaCodigo($codigoComanda);
            $mesa = Mesa::obtenerMesaCodigo($codigoMesa);
            if(!is_bool($mesa))
            {
                if(!is_bool($comanda))
                {
                    if(EncuestaController::checkearValoresEncuesta($puntajeCocinero,$puntajeMesa,$puntajeMozo,$puntajeResto))
                    {
                        if(strlen($descripcion) < 67 && strlen($descripcion) > 9) 
                        {
                            if($comanda->codigoMesa == $mesa->codigoMesa)
                            {
                                if($comanda->estadoComanda == "Finalizada")
                                {
                                    if($mesa->estadoMesa == "Cerrado")
                                    {
                                        $encuesta = new Encuesta();
                                        $encuesta->codigoComanda = $codigoComanda;
                                        $encuesta->codigoMesa = $codigoMesa;
                                        $encuesta->puntajeMozo = $puntajeMozo;
                                        $encuesta->puntajeCocinero = $puntajeCocinero;
                                        $encuesta->puntajeMesa = $puntajeMesa;
                                        $encuesta->puntajeResto = $puntajeResto;
                                        $encuesta->promedio = round($promedio,2);
                                        $encuesta->descripcion = $descripcion;
        
                                        if(!is_bool($encuesta->crearEncuesta()))
                                        {
                                            $payload = json_encode(array("mensaje" => "La encuesta de la mesa $codigoMesa se creo exitosamente!"));
                                        }
                                        else
                                        {
                                            $payload = json_encode(array("Error" => "Error al crear la encuesta!"));
                                        }
                                    }
                                    else
                                    {
                                        $payload = json_encode(array("Error" => "Error la mesa debe estar cerrada!"));
                                    }
                                   
                                }
                                else
                                {
                                    $payload = json_encode(array("Error" => "Luego de pagar la cuenta se habilita la encuesta!"));
                                }
                            }   
                            else
                            {
                                $payload = json_encode(array("Error" => "La mesa y la comanda no coinciden!"));
                            }
                        }
                        else
                        {
                            $payload = json_encode(array("Error" => "Cantidad maxima permitida de caracteres es de 66 (Minimo 10)"));
                        }
                       
                    }
                    else
                    {
                        $payload = json_encode(array("Error" => "Los valores deben estar entre 1 y 10"));
                    }
                }
                else
                {
                    $payload = json_encode(array("Error" => "La comanda no corresponde a ninguno codigo $codigoComanda"));
                }         
            }
            else
            {
                $payload = json_encode(array("Error" => "La mesa no corresponde a ninguno codigo $codigoMesa"));
            }
        }                   
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');    
    }

    public static function checkearValoresEncuesta($puntajeCocinero,$puntajeMesa,$puntajeMozo,$puntajeResto)
    {
        $todoOk = false;
        if(($puntajeCocinero > 0 && $puntajeCocinero < 11) &&
        ($puntajeMesa > 0 && $puntajeMesa < 11) && 
        ($puntajeMozo > 0 && $puntajeMozo < 11) &&
        ($puntajeResto > 0 && $puntajeResto < 11))
        {
            $todoOk = true;
        }
        return $todoOk;
    }

    public static function obtenerComentariosPositivos($request, $response, $args)
    {
        $payload = json_encode(array("Comentarios" => Encuesta::obtenerMejoresComentarios()));
        if(is_bool($payload))
        {
            $payload = json_encode(array("Error" => "Error inesperado al cagar los comentarios!"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');  
    }
    
}

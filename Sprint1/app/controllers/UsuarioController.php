<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $perfil = $parametros['perfil'];

        // Creamos el usuario
        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->perfil = $perfil;
        
        if($usr->crearUsuario())
        {
          $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "Error al crear el usuario!"));
        }
       
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $usr = $args['usuario'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['usuario'];
        $clave = $parametros['clave'];
        $perfil = $parametros['perfil'];

        $usuario = new Usuario();
        $usuario->id = $args['usuarioId'];
        $usuario->usuario = $nombre;
        $usuario->clave = $clave;
        $usuario->perfil = $perfil;
        if(Usuario::modificarUsuario($usuario))
        {
          $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
        }
        else{
          $payload = json_encode(array("mensaje" => "Error al modificar al usuario"));
        }

        

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        //$parametros = $request->getParsedBody();

        $usuarioId = $args['usuarioId'];
        if(Usuario::borrarUsuario($usuarioId))
        {
          $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "Error al intentar borrar al usuario!"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}

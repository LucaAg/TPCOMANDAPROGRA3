<?php
require_once './models/AutenticadorJWT.php';
require_once './models/Usuario.php';

class LoginController
{
    public function UsuarioLogin($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['usuario']) && isset($parametros['clave']))
        {   
            $payload = json_encode(array('Error' => "Error al logueaerse!"));
            $clave = $parametros['clave'];
            $usuario = Usuario::obtenerUsuario($parametros['usuario']);           
            if(!is_null($usuario))
            {
                if(password_verify($clave, $usuario->clave))
                {
                    $datos = array(
                        'usuario' => $usuario->usuario,
                        'perfil' => $usuario->perfil
                    );
                    $token = AutentificadorJWT::CrearToken($datos); //, 'response' => 'Correcto' => $usuario->perfil;
                    $payload = json_encode(array("Login exitoso" => "Se ha logueado correctamente!",
                "JWT" => $token));
                    $response = $response->withStatus(200);
                }
            }
            else
            {
                $payload = json_encode(array('Error' => "El usuario no existe!"));
            }
        }
        else{
            $payload = json_encode(array('Error' => "Faltan datos!"));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>
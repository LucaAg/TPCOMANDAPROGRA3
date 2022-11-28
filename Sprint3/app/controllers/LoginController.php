<?php
require_once './models/AutenticadorJWT.php';
require_once './models/Empleado.php';

class LoginController
{
    public function UsuarioLogin($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $payload = json_encode(array('Error' => "Faltan datos!"));
        try
        {
            if(isset($parametros['id']) && isset($parametros['clave']))
            {   
                $payload = json_encode(array('Error' => "Error al logueaerse!"));
                $clave = $parametros['clave'];
                $usuario = Empleado::obtenerEmpleadoId($parametros['id']);                    
                if(!is_null($usuario))
                {                   
                    if(password_verify($clave, $usuario->clave))
                    {
                        $datos = array(
                            'id' => $usuario->id,
                            'tipo' => $usuario->tipoEmpleado
                        );
                        $token = AutentificadorJWT::CrearToken($datos);
                        $payload = json_encode(array("Login" => "Exitoso!","Empleado" => $usuario->nombreCompleto, "Cargo" => $usuario->tipoEmpleado,
                    "JWT" => $token));
                        $response = $response->withStatus(200);
                    }
                }
                else
                {
                    throw new Exception("El usuario no existe!");
                }
            }
        }
        catch(Exception $ex)
        {
            $payload = json_encode(array("Error" => $ex->getMessage()));
            $response->getBody()->write($payload);
            $response = $response->withStatus(401);
        }
      
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>
<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
class MWPerfilSocio
{
    public function __invoke(Request $request, RequestHandler $handler) :Response
    {
        $header = $request->getHeaderLine('Authorization');
        $response = new Response();
        try
        {
            if(!empty($header))
            {
                $token = trim(explode("Bearer", $header) [1]);
                json_encode(array('datos'=>AutentificadorJWT::VerificarToken($token)));
                
                $datosToken = AutentificadorJWT::ObtenerData($token);
                if($datosToken->tipo == "Socio")
                {
                    $response = $handler->handle($request);
                }
                else{
                    throw new Exception("Solo socios estan autorizados!");
                }
            }
            else
            {
                throw new Exception("El token no existe!");
            }
        }
        catch(Exception $ex)
        {
            $payload = json_encode(array("Error" => $ex->getMessage()));
            $response->getBody()->write($payload);
            $response = $response->withStatus(401);
        }        
        return $response->withHeader('Content-Type', 'application/json');
    }

}
?>
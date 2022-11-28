<?php
class Mesa
{
    public $codigoMesa;
    public $idMozo;
    public $estadoMesa;

    public function altaMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET idMozo = :idMozo where codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function crearCodigo($length = 5) 
    { 
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length); 
    } 
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesaCodigo($codigoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function obtenerMesasPorEstado($estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE estadoMesa = :estado");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_BOOL);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    /*public static function actualizarMesa($mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET estadoMesa = :estadoMesa, idMozo = :idMozo WHERE codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $mesa->codigoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':estadoMesa', $mesa->estadoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':idMozo', $mesa->idMozo, PDO::PARAM_INT);
        return $consulta->execute();
    }*/

    public static function actualizarEstadoMesa($mesa,$estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET estadoMesa = :estadoMesa, idMozo = :idMozo WHERE codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $mesa->codigoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':estadoMesa', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':idMozo', $mesa->idMozo, PDO::PARAM_INT);
        return $consulta->execute();
    }

    public static function borrarMesa($mesaId)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE from mesas WHERE id = :mesaId");
        $consulta->bindValue(':id', $mesaId, PDO::PARAM_INT);
        return $consulta->execute();
    }

    public static function CheckearMozoAsignado($idEmpleado,$codigoMesa)
    {
      $todoOk = false;
      $mesa = Mesa::obtenerMesaCodigo($codigoMesa);
      if(!is_bool($mesa))
      {          
        if($mesa->idMozo == $idEmpleado)
        {
            $todoOk = true;
        }
      }   
      return $todoOk;
    }
  
   
}
?>
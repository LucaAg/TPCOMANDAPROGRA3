<?php
class Mesa
{
    public $codigoMesa;
    public $idMozo;
    public $estadoMesa;

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (codigoMesa, idMozo, estadoMesa)
         VALUES (:codigoMesa, :idMozo, :estadoMesa)");
        $codigo = Mesa::crearCodigo();
        $consulta->bindValue(':codigoMesa', $codigo);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':estadoMesa', "Libre");
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
}
?>
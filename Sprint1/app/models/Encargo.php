<?php
class Encargo
{
    public $id;
    public $codigoComanda;
    public $idArticulo;
    public $estadoEncargo;
    public $idEmpleado;
    public $tiempoEstimado;

    public function crearEncargo()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encargos (codigoComanda, idArticulo, estadoEncargo, idEmpleado)
         VALUES (:codigoComanda, :idArticulo, :estadoEncargo, :idEmpleado)");

        $consulta->bindValue(':codigoComanda', $this->codigoComanda, PDO::PARAM_STR);
        $consulta->bindValue(':idArticulo', $this->idArticulo, PDO::PARAM_INT);
        $consulta->bindValue(':idEmpleado', $this->idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':estadoEncargo', 'Pendiente');
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function modificarEncargo($encargo)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE encargos SET codigoComanda = :codigoComanda, idArticulo = :idArticulo, estadoEncargo = :estadoEncargo, idEmpleado = :idEmpleado, tiempoEstimado = :tiempoEstimado WHERE id = :id");
        $consulta->bindValue(':id', $encargo->id, PDO::PARAM_INT);
        $consulta->bindValue(':estadoEncargo', $encargo->estadoEncargo, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEstimado', $encargo->tiempoEstimado, PDO::PARAM_INT);
        $consulta->bindValue(':codigoComanda', $encargo->codigoComanda, PDO::PARAM_STR);
        $consulta->bindValue(':idArticulo', $encargo->idArticulo, PDO::PARAM_INT);
        $consulta->bindValue(':idEmpleado', $encargo->idEmpleado, PDO::PARAM_INT);
        return $consulta->execute();
    }

    public static function modificarEstadoEncargoDuracion($encargo,$estadoEncargo,$duracion)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE encargos SET estadoEncargo = :estadoEncargo, tiempoEstimado = :duracion WHERE id = :id");
        $consulta->bindValue(':id', $encargo->id, PDO::PARAM_INT);
        $consulta->bindValue(':estadoEncargo', $estadoEncargo);
        $consulta->bindValue(':duracion', $duracion);
        return $consulta->execute();
    }

    
    public static function borrarEncargo($encargoId)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE encargos SET estadoEncargo = :estadoEncargo WHERE id = :id");
        $consulta->bindValue(':id', $encargoId, PDO::PARAM_INT);
        $consulta->bindValue(':estadoEncargo', 'Cancelado');
        return $consulta->execute();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encargos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    public static function obtenerUno()
    {
        
    }

    public static function obtenerEscargoId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encargos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Empleado');
    }
}
?>
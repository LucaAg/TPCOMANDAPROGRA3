<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
class Encargo
{
    public $id;
    public $codigoComanda;
    public $idArticulo;
    public $estadoEncargo;
    public $idEmpleado;
    public $horaInicio;
    public $horaFin;
    public $tiempoEstimado;

    public function crearEncargo()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encargos (codigoComanda, idArticulo, estadoEncargo)
         VALUES (:codigoComanda, :idArticulo, :estadoEncargo)");
        $consulta->bindValue(':codigoComanda', $this->codigoComanda, PDO::PARAM_STR);
        $consulta->bindValue(':idArticulo', $this->idArticulo, PDO::PARAM_INT);
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
    
    public static function modificarEncargoConId($encargo,$estadoEncargo,$duracion,$idEmpleado,$horaInicio)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE encargos SET idEmpleado = :idEmpleado, estadoEncargo = :estadoEncargo, tiempoEstimado = :duracion, horaInicio = :horaInicio WHERE id = :id");
        $consulta->bindValue(':id', $encargo->id, PDO::PARAM_INT);
        $consulta->bindValue(':estadoEncargo', $estadoEncargo);
        $consulta->bindValue(':duracion', $duracion);
        $consulta->bindValue(':idEmpleado', $idEmpleado);
        $consulta->bindValue(':horaInicio', $horaInicio);
        return $consulta->execute();
    }

    public static function modificarEstadoEncargoEstadoDuracion($encargo,$estadoEncargo,$duracion,$horaFin)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE encargos SET estadoEncargo = :estadoEncargo, tiempoEstimado = :duracion, horaFin = :horaFin WHERE id = :id");
        $consulta->bindValue(':id', $encargo->id, PDO::PARAM_INT);
        $consulta->bindValue(':estadoEncargo', $estadoEncargo);
        $consulta->bindValue(':duracion', $duracion);
        $consulta->bindValue(':horaFin', $horaFin);
        return $consulta->execute();
    }

    public static function modificarEstadoPorComanda($codigoComanda,$estadoEncargo)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE encargos SET estadoEncargo = :estadoEncargo WHERE codigoComanda = :codigoComanda");
        $consulta->bindValue(':codigoComanda', $codigoComanda, PDO::PARAM_STR);
        $consulta->bindValue(':estadoEncargo', $estadoEncargo);
        return $consulta->execute();
    }
    
    public static function cancelarEncargo($encargoId)
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

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encargo');
    }

    public static function obtenerEncargosPorCodigoComanda($codigoComanda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encargos WHERE codigoComanda = :codigoComanda");
        $consulta->bindValue(':codigoComanda', $codigoComanda, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encargo');
    }

    public static function obtenerEncargosPorCodigoYEstado($codigoComanda,$estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encargos WHERE (codigoComanda = :codigoComanda AND estadoEncargo = :estadoEncargo)");
        $consulta->bindValue(':codigoComanda', $codigoComanda, PDO::PARAM_STR);
        $consulta->bindValue(':estadoEncargo', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encargo');
    }

    public static function obtenerEncargosPendientesPorTipo($estado,$tipo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigoComanda, idArticulo, estadoEncargo, a.nombreArticulo, em.tipoEmpleado FROM encargos as en
        JOIN articulos as a ON a.id = en.idArticulo
        JOIN empleados as em ON em.id = en.idEmpleado
        WHERE en.estadoEncargo = :estadoEncargo AND em.tipoEmpleado = :tipo");
        $consulta->bindValue(':estadoEncargo', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'stdClass');
    }

    
    public static function obtenerEncargosPendientes()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encargos WHERE estadoEncargo = :estadoEncargo");
        $consulta->bindValue(':estadoEncargo', "Pendiente");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encargo');
    }

    public static function obtenerEncargosPreparacion()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encargos WHERE estadoEncargo = :estadoEncargo");
        $consulta->bindValue(':estadoEncargo', "En preparacion");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'stdClass');
    }

    public static function obtenerEncargosListosParaSevir()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encargos WHERE estadoEncargo = :estadoEncargo");
        $consulta->bindValue(':estadoEncargo', "Listo para servir");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'stdClass');
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
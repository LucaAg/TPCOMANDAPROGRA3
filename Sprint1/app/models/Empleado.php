<?php
class Empleado
{
    public $id;
    public $tipoEmpleado;
    public $clave;
    public $nombreCompleto;
    public $esSocio;
    public $fechaAlta;
    public $fechaBaja;


    public function crearEmpleado()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO empleados (tipoEmpleado, clave, nombreCompleto, esSocio, fechaAlta)
         VALUES (:tipoEmpleado, :clave, :nombreCompleto, :esSocio, :fechaAlta)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':tipoEmpleado', $this->tipoEmpleado, PDO::PARAM_STR);
        $consulta->bindValue(':esSocio', $this->esSocio, PDO::PARAM_BOOL);
        $consulta->bindValue(':nombreCompleto', $this->nombreCompleto, PDO::PARAM_STR);
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':fechaAlta', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function modificarEmpleado($empleado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET tipoEmpleado = :tipoEmpleado, clave = :clave, nombreCompleto = :nombreCompleto, esSocio = :esSocio WHERE id = :id");
        $consulta->bindValue(':tipoEmpleado', $empleado->tipoEmpleado, PDO::PARAM_STR);
        $claveHash = password_hash($empleado->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':id', $empleado->id, PDO::PARAM_INT);
        $consulta->bindValue(':nombreCompleto', $empleado->nombreCompleto, PDO::PARAM_STR);
        $consulta->bindValue(':esSocio', $empleado->esSocio, PDO::PARAM_BOOL);
        return $consulta->execute();
    }

    
    public static function borrarEmpleado($usuarioId)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET fechaBaja = :fechaBaja WHERE id = :id");
        $consulta->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d'));
        return $consulta->execute();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleados");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    public static function obtenerEmpleadoId($idEmpleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleados WHERE id = :idEmpleado");
        $consulta->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Empleado');
    }
}
?>
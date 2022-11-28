<?php
class Articulo
{
    public $id;
    public $nombreArticulo;
    public $precio;
    public $cargoEmpleado;

    public function crearArticulo()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO articulos (nombreArticulo, precio, cargoEmpleado)
         VALUES (:nombreArticulo, :precio, :cargoEmpleado)");
        $consulta->bindValue(':nombreArticulo', $this->nombreArticulo, PDO::PARAM_INT);       
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':cargoEmpleado', $this->cargoEmpleado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
    public static function obtenerTodos() 
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM articulos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Articulo');
    }

    public static function obtenerArticulo($idArticulo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM articulos WHERE id = :idArticulo");
        $consulta->bindValue(':idArticulo', $idArticulo, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Articulo');
    }

    public static function actualizarArticulo($articulo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE articulos SET nombreArticulo = :nombreArticulo, precio = :precio, cargoEmpleado = :cargoEmpleado WHERE id = :articuloId");
        $consulta->bindValue(':articuloId', $articulo->id, PDO::PARAM_INT);
        $consulta->bindValue(':precio', $articulo->precio, PDO::PARAM_INT);
        $consulta->bindValue(':cargoEmpleado', $articulo->cargoEmpleado, PDO::PARAM_STR);
        $consulta->bindValue(':nombreArticulo', $articulo->nombreArticulo, PDO::PARAM_STR);
        return $consulta->execute();
    }

    public static function eliminarArticulo($idArticulo)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE from articulos WHERE id = :idArticulo");
        $consulta->bindValue(':id', $idArticulo, PDO::PARAM_INT);
        return $consulta->execute();
    }

    public static function obtenerArticuloEncargo($encargo)
    {   
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM articulos where id = :idArticulo");
        $consulta->bindValue(':idArticulo', $encargo->idArticulo, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Articulo');
    }

    public static function obtenerArticulosPorCodigoComanda($codigoComanda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM articulos as a 
        JOIN encargos as e
        ON e.idArticulo = a.id
        JOIN comandas as c
        ON c.codigo = e.codigoComanda
        WHERE e.codigoComanda = :codigoComanda");
        $consulta->bindValue(':codigoComanda', $codigoComanda, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Articulo');
    }
}
?>
<?php
class Producto
{
    public $id;
    public $idArea;
    public $idPedido;
    public $estadoProducto;
    public $nombreProducto;
    public $precio;
    public $horaInicio;
    public $horaFinalizado;
    public $duracion;

    public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (idArea, idPedido, estadoProducto, nombreProducto, precio, horaInicio, horaFinalizado, duracion)
         VALUES (:idArea, :idPedido, :estadoProducto, :nombreProducto, :precio, :horaInicio, :horaFinalizado, :duracion)");
        $consulta->bindValue(':idArea', $this->idArea, PDO::PARAM_INT);       
        $consulta->bindValue(':estadoProducto', "Pendiente");
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':nombreProducto', $this->nombreProducto, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':horaInicio', date('H-i-s'));
        $consulta->bindValue(':horaFinalizado', $this->horaFinalizado);
        $consulta->bindValue(':duracion', $this->duracion);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE codigo = :codigoPedido");
        $consulta->bindValue(':codigo', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerPedidosPorEstado($estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE estadoPedido = :estado");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }
}
?>
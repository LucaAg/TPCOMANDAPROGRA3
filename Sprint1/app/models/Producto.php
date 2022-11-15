<?php
class Producto
{
    public $id;
    public $idArea;
    //public $pedidoCodigo;
    public $estadoProducto;
    public $nombreProducto;
    public $precio;
    public $horaInicio;
    public $horaFinalizado;
    public $duracion;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (idArea, pedidoCodigo, nombreProducto, precio, horaInicio, horaFinalizado, duracion)
         VALUES (:idArea, :pedidoCodigo, :nombreProducto, :precio, :horaInicio, :horaFinalizado, :duracion)");
        $consulta->bindValue(':idArea', $this->idArea, PDO::PARAM_INT);
        $consulta->bindValue(':pedidoCodigo', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':estadoPedido', "Pendiente");
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':imagenPedido', $this->imagenPedido, PDO::PARAM_STR);
        $consulta->bindValue(':precioFinal', $this->precioFinal, PDO::PARAM_STR);
        $consulta->bindValue(':precioFinal', $this->precioFinal, PDO::PARAM_STR);
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
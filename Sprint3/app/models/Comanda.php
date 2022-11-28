<?php
class Comanda
{
    public $codigo;
    public $codigoMesa;
    public $estadoComanda;
    public $nombreCliente;
    public $imagenComanda;        
    public $precioFinal;

    public function crearComanda()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO comandas (codigo, codigoMesa, estadoComanda, nombreCliente, imagenComanda)
         VALUES (:codigo, :codigoMesa, :estadoComanda, :nombreCliente, :imagenComanda)");
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':estadoComanda', "Pendiente");
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':imagenComanda', $this->imagenComanda, PDO::PARAM_STR);
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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM comandas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Comanda');
    }
    public static function obtenerComandaCodigo($codigoComanda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM comandas WHERE codigo = :codigoComanda");
        $consulta->bindValue(':codigoComanda', $codigoComanda, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Comanda');
    }

    public static function obtenerComandasPorEstado($estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM comandas WHERE estadoComanda = :estado");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Comanda');
    }

    public static function obtenerComandaMesaCodigo($codigoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM comandas WHERE codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Comanda');
    }

    public static function obtenerComandasMesaCodigo($codigoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM comandas WHERE codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Comanda');
    }


    public static function actualizarEstadoComanda($comanda,$estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE comandas SET estadoComanda = :estado WHERE codigo = :comandaId");
        $consulta->bindValue(':comandaId', $comanda->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        return $consulta->execute();
    }

    public static function actualizarEstadoPrecioComanda($comanda,$estado,$precio)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE comandas SET estadoComanda = :estado, precioFinal = :precio WHERE codigo = :comandaId");
        $consulta->bindValue(':comandaId', $comanda->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $precio, PDO::PARAM_STR);
        return $consulta->execute();
    }

    public static function actualizarComanda($comanda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE comandas SET codigoMesa = :codigoMesa, nombreCliente = :nombreCliente, estado = :estado, imagen = :imagenComanda, precioFinal = :precio WHERE id = :comandaId");
        $consulta->bindValue(':comandaId', $comanda->codigo, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $comanda->estadoComanda, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $comanda->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':imagenComanda', $comanda->imagenComanda , PDO::PARAM_STR);
        $consulta->bindValue(':precio', $comanda->precioFinal , PDO::PARAM_INT);
        $consulta->bindValue(':codigoMesa', $comanda->codigoMesa , PDO::PARAM_STR);
        return $consulta->execute();
    }

    public static function obtenerComandaEncargo($encargo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM comandas WHERE codigo = :codigoComanda");
        $consulta->bindValue(':codigoComanda', $encargo->codigoComanda, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Comanda');
    }

    public static function crearNombreImagenComanda($extension,$codigo,$codigoMesa,$nombreCliente)
    {
      $nombreImagen = $codigo.'-'.$codigoMesa.'-'.$nombreCliente.'.'.$extension;
      return $nombreImagen;
    }

    public static function crearDestinoImagenComanda($nombreImagen,$ruta)
    {
      $destinoImagen = $ruta.'/'.$nombreImagen;
      if (!file_exists($ruta))
      {
        mkdir($ruta, 0777, true);
      }

      move_uploaded_file($_FILES['imagenComanda']['tmp_name'], $destinoImagen);
      return $destinoImagen;
    }

    public static function obtenerTiempoPedido($comanda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT MAX(e.tiempoEstimado)  FROM encargos as e
        JOIN comandas as c ON c.codigo = e.codigoComanda
        WHERE e.codigoComanda = :codigoComanda AND c.codigoMesa = :codigoMesa AND e.estadoEncargo = :estado");
        $consulta->bindValue(':codigoComanda', $comanda->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $comanda->codigoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':estado', 'En preparacion');
        $consulta->execute();

        return $consulta->fetchColumn();
    }

    public static function obtenerMesaMasUsada()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigoMesa FROM comandas
                GROUP BY codigoMesa
                HAVING COUNT(*)>1");
        $consulta->execute();
        return $consulta->fetchObject('stdClass');
    }
}
?>
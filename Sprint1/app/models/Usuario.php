<?php

class Usuario
{
    public $id;
    public $nombreUsuario;
    public $clave;
    public $cargoUsuario;
    public $estado;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, cargo, estado) VALUES (:nombreUsuario, :clave, :cargoUsuario, :estado)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':nombreUsuario', $this->nombreUsuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':cargoUsuario', $this->cargoUsuario, PDO::PARAM_STR);
        $consulta->bindValue(':estado', true);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, cargo, estado FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuarioNombre)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, cargo, estado FROM usuarios WHERE usuario = :usuarioNombre");
        $consulta->bindValue(':usuario', $usuarioNombre, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
    
    public static function modificarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :nombreUsuario, clave = :clave, cargo = :cargoUsuario, estado = :estado WHERE id = :id");
        $consulta->bindValue(':nombreUsuario', $usuario->nombreUsuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $usuario->clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $usuario->id, PDO::PARAM_INT);
        $consulta->bindValue(':cargo', $usuario->cargoUsuario, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $usuario->estado, PDO::PARAM_BOOL);
        return $consulta->execute();
    }

    
    public static function borrarUsuario($usuarioId)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $consulta->bindValue(':estado', false);
        return $consulta->execute();
    }
}
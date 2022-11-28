<?php
class Encuesta
{
    public $codigoMesa;
    public $codigoComanda;
    public $puntajeMozo;
    public $puntajeCocinero;
    public $puntajeMesa;
    public $puntajeResto;
    public $promedio;
    public $descripcion;

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuesta (codigoMesa, codigoComanda, puntajeMozo, puntajeCocinero, puntajeMesa, puntajeResto, promedio, descripcion)
         VALUES (:codigoMesa, :codigoComanda, :puntajeMozo, :puntajeCocinero, :puntajeMesa, :puntajeResto, :promedio, :descripcion)");
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':codigoComanda', $this->codigoComanda, PDO::PARAM_STR);
        $consulta->bindValue(':puntajeMozo', $this->puntajeMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeCocinero', $this->puntajeCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeMesa', $this->puntajeMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeResto', $this->puntajeResto, PDO::PARAM_INT);
        $consulta->bindValue(':promedio', $this->promedio);
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->execute();
    
        return $objAccesoDatos->obtenerUltimoId();
    }
    
}

?>
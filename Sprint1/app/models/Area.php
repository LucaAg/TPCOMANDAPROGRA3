<?php
require_once './db/AccesoDatos.php';
class Area
{
    public $idArea;
    public $nombreArea;

    public function __construct(){}

    public static function obtenerAreaId($areaId){
        $objetoAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDatos->prepararConsulta("SELECT * FROM areas WHERE idArea = :areaId;");
        $consulta->bindParam(':areaId', $areaId);
        $consulta->execute();       
        return  $consulta->fetchObject('Area');
    }

    public static function obtenerAreaNomre($nombre){
        $objetoAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDatos->prepararConsulta("SELECT idArea, nombreArea FROM areas WHERE nombreArea = :nombreArea;");
        $consulta->bindParam(':nombreArea', $nombre);
        $consulta->execute(); 
        return $consulta->fetchObject('Area');
    }

    public static function obtenerTodas(){
        $objetoAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDatos->prepararConsulta("SELECT * FROM areas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Area');
    }
}
?>
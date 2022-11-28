<?php

    class Csv
    {
        public static function crearArchivoCsvTablaArticulos()
        {
            $todoOk = false;
            $articulos = Articulo::obtenerTodos();
            $ruta = "../Media/articulos.csv";
            $file = fopen($ruta, "w+");
            foreach($articulos as $articulo)
            {
                if($file)
                {
                    fwrite($file, implode(",", (array)$articulo).PHP_EOL); 
                }                           
            }
            fclose($file);  
            if(filesize($ruta) > 0)
            {
                $todoOk = true;
            }

            return $todoOk;
        }

        public static function cargarTablaArticulosCSv($file)
        {
            $todoOk=false;
            if(file_exists($file))
            {
                Articulo::borrarDatosTablaBackUpArticulos();
                $archivo = fopen($file, "r");
                try
                {
                    while(!feof($archivo))
                    {
                        $datosArticulos = fgets($archivo);                        
                        if(!empty($datosArticulos))
                        {         

                            $articulo = new Articulo();

                            $infoArticulo =explode(",", $datosArticulos);
                            $articulo->id=$infoArticulo[0];
                            $articulo->nombreArticulo=$infoArticulo[1];
                            $articulo->precio=$infoArticulo[2];
                            $articulo->cargoEmpleado=$infoArticulo[3];
                            $articulo->crearArticuloId();                       
                        }
                    }
                    $todoOk = true;
                }
                catch(Exception $exp)
                {
                    echo "Error al leer el archivo -".$exp->getMessage();
                    
                }
                finally
                {
                    fclose($archivo);
                    return $todoOk;
                }
                
            }
        }
    }
?>
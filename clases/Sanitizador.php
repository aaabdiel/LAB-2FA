<?php
class Sanitizador {  
    // Sanitiza una cadena eliminando espacios y etiquetas HTML
    public static function limpiarCadena($cadena) {
        return trim(strip_tags($cadena));
    }

}//Sanitizador  // ← También cambia este comentario

//$nombre = "<b>Juan</b> ";
//$nombreLimpio = Sanitizador::limpiarCadena($nombre);  
//echo "la salida es: ".$nombre."<br>";
?>
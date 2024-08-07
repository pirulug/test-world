<?php
// Crear una imagen en blanco
$width = 400;
$height = 200;
$image = imagecreatetruecolor($width, $height);

// Asignar colores
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);

// Rellenar la imagen con color blanco
imagefill($image, 0, 0, $white);

// Escribir texto en la imagen
$text = "Hola, PHP 8.3!";
$font_size = 5; // Tamaño de la fuente (de 1 a 5 para fuentes internas)
$x = 50; // Coordenada X
$y = 100; // Coordenada Y

// Dibujar el texto
imagestring($image, $font_size, $x, $y, $text, $black);

// Enviar la imagen al navegador
header("Content-Type: image/png");
imagepng($image);

// Liberar la memoria
imagedestroy($image);
?>

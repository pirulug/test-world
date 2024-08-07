<?php
function createAvatar($name, $size = 100, $background = 'cccccc', $color = 'ffffff', $rounded = false) {
  // Generar color de fondo aleatorio si se especifica 'random'
  if ($background === 'random') {
    $background = sprintf('%06X', mt_rand(0, 0xFFFFFF));
  }

  // Convertir los parámetros de color de hexadecimal a RGB
  list($r, $g, $b)    = sscanf($background, "%02x%02x%02x");
  list($fr, $fg, $fb) = sscanf($color, "%02x%02x%02x");

  // Crear una imagen en blanco
  $image = imagecreatetruecolor($size, $size);

  // Asignar colores
  $bgColor   = imagecolorallocate($image, $r, $g, $b);
  $fontColor = imagecolorallocate($image, $fr, $fg, $fb);

  // Rellenar la imagen con el color de fondo
  imagefill($image, 0, 0, $bgColor);

  // Obtener las iniciales del nombre
  $initials = '';

  if ($name == "") {
    $name = "NN";
  }

  $words = explode(' ', $name);

  foreach ($words as $word) {
    $initials .= strtoupper($word[0]);
  }

  // Usar una fuente TrueType
  $font_file = 'ttf/FiraCode-Bold.ttf'; // Reemplaza con la ruta a tu fuente TrueType
  $font_size = $size * 0.4; // Ajustar el tamaño de la fuente según el tamaño de la imagen
  $bbox      = imagettfbbox($font_size, 0, $font_file, $initials);

  // Calcular la posición del texto
  $x = intval(($size - ($bbox[2] - $bbox[0])) / 2);
  $y = intval(($size - ($bbox[7] - $bbox[1])) / 2);

  // Dibujar el texto en la imagen
  imagettftext($image, $font_size, 0, $x, $y, $fontColor, $font_file, $initials);

  // Si se requiere redondear la imagen
  if ($rounded) {
    $mask        = imagecreatetruecolor($size, $size);
    $transparent = imagecolorallocate($mask, 0, 0, 0);
    $black       = imagecolorallocate($mask, 255, 255, 255);
    imagefill($mask, 0, 0, $transparent);
    imagefilledellipse($mask, $size / 2, $size / 2, $size, $size, $black);
    imagecolortransparent($mask, $black);

    // imagealphablending($mask, true); // Activar mezcla de transparencia
    // imagesavealpha($mask, true); // Guardar la transparencia

    // imagealphablending($mask, false); // Desactivar mezcla de color
    // imagesavealpha($mask, true); // Guardar transparencia

    // Aplicar la máscara circular
    imagecopymerge($image, $mask, 0, 0, 0, 0, $size, $size, 100);
    imagedestroy($mask);

    // Establecer el color transparente en la imagen final
    $transparent = imagecolorallocate($image, 0, 0, 0);
    imagecolortransparent($image, $transparent);
  }

  // Enviar la imagen al navegador
  header("Content-Type: image/png");
  imagepng($image);

  // Liberar la memoria
  imagedestroy($image);
}

// Obtener parámetros de la URL
$name       = isset($_GET['name']) ? $_GET['name'] : 'NN';
$size       = isset($_GET['size']) ? intval($_GET['size']) : 100;
$background = isset($_GET['background']) ? $_GET['background'] : 'cccccc';
$color      = isset($_GET['color']) ? $_GET['color'] : 'ffffff';
$rounded    = isset($_GET['rounded']) ? filter_var($_GET['rounded'], FILTER_VALIDATE_BOOLEAN) : false;

// Crear el avatar
createAvatar($name, $size, $background, $color, $rounded);

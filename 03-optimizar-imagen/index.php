<?php
function optimizeImage($sourceImagePath, $destinationImagePath) {
  // Obtener información de la imagen original
  $originalSize = filesize($sourceImagePath);

  // Obtener la información de la imagen original
  $info = getimagesize($sourceImagePath);
  $mime = $info['mime'];

  // Crear una imagen según el tipo de la imagen original
  switch ($mime) {
    case 'image/jpeg':
      $image = imagecreatefromjpeg($sourceImagePath);
      break;
    case 'image/png':
      $image = imagecreatefrompng($sourceImagePath);
      break;
    default:
      echo "Formato de imagen no compatible: $mime";
      return false;
  }

  // Guardar la imagen optimizada
  switch ($mime) {
    case 'image/jpeg':
      imagejpeg($image, $destinationImagePath, 75); // 75 es la calidad del 0 al 100
      break;
    case 'image/png':
      imagepng($image, $destinationImagePath, 9); // 9 es el nivel de compresión del 0 al 9
      break;
  }

  // Liberar la memoria
  imagedestroy($image);

  // Obtener el tamaño del archivo optimizado
  $optimizedSize = filesize($destinationImagePath);

  // Mostrar información de optimización
  echo "La imagen original tenía un tamaño de: " . formatBytes($originalSize) . "<br>";
  echo "La imagen optimizada se ha guardado en: $destinationImagePath y tiene un tamaño de: " . formatBytes($optimizedSize);
}

// Función auxiliar para formatear el tamaño de archivo en bytes
function formatBytes($bytes, $precision = 2) {
  $units = array('B', 'KB', 'MB', 'GB', 'TB');

  $bytes = max($bytes, 0);
  $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
  $pow   = min($pow, count($units) - 1);

  $bytes /= pow(1024, $pow);

  return round($bytes, $precision) . ' ' . $units[$pow];
}

// Ejemplo de uso
$sourceImagePath      = 'poster.jpg'; // Ruta de la imagen original
$destinationImagePath = 'poster_redux.jpg'; // Ruta donde guardar la imagen optimizada

// Llamar a la función para optimizar la imagen
optimizeImage($sourceImagePath, $destinationImagePath);
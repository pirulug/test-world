<?php
function convertToWebP($sourceImagePath, $destinationImagePath) {
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
    case 'image/gif':
      $image = imagecreatefromgif($sourceImagePath);
      break;
    default:
      echo "Formato de imagen no compatible: $mime";
      return false;
  }

  // Guardar la imagen en formato WebP
  if (imagewebp($image, $destinationImagePath, 85)) { // 85 es la calidad del 0 al 100
    echo "La imagen se ha convertido a WebP satisfactoriamente.";
  } else {
    echo "Error al convertir la imagen a WebP.";
  }

  // Liberar la memoria
  imagedestroy($image);
}

// Ejemplo de uso
$sourceImagePath      = 'avatar.png'; // Reemplaza con la ruta de tu imagen original
$destinationImagePath = 'avatar.webp'; // Ruta donde guardar la imagen WebP

// Llamar a la función para convertir la imagen a WebP
convertToWebP($sourceImagePath, $destinationImagePath);
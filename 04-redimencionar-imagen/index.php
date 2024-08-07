<?php
function resizeImage($sourceImagePath, $destinationImagePath, $maxWidth, $maxHeight) {
  // Obtener la información de la imagen original
  $info           = getimagesize($sourceImagePath);
  $originalWidth  = $info[0];
  $originalHeight = $info[1];
  $mime           = $info['mime'];

  // Determinar el tipo de imagen y crear la imagen correspondiente
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

  // Calcular las nuevas dimensiones de la imagen manteniendo la proporción
  $ratio     = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
  $newWidth  = $originalWidth * $ratio;
  $newHeight = $originalHeight * $ratio;

  // Crear una nueva imagen redimensionada
  $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

  // Preservar la transparencia si es una imagen PNG o GIF
  if ($mime === 'image/png' || $mime === 'image/gif') {
    imagealphablending($resizedImage, false);
    imagesavealpha($resizedImage, true);
    $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
    imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
  }

  // Redimensionar la imagen original a la nueva imagen redimensionada
  imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

  // Guardar la imagen redimensionada en el archivo de destino
  switch ($mime) {
    case 'image/jpeg':
      imagejpeg($resizedImage, $destinationImagePath, 75); // 75 es la calidad del 0 al 100
      break;
    case 'image/png':
      imagepng($resizedImage, $destinationImagePath, 9); // 9 es el nivel de compresión del 0 al 9
      break;
    case 'image/gif':
      imagegif($resizedImage, $destinationImagePath);
      break;
  }

  // Liberar memoria
  imagedestroy($image);
  imagedestroy($resizedImage);

  echo "La imagen se ha redimensionado y guardado en: $destinationImagePath";
}

// Ejemplo de uso
$sourceImagePath      = 'poster.jpg'; // Ruta de la imagen original
$destinationImagePath = 'poster_redimensionada.jpg'; // Ruta donde guardar la imagen redimensionada
$maxWidth             = 800; // Ancho máximo deseado
$maxHeight            = 600; // Altura máxima deseada

// Llamar a la función para redimensionar la imagen
resizeImage($sourceImagePath, $destinationImagePath, $maxWidth, $maxHeight);
?>
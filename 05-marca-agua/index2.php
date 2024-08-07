<?php
function addWatermark($sourceImagePath, $destinationImagePath, $watermark, $position = 'bottom-right', $textFontSize = 12, $imageWidthRatio = 0.2, $imageHeightRatio = 0.2) {
  // Obtener la información de la imagen original
  $info = getimagesize($sourceImagePath);
  $mime = $info['mime'];

  // Determinar el tipo de imagen y crear la imagen correspondiente
  switch ($mime) {
    case 'image/jpeg':
      $image = imagecreatefromjpeg($sourceImagePath);
      break;
    case 'image/png':
      $image = imagecreatefrompng($sourceImagePath);
      imagealphablending($image, true);
      break;
    default:
      echo "Formato de imagen no compatible: $mime";
      return false;
  }

  // Función para obtener dimensiones de texto
  function getTextDimensions($text, $font, $fontSize) {
    $bbox = imagettfbbox($fontSize, 0, $font, $text);
    return [
      'width'  => $bbox[2] - $bbox[0],
      'height' => $bbox[1] - $bbox[7]
    ];
  }

  // Determinar si es texto o imagen de marca de agua
  if (is_file($watermark)) {
    // Es imagen
    $watermarkInfo = getimagesize($watermark);
    $watermarkMime = $watermarkInfo['mime'];

    // Crear la imagen de la marca de agua
    switch ($watermarkMime) {
      case 'image/jpeg':
        $watermarkImg = imagecreatefromjpeg($watermark);
        break;
      case 'image/png':
        $watermarkImg = imagecreatefrompng($watermark);
        imagealphablending($watermarkImg, true); // Activar mezcla de transparencia
        imagesavealpha($watermarkImg, true); // Guardar la transparencia
        break;
      default:
        echo "Formato de marca de agua no compatible: $watermarkMime";
        return false;
    }

    // Obtener dimensiones de las imágenes
    $imageWidth      = imagesx($image);
    $imageHeight     = imagesy($image);
    $watermarkWidth  = imagesx($watermarkImg);
    $watermarkHeight = imagesy($watermarkImg);

    // Calcular las nuevas dimensiones para la imagen de marca de agua
    $newWatermarkWidth  = $imageWidth * $imageWidthRatio;
    $newWatermarkHeight = $imageHeight * $imageHeightRatio;

    // Redimensionar la imagen de marca de agua
    $resizedWatermark = imagecreatetruecolor($newWatermarkWidth, $newWatermarkHeight);
    imagealphablending($resizedWatermark, false); // Desactivar mezcla de color
    imagesavealpha($resizedWatermark, true); // Guardar transparencia
    imagecopyresampled($resizedWatermark, $watermarkImg, 0, 0, 0, 0, $newWatermarkWidth, $newWatermarkHeight, $watermarkWidth, $watermarkHeight);

    // Determinar la posición de la marca de agua
    switch ($position) {
      case 'top-left':
        $destX = 0;
        $destY = 0;
        break;
      case 'top-right':
        $destX = $imageWidth - $newWatermarkWidth;
        $destY = 0;
        break;
      case 'bottom-left':
        $destX = 0;
        $destY = $imageHeight - $newWatermarkHeight;
        break;
      case 'bottom-right':
        $destX = $imageWidth - $newWatermarkWidth;
        $destY = $imageHeight - $newWatermarkHeight;
        break;
      case 'top-center':
        $destX = ($imageWidth - $newWatermarkWidth) / 2;
        $destY = 0;
        break;
      case 'bottom-center':
        $destX = ($imageWidth - $newWatermarkWidth) / 2;
        $destY = $imageHeight - $newWatermarkHeight;
        break;
      case 'center-left':
        $destX = 0;
        $destY = ($imageHeight - $newWatermarkHeight) / 2;
        break;
      case 'center-right':
        $destX = $imageWidth - $newWatermarkWidth;
        $destY = ($imageHeight - $newWatermarkHeight) / 2;
        break;
      case 'center':
        $destX = ($imageWidth - $newWatermarkWidth) / 2;
        $destY = ($imageHeight - $newWatermarkHeight) / 2;
        break;
      default:
        echo "Posición de marca de agua no válida: $position";
        return false;
    }

    // Fusionar la imagen de marca de agua redimensionada con la imagen original
    imagecopy($image, $resizedWatermark, $destX, $destY, 0, 0, $newWatermarkWidth, $newWatermarkHeight);

    // Liberar memoria de la imagen de marca de agua redimensionada
    imagedestroy($resizedWatermark);
    imagedestroy($watermarkImg);
  } elseif (is_string($watermark)) {
    // Es texto
    $font      = 'ttf/FiraCode.ttf'; // Ruta al archivo de fuente TTF, ajusta según tu configuración
    $textColor = imagecolorallocate($image, 255, 255, 255); // Color del texto (blanco en este caso)
    $text      = $watermark;

    // Calcular el tamaño de la fuente dinámicamente
    $textDimensions = getTextDimensions($text, $font, $textFontSize);

    // Calcular la posición para el texto
    switch ($position) {
      case 'top-left':
        $destX = 10;
        $destY = 10 + $textDimensions['height'];
        break;
      case 'top-right':
        $destX = imagesx($image) - $textDimensions['width'] - 10;
        $destY = 10 + $textDimensions['height'];
        break;
      case 'bottom-left':
        $destX = 10;
        $destY = imagesy($image) - 10;
        break;
      case 'bottom-right':
        $destX = imagesx($image) - $textDimensions['width'] - 10;
        $destY = imagesy($image) - 10;
        break;
      case 'top-center':
        $destX = (imagesx($image) - $textDimensions['width']) / 2;
        $destY = 10 + $textDimensions['height'];
        break;
      case 'bottom-center':
        $destX = (imagesx($image) - $textDimensions['width']) / 2;
        $destY = imagesy($image) - 10;
        break;
      case 'center-left':
        $destX = 10;
        $destY = (imagesy($image) - $textDimensions['height']) / 2 + $textDimensions['height'];
        break;
      case 'center-right':
        $destX = imagesx($image) - $textDimensions['width'] - 10;
        $destY = (imagesy($image) - $textDimensions['height']) / 2 + $textDimensions['height'];
        break;
      case 'center':
        $destX = (imagesx($image) - $textDimensions['width']) / 2;
        $destY = (imagesy($image) - $textDimensions['height']) / 2 + $textDimensions['height'];
        break;
      default:
        echo "Posición de marca de agua no válida: $position";
        return false;
    }

    // Agregar texto como marca de agua
    imagettftext($image, $textFontSize, 0, $destX, $destY, $textColor, $font, $text);
  } else {
    echo "Marca de agua no válida: $watermark";
    return false;
  }

  // Guardar la imagen con la marca de agua
  switch ($mime) {
    case 'image/jpeg':
      imagejpeg($image, $destinationImagePath, 75); // 75 es la calidad del 0 al 100
      break;
    case 'image/png':
      imagepng($image, $destinationImagePath, 7); // 9 es el nivel de compresión del 0 al 9
      break;
  }

  // Liberar memoria de la imagen original
  imagedestroy($image);

  echo "Se ha agregado la marca de agua y guardado en: $destinationImagePath";
}

// Ejemplo de uso con texto como marca de agua en la esquina inferior derecha
$sourceImagePath      = 'poster.jpg'; // Ruta de la imagen original
$destinationImagePath = 'poster_marca_de_agua_2.png'; // Ruta donde guardar la imagen con marca de agua
$watermarkText        = 'mg.png'; // Texto o ruta de imagen de marca de agua
$position             = 'bottom-right'; // Posición de la marca de agua: top-left, top-right, bottom-left, bottom-right, top-center, bottom-center, center-left, center-right, center
$textFontSize         = 100; // Tamaño de la fuente para el texto (en puntos)
$imageWidthRatio      = 0.1; // Proporción del ancho de la imagen de marca de agua en relación con la imagen original
$imageHeightRatio     = 0.08; // Proporción de la altura de la imagen de marca de agua en relación con la imagen original

// Llamar a la función para agregar la marca de agua
addWatermark($sourceImagePath, $destinationImagePath, $watermarkText, $position, $textFontSize, $imageWidthRatio, $imageHeightRatio);

?>
<br>
<img src="<?= $destinationImagePath ?>" width="350">
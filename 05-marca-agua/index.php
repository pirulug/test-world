<?php
function addWatermark($sourceImagePath, $destinationImagePath, $watermark, $position = 'bottom-right') {
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
            break;
        default:
            echo "Formato de imagen no compatible: $mime";
            return false;
    }

    // Función para obtener dimensiones de texto
    function getTextDimensions($text, $font, $fontSize) {
        $bbox = imagettfbbox($fontSize, 0, $font, $text);
        return [
            'width' => $bbox[2] - $bbox[0],
            'height' => $bbox[1] - $bbox[7]
        ];
    }

    // Determinar si es texto o imagen de marca de agua
    if (is_string($watermark)) {
        // Es texto
        $font = 'ttf/FiraCode.ttf'; // Ruta al archivo de fuente TTF, ajusta según tu configuración
        $textColor = imagecolorallocate($image, 255, 255, 255); // Color del texto (blanco en este caso)
        $text = $watermark;

        // Calcular tamaño de fuente inicial
        $fontSize = 36; // Tamaño de fuente inicial
        $textDimensions = getTextDimensions($text, $font, $fontSize);

        // Ajustar el tamaño de la fuente si es necesario
        while ($textDimensions['width'] > imagesx($image) * 0.8) {
            $fontSize--;
            $textDimensions = getTextDimensions($text, $font, $fontSize);
        }

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
        imagettftext($image, $fontSize, 0, $destX, $destY, $textColor, $font, $text);
    } elseif (is_file($watermark)) {
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
                break;
            default:
                echo "Formato de marca de agua no compatible: $watermarkMime";
                return false;
        }

        // Ajustar tamaño de la imagen de marca de agua si es necesario
        $watermarkWidth = imagesx($watermarkImg);
        $watermarkHeight = imagesy($watermarkImg);
        $maxWatermarkSize = min(imagesx($image), imagesy($image)) * 0.25; // Máximo 25% del tamaño de la imagen original

        if ($watermarkWidth > $maxWatermarkSize || $watermarkHeight > $maxWatermarkSize) {
            $ratio = $maxWatermarkSize / max($watermarkWidth, $watermarkHeight);
            $newWidth = $watermarkWidth * $ratio;
            $newHeight = $watermarkHeight * $ratio;
            $resizedWatermarkImg = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resizedWatermarkImg, $watermarkImg, 0, 0, 0, 0, $newWidth, $newHeight, $watermarkWidth, $watermarkHeight);
            imagedestroy($watermarkImg);
            $watermarkImg = $resizedWatermarkImg;
            $watermarkWidth = $newWidth;
            $watermarkHeight = $newHeight;
        }

        // Determinar la posición de la marca de agua
        switch ($position) {
            case 'top-left':
                $destX = 0;
                $destY = 0;
                break;
            case 'top-right':
                $destX = imagesx($image) - $watermarkWidth;
                $destY = 0;
                break;
            case 'bottom-left':
                $destX = 0;
                $destY = imagesy($image) - $watermarkHeight;
                break;
            case 'bottom-right':
                $destX = imagesx($image) - $watermarkWidth;
                $destY = imagesy($image) - $watermarkHeight;
                break;
            case 'top-center':
                $destX = (imagesx($image) - $watermarkWidth) / 2;
                $destY = 0;
                break;
            case 'bottom-center':
                $destX = (imagesx($image) - $watermarkWidth) / 2;
                $destY = imagesy($image) - $watermarkHeight;
                break;
            case 'center-left':
                $destX = 0;
                $destY = (imagesy($image) - $watermarkHeight) / 2;
                break;
            case 'center-right':
                $destX = imagesx($image) - $watermarkWidth;
                $destY = (imagesy($image) - $watermarkHeight) / 2;
                break;
            case 'center':
                $destX = (imagesx($image) - $watermarkWidth) / 2;
                $destY = (imagesy($image) - $watermarkHeight) / 2;
                break;
            default:
                echo "Posición de marca de agua no válida: $position";
                return false;
        }

        // Fusionar la imagen de marca de agua con la imagen original
        imagecopy($image, $watermarkImg, $destX, $destY, 0, 0, $watermarkWidth, $watermarkHeight);

        // Liberar memoria de la imagen de marca de agua
        imagedestroy($watermarkImg);
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
            imagepng($image, $destinationImagePath, 9); // 9 es el nivel de compresión del 0 al 9
            break;
    }

    // Liberar memoria de la imagen original
    imagedestroy($image);

    echo "Se ha agregado la marca de agua y guardado en: $destinationImagePath";
}

// Ejemplo de uso con texto como marca de agua en la esquina inferior derecha
$sourceImagePath = 'poster.jpg'; // Ruta de la imagen original
$destinationImagePath = 'poster_marca_de_agua.jpg'; // Ruta donde guardar la imagen con marca de agua
$watermark = 'Pirulug'; // Texto o ruta de imagen de marca de agua
$position = 'bottom-right'; // Posición de la marca de agua: top-left, top-right, bottom-left, bottom-right, top-center, bottom-center, center-left, center-right, center

// Llamar a la función para agregar la marca de agua
addWatermark($sourceImagePath, $destinationImagePath, $watermark, $position);
?>

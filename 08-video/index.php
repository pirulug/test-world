<?php
// Ruta al archivo de video en el servidor
$video_path = 'video.mp4';

// Definir los headers para streaming
header('Content-Type: video/mp4');
header('Content-Length: ' . filesize($video_path));
header('Content-Disposition: inline; filename="video.mp4"');

// Leer y enviar el video al navegador
readfile($video_path);
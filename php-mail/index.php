<?php
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
  // Configuración del servidor SMTP
  $mail->isSMTP();
  $mail->Host       = 'smtp.gmail.com'; // Dirección del servidor SMTP
  $mail->SMTPAuth   = true;
  $mail->Username   = 'gele.omfg@gmail.com'; // Tu dirección de correo
  $mail->Password   = 'zivu quga vxyy timl'; // Tu contraseña de correo
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port       = 587; // Puerto SMTP para TLS

  $mail->setLanguage('es', 'PHPMailer/language/');

  // Remitente y destinatarios
  $mail->setFrom('gele.omfg@gmail.com', 'Gele Omfg');
  $mail->addAddress('guidolaes@gmail.com', 'Guido Laes');

  // Contenido del correo
  $mail->isHTML(true); // Establecer el formato de correo como HTML
  $mail->Subject = 'Asunto del Correo';
  $mail->Body    = 'Este es el <b>cuerpo</b> del correo en formato HTML.';
  $mail->AltBody = 'Este es el cuerpo del correo en formato texto plano para clientes de correo que no soportan HTML.';

  $mail->send();
  echo 'El mensaje ha sido enviado';
} catch (Exception $e) {
  echo "El mensaje no pudo ser enviado. Error de correo: {$mail->ErrorInfo}";
}

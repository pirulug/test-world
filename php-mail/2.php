<?php
// Dirección del servidor SMTP y puerto
$smtpHost = 'smtp.gmail.com';
$smtpPort = 587;

// Dirección de correo electrónico y contraseña
$smtpUsername = 'gele.omfg@gmail.com';
$smtpPassword = 'sseq pdhh blmu vzjc';

// Dirección de correo electrónico del remitente y del destinatario
$fromEmail = 'gele.omfg@gmail.com';
$toEmail   = 'guidolaes♠4gmail.com';

// Asunto y cuerpo del correo electrónico
$subject = 'Asunto del Correo';
$body    = 'Este es el cuerpo del correo.';

// Construir el mensaje del correo electrónico
$message = wordwrap($body, 70);

// Construir los datos de autenticación
$credentials = base64_encode("$smtpUsername:$smtpPassword");

// Establecer las cabeceras del correo electrónico
$headers = "From: $fromEmail\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Transfer-Encoding: 8bit\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "X-Originating-IP: " . $_SERVER['SERVER_ADDR'] . "\r\n";

// Establecer el contexto de conexión SSL/TLS
$context = stream_context_create([
  'ssl' => [
    'verify_peer'       => false,
    'verify_peer_name'  => false,
    'allow_self_signed' => true
  ]
]);

// Establecer la conexión con el servidor SMTP
$smtpConnection = stream_socket_client("tcp://$smtpHost:$smtpPort", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);

if ($smtpConnection === false) {
  echo "Error al conectarse al servidor SMTP: $errstr ($errno)";
} else {
  // Leer el mensaje de bienvenida del servidor SMTP
  $welcomeMessage = fgets($smtpConnection);

  // Autenticarse con el servidor SMTP
  fputs($smtpConnection, "EHLO example.com\r\n");
  $authResponse = fgets($smtpConnection);

  fputs($smtpConnection, "AUTH LOGIN\r\n");
  $authResponse = fgets($smtpConnection);

  fputs($smtpConnection, "$credentials\r\n");
  $authResponse = fgets($smtpConnection);

  // Enviar el correo electrónico
  fputs($smtpConnection, "MAIL FROM: <$fromEmail>\r\n");
  $fromResponse = fgets($smtpConnection);

  fputs($smtpConnection, "RCPT TO: <$toEmail>\r\n");
  $toResponse = fgets($smtpConnection);

  fputs($smtpConnection, "DATA\r\n");
  $dataResponse = fgets($smtpConnection);

  fputs($smtpConnection, "Subject: $subject\r\n");
  fputs($smtpConnection, "From: $fromEmail\r\n");
  fputs($smtpConnection, "To: $toEmail\r\n");
  fputs($smtpConnection, "Content-Type: text/html; charset=UTF-8\r\n");
  fputs($smtpConnection, "\r\n");
  fputs($smtpConnection, "$message\r\n");
  fputs($smtpConnection, ".\r\n");

  // Finalizar la sesión SMTP
  fputs($smtpConnection, "QUIT\r\n");

  // Leer la respuesta del servidor SMTP después de enviar el correo
  $quitResponse = fgets($smtpConnection);

  // Cerrar la conexión
  fclose($smtpConnection);

  // Verificar si el correo se ha enviado correctamente
  if (strpos($quitResponse, '250') !== false) {
    echo 'El correo electrónico ha sido enviado con éxito.';
  } else {
    echo 'Error al enviar el correo electrónico.';
  }
}
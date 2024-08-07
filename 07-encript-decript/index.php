<?php
require 'SecureCrypt.php';

// Llave secreta para la encriptación y desencriptación
$key = 'mi_llave_secret';

// Instanciar la clase SecureCrypt
$crypt = new SecureCrypt($key);

// Texto a encriptar
$plaintext = 'Este es un texto secreto';

// Encriptar el texto
$encrypted = $crypt->encrypt($plaintext);
echo "Texto encriptado: $encrypted <br>";

// Desencriptar el texto
try {
  $decrypted = $crypt->decrypt($encrypted);
  echo "Texto desencriptado: $decrypted <br>";

  echo $decrypted = $crypt->decrypt("qIMOikywYTRhv8rJ7u+pL1sodkIf3LK/Ww891wARtTZk2v8EQd6loQ4WJJ/bS4D6NXMya2plZGhGMklHazZYeDJMbldvc0Q5M080bW9PaHFZNUtBbUxTb0xyWT0=");
} catch (Exception $e) {
  echo "Error: " . $e->getMessage();
}

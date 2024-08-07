<?php
// Función para listar archivos y carpetas en un directorio
function listarContenido($ruta) {
  $files = scandir($ruta);
  echo "<ul>";
  // Agregar enlace para retroceder a la carpeta anterior si no estamos en la carpeta raíz
  if ($ruta != './uploads') {
    $ruta_anterior = dirname($ruta);
    echo "<li><a href='?dir=$ruta_anterior'>../ (Retroceder)</a></li>";
  }
  foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
      $file_path = "$ruta/$file";
      echo "<li>";
      if (is_dir($file_path)) {
        echo "<a href='?dir=$file_path'>$file</a> - ";
        echo "<a href='?action=deleteFolder&dir=$file_path'>Eliminar</a> - ";
        echo "<form action='' method='post' style='display:inline;'>
                <input type='hidden' name='item' value='$file_path'>
                <select name='destination'>";
        echo "<option value=''>Seleccione destino</option>";
        echo "<option value='$ruta'>Actual</option>";
        echo "<option value='$ruta_anterior'>Retroceder</option>";

        // Listar carpetas existentes como opciones de destino
        $carpetas = glob($ruta . '/*', GLOB_ONLYDIR);
        foreach ($carpetas as $carpeta) {
          echo "<option value='$carpeta'>$carpeta</option>";
        }

        echo "</select>";
        echo "<button type='submit' name='move'>Mover</button>";
        echo "</form>";
      } else {
        echo "<a href='$ruta/$file' target='_blanck'>$file</a> - ";
        echo "<a href='?action=deleteFile&file=$file'>Eliminar</a> - ";
        echo "<form action='' method='post' style='display:inline;'>
                <input type='hidden' name='item' value='$file_path'>
                <select name='destination'>";
        echo "<option value=''>Seleccione destino</option>";
        echo "<option value='$ruta'>Actual</option>";
        echo "<option value='$ruta_anterior'>Retroceder</option>";

        // Listar carpetas existentes como opciones de destino
        $carpetas = glob($ruta . '/*', GLOB_ONLYDIR);
        foreach ($carpetas as $carpeta) {
          echo "<option value='$carpeta'>$carpeta</option>";
        }

        echo "</select>";
        echo "<button type='submit' name='move'>Mover</button>";
        echo "</form>";
      }
      echo "</li>";
    }
  }
  echo "</ul>";
}

// Ruta predeterminada para el directorio raíz
$directorio = './uploads';

// Si se especifica un directorio en la URL, navegar a esa carpeta
if (isset($_GET['dir'])) {
  // Verificar si la carpeta especificada está dentro de './uploads/'
  $ruta_solicitada = $_GET['dir'];
  if (strpos($ruta_solicitada, $directorio) === 0) {
    $directorio = $ruta_solicitada;
  }
}


// Acciones para eliminar archivos y carpetas
if (isset($_GET['action'])) {
  if ($_GET['action'] === 'deleteFile') {
    $fileToDelete = $_GET['file'];
    $filePath     = "$directorio/$fileToDelete";
    if (file_exists($filePath)) {
      if (is_file($filePath)) {
        unlink($filePath);
        echo "¡Archivo eliminado con éxito!";
      } else {
        echo "No se puede eliminar. No es un archivo.";
      }
    } else {
      echo "El archivo no existe.";
    }
  } elseif ($_GET['action'] === 'deleteFolder') {
    $folderToDelete = rtrim($_GET['dir'], '/');
    if (is_dir($folderToDelete)) {
      // Recursively delete folder and its contents
      $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folderToDelete, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
      );
      foreach ($files as $fileinfo) {
        $action = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $action($fileinfo->getRealPath());
      }
      rmdir($folderToDelete);
      echo "¡Carpeta eliminada con éxito!";

      $ruta_anterior = dirname($folderToDelete);
      header("Location: ?dir=$ruta_anterior");
    } else {
      echo "No se puede eliminar. No es una carpeta.";
    }
  }
}

// Acción para mover archivos y carpetas
if (isset($_POST['move']) && isset($_POST['destination']) && isset($_POST['item'])) {
  $destination = $_POST['destination'];
  $item        = $_POST['item'];

  if (file_exists($item)) {
    $item_name    = basename($item);
    $new_location = $destination . '/' . $item_name;

    // Verificar si el destino es diferente del origen
    if ($item !== $destination && !is_descendant($item, $destination)) {
      if (rename($item, $new_location)) {
        echo "¡$item_name movido a $destination con éxito!";
      } else {
        echo "Error al mover $item_name.";
      }
    } else {
      echo "No se puede mover la carpeta dentro de sí misma.";
    }
  } else {
    echo "El archivo/carpeta no existe.";
  }
}

// Función para verificar si una ruta es descendiente de otra
function is_descendant($path, $potential_parent) {
  $path             = realpath($path);
  $potential_parent = realpath($potential_parent);

  return strpos($path, $potential_parent) === 0 && $path !== $potential_parent;
}

// Verificar si se ha enviado una solicitud para crear una carpeta nueva
if (isset($_POST['folder_name'])) {
  $folderName    = $_POST['folder_name'];
  $newFolderPath = $directorio . '/' . $folderName;
  if (!file_exists($newFolderPath)) {
    mkdir($newFolderPath);
    echo "¡Carpeta creada con éxito!";
  } else {
    echo "La carpeta ya existe.";
  }
}

// Verificar si se ha enviado una solicitud para cargar un archivo
if (isset($_FILES['file'])) {
  $file            = $_FILES['file'];
  $fileName        = $file['name'];
  $fileTmpName     = $file['tmp_name'];
  $fileDestination = $directorio . '/' . $fileName;

  if (move_uploaded_file($fileTmpName, $fileDestination)) {
    echo "¡Archivo cargado con éxito!";
  } else {
    echo "Error al cargar el archivo.";
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestor de Archivos y Carpetas</title>
</head>

<body>
  <h1>Gestor de Archivos y Carpetas</h1>

  <h2>Crear Carpeta</h2>
  <form action="" method="post">
    <input type="text" name="folder_name" placeholder="Nombre de la Carpeta" required>
    <button type="submit">Crear Carpeta</button>
  </form>

  <h2>Subir Archivo</h2>
  <form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Subir Archivo</button>
  </form>

  <h2>Contenido del Directorio Actual: <?php echo $directorio; ?></h2>
  <?php listarContenido($directorio); ?>
</body>

</html>
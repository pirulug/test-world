<?php
// Conexión a la base de datos utilizando PDO
$dsn        = 'mysql:host=localhost;dbname=test';
$usuario    = 'root';
$contraseña = '';

try {
  $conexion = new PDO($dsn, $usuario, $contraseña);
} catch (PDOException $e) {
  echo 'Error al conectarse a la base de datos: ' . $e->getMessage();
  exit;
}

$fecha = date('Y-m-d');

// Verificar si ya hay una entrada para hoy
$query = $conexion->prepare("SELECT contador FROM visitas WHERE fecha = :fecha");
$query->bindParam(':fecha', $fecha);
$query->execute();
$resultado = $query->fetch(PDO::FETCH_ASSOC);

if ($resultado) {
  // Si ya existe una entrada para hoy, actualizar el contador
  $query = $conexion->prepare("UPDATE visitas SET contador = contador + 1 WHERE fecha = :fecha");
  $query->bindParam(':fecha', $fecha);
  $query->execute();
} else {
  // Si no existe una entrada para hoy, insertar una nueva fila
  $query = $conexion->prepare("INSERT INTO visitas (fecha, contador) VALUES (:fecha, 1)");
  $query->bindParam(':fecha', $fecha);
  $query->execute();
}

// Obtener el número total de visitas
$totalVisitas   = 0;
$query          = $conexion->query("SELECT SUM(contador) as total FROM visitas");
$totalResultado = $query->fetch(PDO::FETCH_ASSOC);
if ($totalResultado) {
  $totalVisitas = $totalResultado['total'];
}

// Obtener el número de visitas de hoy
$hoy   = date('Y-m-d');
$query = $conexion->prepare("SELECT contador FROM visitas WHERE fecha = :fecha");
$query->bindParam(':fecha', $hoy);
$query->execute();
$visitasHoyResultado = $query->fetch(PDO::FETCH_ASSOC);
$visitasHoy          = $visitasHoyResultado ? $visitasHoyResultado['contador'] : 0;

// Obtener el número de visitas de ayer
$ayer = date('Y-m-d', strtotime('-1 day'));
$query->bindParam(':fecha', $ayer);
$query->execute();
$visitasAyerResultado = $query->fetch(PDO::FETCH_ASSOC);
$visitasAyer          = $visitasAyerResultado ? $visitasAyerResultado['contador'] : 0;

// Obtener el número de visitas de esta semana
$inicioSemana = date('Y-m-d', strtotime('monday this week'));
$query        = $conexion->prepare("SELECT SUM(contador) as total FROM visitas WHERE fecha >= :inicioSemana");
$query->bindParam(':inicioSemana', $inicioSemana);
$query->execute();
$visitasSemanaResultado = $query->fetch(PDO::FETCH_ASSOC);
$visitasSemana          = $visitasSemanaResultado ? $visitasSemanaResultado['total'] : 0;

// Obtener el número de visitas de la semana pasada
$inicioSemanaPasada = date('Y-m-d', strtotime('monday last week'));
$finSemanaPasada    = date('Y-m-d', strtotime('sunday last week'));
$query              = $conexion->prepare("SELECT SUM(contador) as total FROM visitas WHERE fecha BETWEEN :inicioSemanaPasada AND :finSemanaPasada");
$query->bindParam(':inicioSemanaPasada', $inicioSemanaPasada);
$query->bindParam(':finSemanaPasada', $finSemanaPasada);
$query->execute();
$visitasSemanaPasadaResultado = $query->fetch(PDO::FETCH_ASSOC);
$visitasSemanaPasada          = $visitasSemanaPasadaResultado ? $visitasSemanaPasadaResultado['total'] : 0;

// Obtener el número de visitas de este mes
$inicioMes = date('Y-m-01');
$query     = $conexion->prepare("SELECT SUM(contador) as total FROM visitas WHERE fecha >= :inicioMes");
$query->bindParam(':inicioMes', $inicioMes);
$query->execute();
$visitasMesResultado = $query->fetch(PDO::FETCH_ASSOC);
$visitasMes          = $visitasMesResultado ? $visitasMesResultado['total'] : 0;

// Obtener el número de visitas del mes pasado
$inicioMesPasado = date('Y-m-01', strtotime('first day of last month'));
$finMesPasado    = date('Y-m-t', strtotime('last day of last month'));
$query           = $conexion->prepare("SELECT SUM(contador) as total FROM visitas WHERE fecha BETWEEN :inicioMesPasado AND :finMesPasado");
$query->bindParam(':inicioMesPasado', $inicioMesPasado);
$query->bindParam(':finMesPasado', $finMesPasado);
$query->execute();
$visitasMesPasadoResultado = $query->fetch(PDO::FETCH_ASSOC);
$visitasMesPasado          = $visitasMesPasadoResultado ? $visitasMesPasadoResultado['total'] : 0;

// Mostrar los resultados
echo "Hoy\t" . (isset($visitasHoy) ? str_pad($visitasHoy, 6, " ", STR_PAD_LEFT) : "0") . PHP_EOL . "<br>";
echo "Ayer\t" . (isset($visitasAyer) ? str_pad($visitasAyer, 6, " ", STR_PAD_LEFT) : "0") . PHP_EOL . "<br>";
echo "Esta semana\t" . (isset($visitasSemana) ? str_pad($visitasSemana, 6, " ", STR_PAD_LEFT) : "0") . PHP_EOL . "<br>";
echo "La semana pasada\t" . (isset($visitasSemanaPasada) ? str_pad($visitasSemanaPasada, 6, " ", STR_PAD_LEFT) : "0") . PHP_EOL . "<br>";
echo "Este mes\t" . (isset($visitasMes) ? str_pad($visitasMes, 6, " ", STR_PAD_LEFT) : "0") . PHP_EOL . "<br>";
echo "El mes pasado\t" . (isset($visitasMesPasado) ? str_pad($visitasMesPasado, 6, " ", STR_PAD_LEFT) : "0") . PHP_EOL . "<br>";
echo "Todos los días\t" . (isset($totalVisitas) ? str_pad($totalVisitas, 6, " ", STR_PAD_LEFT) : "0") . PHP_EOL . "<br>";

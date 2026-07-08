<?php
require '../../extensiones/csv-master/autoload.php';
use League\Csv\Writer;

// Configurar la respuesta HTTP para que se descargue como CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="anexo_de_ventas.csv"');

// Crear el archivo CSV en memoria
$csv = Writer::createFromFileObject(new SplTempFileObject());

// Agregar el encabezado del CSV
$csv->insertOne(['Fecha', 'Col2', 'Col3', 'Col4', 'Col21']); // Ajusta según tus columnas

// Conectar a la base de datos
$conexion = new mysqli("localhost", "root", "", "fox_control");

// Verifica la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener los datos de la tabla (ajusta la consulta según tu estructura)
$resultado = $conexion->query("SELECT fecEmi, tipoDte, tipoDte, tipoDte, tipoDte FROM facturas_locales");

while ($fila = $resultado->fetch_assoc()) {
    // Convertir la fecha al formato correcto (DD/MM/YYYY)
    $fecha = date('d/m/Y', strtotime($fila['fecEmi']));

    // Asegurar que las columnas numéricas mantengan los ceros a la izquierda
    $tipoDte = str_pad($fila['tipoDte'], 2, '0', STR_PAD_LEFT);

    // Agregar fila al CSV
    $csv->insertOne([$fecha, $tipoDte, $tipoDte, $tipoDte, $tipoDte]);
}

// Enviar el contenido del CSV al navegador
echo $csv;
exit;
?>

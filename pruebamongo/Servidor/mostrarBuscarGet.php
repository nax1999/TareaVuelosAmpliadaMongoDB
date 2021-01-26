<?php
require 'vendor/autoload.php';
$cliente = new MongoDB\Client("mongodb://localhost:27017");
$array = $cliente->adat_vuelos_ampliada->clientes;

if (isset($_GET["fecha"]) && isset($_GET["origen"]) && isset($_GET["destino"])) {
    $fecha = $_GET["fecha"];
    $origen = $_GET["origen"];
    $destino = $_GET["destino"];
    $result = $array->find(['fecha' => $fecha, 'origen' => $origen, 'destino' => $destino]);
} elseif (isset($_GET["fecha"]) && isset($_GET["origen"])) {
    $origen = $_GET["origen"];
    $fecha = $_GET["fecha"];
    $result = $array->find(['origen' => $origen, 'fecha' => $fecha]);
} else {
    $result = $array->find();
}

$contador = 0;

$sizeCollection = $array->count();
$arrayVuelos = array();

if (isset($result) && $result) {
    if ($sizeCollection > 0) {
        foreach ($result as $entry) {


            $arrayVuelo = array();
            $arrayVuelo["codigo"] = $entry["codigo"];
            $arrayVuelo["origen"] = $entry["origen"];
            $arrayVuelo["destino"] = $entry["destino"];
            $arrayVuelo["fecha"] = $entry["fecha"];
            $arrayVuelo["hora"] = $entry["hora"];
            $arrayVuelo["plazas_totales"] = $entry["plazas_totales"];
            $arrayVuelo["plazas_disponibles"] = $entry["plazas_disponibles"];
            $arrayVuelo["precio"] = $entry["precio"];


            $arrayVuelos[] = $arrayVuelo;

            $contador++;
        }
        $arrayMensaje["estado"] = true;
        $arrayMensaje["encontrados"] = $contador;
        $arrayMensaje["vuelos"] = $arrayVuelos;
        $mensajeJSON = json_encode($arrayMensaje, JSON_PRETTY_PRINT);
    } else {
        $arrayMensaje["estado"] = true;
        $arrayMensaje["encontrados"] = 0;
    }
} else {
    $arrayMensaje["estado"] = "error";
    $arrayMensaje["mensaje"] = "Se ha producido un error al conectar con la BD";
}
if (isset($_GET["debug"]) && $_GET["debug"] == 1) {
    echo $mensajeJSON;
} else {
    echo $mensajeJSON;
}

?>

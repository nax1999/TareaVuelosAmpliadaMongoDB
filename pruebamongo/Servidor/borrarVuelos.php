<?php
require 'vendor/autoload.php';
$cliente = new MongoDB\Client("mongodb://localhost:27017");
$coleccion = $cliente->adat_vuelos_ampliada->clientes;
header('Access-Control-Allow-Origin: *');

function Borrar($entry)
{
    $aux = false;
    if (isset($entry["codigo"])
        && isset($entry["dni"])
        && isset($entry["codigoVenta"])) {
        $aux = true;
    }
    return $aux;
}

$arrayMensaje = array();
$parameters = file_get_contents("php://input");
if (isset($parameters)) {
    $data = json_decode($parameters, true);
    if (Borrar($data)) {
        $codigo = $data["codigo"];
        $dni = $data["dni"];
        $codigoVenta = $data["codigoVenta"];
        $resultado = $coleccion->updateOne(array("codigo" => $codigo), array('$pull' => array('vendidos' => array('dni' => $dni, 'codigoVenta' => $codigoVenta))));
        if (isset ($resultado) && $resultado) {
            $arrayMensaje["estado"] = true;
        }
    }
} else {
    $arrayMensaje["estado"] = false;
    $arrayMensaje["mensaje"] = "No se ha podido realizar la eliminación se ha producido un error en los datos";
}
$mensajeJSON = json_encode($arrayMensaje);
echo $mensajeJSON;
?>
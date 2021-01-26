<?php
require 'vendor/autoload.php';
$cliente = new MongoDB\Client("mongodb://localhost:27017");
$coleccion = $cliente->adat_vuelos_ampliada->clientes;
header('Access-Control-Allow-Origin: *');


function Actualizar($recibido)
{
    $aux = false;
    if (isset($recibido["codigo"])
        && isset($recibido["dni"])
        && isset($recibido["codigoVenta"])
        && isset($recibido["dniNuevo"])
        && isset($recibido["apellido"])
        && isset($recibido["nombre"])) {
        $aux = true;
    }
    return $aux;
}
$arrMensaje = array();
$parameters = file_get_contents("php://input");

if (isset($parameters)) {
    $mensajeRecibido = json_decode($parameters, true);
    if (Actualizar($mensajeRecibido)) {
        $codigo = $mensajeRecibido["codigo"];
        $dni = $mensajeRecibido["dni"];
        $codigoVenta = $mensajeRecibido["codigoVenta"];
        $dniNuevo = $mensajeRecibido["dniNuevo"];
        $apellido = $mensajeRecibido["apellido"];
        $nombre = $mensajeRecibido["nombre"];
        $resultado = $coleccion->updateOne(
            ['vendidos.dni' => $dni,
                'vendidos.codigoVenta' => $codigoVenta,
                'codigo' => $codigo],
            ['$set' => ["vendidos.$.apellido" => $apellido,
                "vendidos.$.nombre" => $nombre,
                "vendidos.$.dni" => $dniNuevo]]
        );
        if (isset ($resultado) && $resultado) {
            $arrMensaje["estado"] = true;
        } else {
            $arrMensaje["estado"] = false;
            $arrMensaje["mensaje"] = "No se ha podido realizar la actualización, query erronea";
        }
    } else {
        $arrMensaje["estado"] = false;
        $arrMensaje["mensaje"] = "No se ha podido realizar la actualización, introduzca los campos adecuados";
        $arrMensaje["recibido"] = $mensajeRecibido;
    }
} else {
    $arrMensaje["estado"] = false;
    $arrMensaje["mensaje"] = "No se ha podido modificar por error en los datos recibidos";
}
$mensajeJSON = json_encode($arrMensaje);
echo $mensajeJSON;


?>
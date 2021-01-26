<?php
require 'vendor/autoload.php';
$cliente = new MongoDB\Client("mongodb://localhost:27017");
$coleccion = $cliente->adat_vuelos_ampliada->clientes;
header('Access-Control-Allow-Origin: *');

function Insertar($entry)
{
    $aux = false;
    if (isset($entry["codigo"])
        && isset($entry["dni"])
        && isset($entry["apellido"])
        && isset($entry["nombre"])
        && isset($entry["dniPagador"])
        && isset($entry["tarjeta"])) {
        $aux = true;
    }
    return $aux;
}

$arrayMensaje = array();
$parameters = file_get_contents("php://input");

if (isset($parameters)) {

    $data = json_decode($parameters, true);

    if (Insertar($data)) {
        $asientos = array();
        $asientosOcupados = array();
        $asientosLibres = array();
        $codigo = $data["codigo"];
        $dni = $data["dni"];
        $apellido = $data["apellido"];
        $nombre = $data["nombre"];
        $dniPagador = $data["dniPagador"];
        $tarjeta = $data["tarjeta"];
        $codigoVenta = generarCodigo();
        $resultado = $coleccion->find(array('codigo' => $codigo));

        foreach ($resultado as $entry) {

            $origen = $entry['origen'];
            $destino = $entry['destino'];
            $fecha = $entry['fecha'];
            $hora = $entry['hora'];
            $plazas_totales = $entry['plazas_totales'];
            $arrayVendidos = ((array)$entry["vendidos"]);
        }
        foreach ($arrayVendidos as $valor) {

            $asientoCogido = $valor["asiento"];
            array_push($asientosOcupados, $asientoCogido);
        }

        $cont = 1;
        while ($cont < $plazas_totales + 1) {
            array_push($asientos, $cont);
            $cont++;
        }

        $arrayAsientosLibres = array_diff($asientos, $asientosOcupados);
        $arrayAsientosLibres = array_reverse(array_reverse($arrayAsientosLibres));
        $asientoAsignado = $arrayAsientosLibres[0];
        $nuevosdatos = array('$push' => array('vendidos' => array('asiento' => $asientoAsignado,
            'dni' => $dni,
            'apellido' => $apellido,
            'nombre' => $nombre,
            'dniPagador' => $dniPagador,
            'tarjeta' => $tarjeta,
            'codigoVenta' => $codigoVenta)));
        $result = $coleccion->updateOne(array("codigo" => $codigo), $nuevosdatos);

        if (isset ($result) && $result) {

            $arrayMensaje["estado"] = true;
            $arrayMensaje["codigo"] = $codigo;
            $arrayMensaje["origen"] = $origen;
            $arrayMensaje["destino"] = $destino;
            $arrayMensaje["fecha"] = $fecha;
            $arrayMensaje["hora"] = $hora;
            $arrayMensaje["asiento"] = $asientoAsignado;
            $arrayMensaje["dni"] = $dni;
            $arrayMensaje["apellido"] = $apellido;
            $arrayMensaje["nombre"] = $nombre;
            $arrayMensaje["dniPagador"] = $dniPagador;
            $arrayMensaje["tarjeta"] = $tarjeta;
            $arrayMensaje["codigoVenta"] = $codigoVenta;
            $arrayMensaje["precio"] = 0;

        } else {
            $arrayMensaje["estado"] = false;
            $arrayMensaje["mensaje"] = "No se ha podido realizar la compra,query erronea";
        }

    } else {
        $arrayMensaje["estado"] = false;
        $arrayMensaje["mensaje"] = "No se ha podido realizar la compra, introduzca los campos adecuados";
        $arrayMensaje["recibido"] = $data;
    }
} else {
    $arrayMensaje["estado"] = false;
    $arrayMensaje["mensaje"] = "No se ha podido realizar la compra por un error en algun dato";
}

$mensajeJSON = json_encode($arrayMensaje);
echo $mensajeJSON;

function generarCodigo()
{
    $letrasPermitidas = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $charactersLength = strlen($letrasPermitidas);
    $CodigoGenerado = '';
    for ($i = 0; $i < 10; $i++) {
        $CodigoGenerado .= $letrasPermitidas[rand(0, $charactersLength - 1)];
    }
    return $CodigoGenerado;
}

?>
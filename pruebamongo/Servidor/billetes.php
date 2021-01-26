<?php
require 'vendor/autoload.php';
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        require 'mostrarBuscarGet.php';
        break;
    case 'POST':
        require 'insertarVuelos.php';
        break;
    case 'PUT':
        require 'actualizarVuelos.php';
        break;
    case 'DELETE':
        require 'borrarVuelos.php';
        break;
    default:
        break;
}
?>
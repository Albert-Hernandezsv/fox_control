<?php

require_once "controladores/plantilla.controlador.php";
require_once "controladores/usuarios.controlador.php";
require_once "controladores/categorias.controlador.php";
require_once "controladores/productos.controlador.php";
require_once "controladores/stocks.controlador.php";
require_once "controladores/clientes.controlador.php";
require_once "controladores/facturas.controlador.php";
require_once "controladores/ordenes.controlador.php";

require_once "modelos/usuarios.modelo.php";
require_once "modelos/categorias.modelo.php";
require_once "modelos/productos.modelo.php";
require_once "modelos/stocks.modelo.php";
require_once "modelos/clientes.modelo.php";
require_once "modelos/facturas.modelo.php";
require_once "modelos/ordenes.modelo.php";

$plantilla = new ControladorPlantilla();
$plantilla -> ctrPlantilla();
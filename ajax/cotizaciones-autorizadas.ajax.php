<?php

require_once "../controladores/facturas.controlador.php";
require_once "../modelos/facturas.modelo.php";

session_start();

if(!isset($_SESSION["iniciarSesion"]) || $_SESSION["iniciarSesion"] !== "ok"){
    http_response_code(403);
    echo json_encode(array("ok" => false, "mensaje" => "Sesion no valida."));
    exit;
}

$pagina = isset($_POST["pagina"]) ? max(1, (int) $_POST["pagina"]) : 1;
$rutaActual = isset($_POST["rutaActual"]) ? (string) $_POST["rutaActual"] : "";
$limite = 10;
$total = ControladorFacturas::ctrContarCotizacionesDisponibles();
$totalPaginas = max(1, (int) ceil($total / $limite));
$pagina = min($pagina, $totalPaginas);
$offset = ($pagina - 1) * $limite;
$cotizaciones = ControladorFacturas::ctrMostrarCotizacionesDisponiblesPaginadas($limite, $offset);
$html = "";

foreach($cotizaciones as $key => $cotizacion){
    $fecha = new DateTime($cotizacion["fecEmi"]);
    $html .= '<tr>
        <td>'.($offset + $key + 1).'</td>
        <td>'.htmlspecialchars($cotizacion["cliente_nombre"] ?: "Cliente no disponible", ENT_QUOTES, "UTF-8").'</td>
        <td>'.htmlspecialchars($cotizacion["usuario_nombre"] ?: "Usuario no disponible", ENT_QUOTES, "UTF-8").'</td>
        <td>'.htmlspecialchars($cotizacion["codigo"], ENT_QUOTES, "UTF-8").'</td>
        <td>'.htmlspecialchars($cotizacion["estado"], ENT_QUOTES, "UTF-8").'</td>
        <td>'.$fecha->format('d/m/Y').'</td>
        <td>
            <div class="btn-group">
                <button class="btn btn-success btnUsarCotizacionAutorizada"
                        url="'.htmlspecialchars($rutaActual, ENT_QUOTES, "UTF-8").'"
                        idCotizacionAutorizada="'.(int) $cotizacion["id"].'">
                    <i class="fa fa-pencil-square-o"></i>
                </button>
                <button class="btn btn-danger btnRegresarBodega"
                        idCotizacionAutorizada="'.(int) $cotizacion["id"].'">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </td>
    </tr>';
}

if($html === ""){
    $html = '<tr><td colspan="7" class="text-center">No hay cotizaciones disponibles.</td></tr>';
}

$paginacion = "";
if($totalPaginas > 1){
    $inicio = max(1, $pagina - 2);
    $fin = min($totalPaginas, $pagina + 2);
    $paginacion .= '<ul class="pagination justify-content-center flex-wrap">';
    $paginacion .= '<li class="page-item'.($pagina <= 1 ? ' disabled' : '').'"><button type="button" class="page-link btnPaginaCotizacionesModal" data-pagina="'.max(1, $pagina - 1).'"'.($pagina <= 1 ? ' disabled' : '').'>Anterior</button></li>';
    for($numero = $inicio; $numero <= $fin; $numero++){
        $paginacion .= '<li class="page-item'.($numero === $pagina ? ' active' : '').'"><button type="button" class="page-link btnPaginaCotizacionesModal" data-pagina="'.$numero.'">'.$numero.'</button></li>';
    }
    $paginacion .= '<li class="page-item'.($pagina >= $totalPaginas ? ' disabled' : '').'"><button type="button" class="page-link btnPaginaCotizacionesModal" data-pagina="'.min($totalPaginas, $pagina + 1).'"'.($pagina >= $totalPaginas ? ' disabled' : '').'>Siguiente</button></li>';
    $paginacion .= '</ul>';
}

echo json_encode(array(
    "ok" => true,
    "html" => $html,
    "paginacion" => $paginacion,
    "mostrando" => count($cotizaciones),
    "total" => $total,
    "pagina" => $pagina,
    "totalPaginas" => $totalPaginas
));

<?php

require_once "../../../controladores/facturas.controlador.php";
require_once "../../../modelos/facturas.modelo.php";
require_once "../../../controladores/clientes.controlador.php";
require_once "../../../modelos/clientes.modelo.php";
require_once('tcpdf_include.php');

if(!isset($_GET["idAnulacion"])){
    die("Evento de invalidacion no especificado");
}

$evento = ControladorFacturas::ctrMostrarAnulaciones("id", $_GET["idAnulacion"], "id");

if(!$evento){
    die("Evento de invalidacion no encontrado");
}

$factura = ControladorFacturas::ctrMostrarFacturas("id", $evento["facturaRelacionada"], "id", "no");
$cliente = $factura ? ControladorClientes::ctrMostrarClientes("id", $factura["id_cliente"], "id") : null;
$empresa = ControladorClientes::ctrMostrarEmpresas("id", "1", "id");

$jsonGuardado = isset($evento["json_guardado"]) ? json_decode($evento["json_guardado"], true) : null;
if(!is_array($jsonGuardado)){
    $jsonGuardado = [];
}

$dteJson = isset($jsonGuardado["dteJson"]) && is_array($jsonGuardado["dteJson"]) ? $jsonGuardado["dteJson"] : [];
$motivo = isset($dteJson["motivo"]) && is_array($dteJson["motivo"]) ? $dteJson["motivo"] : [];
$documento = isset($dteJson["documento"]) && is_array($dteJson["documento"]) ? $dteJson["documento"] : [];

$tipoAnulacion = isset($motivo["tipoAnulacion"]) ? (int) $motivo["tipoAnulacion"] : 2;
$tipoAnulacionTexto = $tipoAnulacion === 1 ? "Dejar sin efecto documento" : "Rescindir operacion";
$codigoReemplaza = ($tipoAnulacion === 1 && isset($documento["codigoGeneracionR"]) && $documento["codigoGeneracionR"] !== null && $documento["codigoGeneracionR"] !== "") ? $documento["codigoGeneracionR"] : "No aplica";

$tiposDte = [
    "01" => "Factura",
    "03" => "Comprobante de credito fiscal",
    "04" => "Nota de remision",
    "05" => "Nota de credito",
    "06" => "Nota de debito",
    "11" => "Factura de exportacion",
    "14" => "Factura de sujeto excluido"
];

$tipoDteTexto = $factura ? (($factura["tipoDte"] ?? "") . " - " . ($tiposDte[$factura["tipoDte"]] ?? "Documento")) : "";

function hInvalidacion($valor){
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

class PDFInvalidacion extends TCPDF {
    public function Header() {
        $this->Ln(10);
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 0, "DOCUMENTO TRIBUTARIO ELECTRONICO", 0, true, 'C');
        $this->Ln(5);
        $this->SetFont('helvetica', '', 13);
        $this->Cell(0, 0, "Evento de invalidacion", 0, true, 'C');

        $image_file = K_PATH_IMAGES.'tcpdf_logo.jpg';
        if(file_exists($image_file)){
            $this->Image($image_file, 10, 5, 35, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
    }
}

$pdf = new PDFInvalidacion(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('QwertySystems');
$pdf->setTitle('Invalidacion '.$evento["codigoGeneracion"]);
$pdf->setSubject('Evento de invalidacion');
$pdf->setKeywords('TCPDF, invalidacion, DTE');
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setFont('dejavusans', '', 9);
$pdf->AddPage();

$html = '
<br><br><br>
<div style="font-family: Arial, sans-serif; font-size: 9px;">
    <table border="0" cellspacing="0" cellpadding="3">
        <tr>
            <td colspan="14" style="text-align:center; background-color:#abebff; border:1px solid black;"><b>DATOS DEL EVENTO</b></td>
        </tr>
        <tr>
            <td colspan="7" style="border:1px solid black;">
                <b>Codigo de generacion del evento:</b><br>'.hInvalidacion($evento["codigoGeneracion"]).'<br>
                <b>Fecha:</b> '.hInvalidacion($evento["fecEmi"]).' '.hInvalidacion($evento["horEmi"]).'<br>
                <b>Sello del evento:</b> '.hInvalidacion($evento["sello"] ?? "").'<br>
                <b>Tipo de invalidacion:</b> '.hInvalidacion($tipoAnulacionTexto).'<br>
                <b>Motivo:</b> '.hInvalidacion($evento["motivoAnulacion"]).'
            </td>
            <td colspan="7" style="border:1px solid black;">
                <b>Nombre de quien realiza el evento:</b><br>'.hInvalidacion($motivo["nombreResponsable"] ?? $empresa["nombre"] ?? "").'<br>
                <b>Tipo de documento del responsable:</b> '.hInvalidacion($motivo["tipDocResponsable"] ?? "36").'<br>
                <b>Documento del responsable:</b> '.hInvalidacion($motivo["numDocResponsable"] ?? $empresa["nit"] ?? "").'
            </td>
        </tr>
        <tr>
            <td colspan="14" style="border:1px solid black;">
                <b>Nombre de quien solicita la invalidacion:</b> '.hInvalidacion($motivo["nombreSolicita"] ?? $cliente["nombre"] ?? "").'<br>
                <b>Tipo de documento:</b> '.hInvalidacion($motivo["tipDocSolicita"] ?? "").'<br>
                <b>Documento:</b> '.hInvalidacion($motivo["numDocSolicita"] ?? "")
            .'</td>
        </tr>
    </table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="3">
        <tr>
            <td colspan="14" style="text-align:center; background-color:#abebff; border:1px solid black;"><b>DOCUMENTO INVALIDADO</b></td>
        </tr>
        <tr style="background-color:#dddcdc;">
            <td colspan="2" style="border:1px solid black; text-align:center;"><b>Tipo DTE</b></td>
            <td colspan="4" style="border:1px solid black; text-align:center;"><b>Codigo de generacion del DTE</b></td>
            <td colspan="3" style="border:1px solid black; text-align:center;"><b>Sello del DTE</b></td>
            <td colspan="3" style="border:1px solid black; text-align:center;"><b>Numero de control del DTE</b></td>
            <td colspan="2" style="border:1px solid black; text-align:center;"><b>Fecha</b></td>
        </tr>
        <tr>
            <td colspan="2" style="border:1px solid black;">'.hInvalidacion($tipoDteTexto).'</td>
            <td colspan="4" style="border:1px solid black;">'.hInvalidacion($factura["codigoGeneracion"] ?? "").'</td>
            <td colspan="3" style="border:1px solid black;">'.hInvalidacion($factura["sello"] ?? "").'</td>
            <td colspan="3" style="border:1px solid black;">'.hInvalidacion($factura["numeroControl"] ?? "").'</td>
            <td colspan="2" style="border:1px solid black;">'.hInvalidacion($factura["fecEmi"] ?? "").'</td>
        </tr>
    </table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="3">
        <tr>
            <td colspan="14" style="border:1px solid black; background-color:#f7f7f7;">
                <b>Codigo de generacion que reemplaza al invalidado:</b> '.hInvalidacion($codigoReemplaza).'
            </td>
        </tr>
    </table>
</div>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->lastPage();
$pdf->Output('Invalidacion '.$evento["codigoGeneracion"].'.pdf', 'I');

?>

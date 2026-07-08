<?php

require_once "../../../controladores/facturas.controlador.php";
require_once "../../../modelos/facturas.modelo.php";

require_once "../../../controladores/clientes.controlador.php";
require_once "../../../modelos/clientes.modelo.php";

require_once "../../../controladores/productos.controlador.php";
require_once "../../../modelos/productos.modelo.php";

require_once "../../../controladores/usuarios.controlador.php";
require_once "../../../modelos/usuarios.modelo.php";

require_once '../../phpqrcode/qrlib.php';


if(isset($_GET["idFactura"]) && isset($_GET["idFactura"])){

    $item = "id";
    $orden = "id";
    $valor = $_GET["idFactura"];
    $optimizacion = "optimizacion";

    // Obtiene los datos de la factura
    $factura = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $item = "id";
    $orden = "id";
    $valor = $factura["id_cliente"];

    // Obtiene los datos de la factura
    $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);

    $item = "id";
    $orden = "id";
    $valor = "1";

    // Obtiene los datos de la factura
    $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

    $ancho = $empresa["ancho"];
    $largo = "200";

    function numeroALetras($numero) {
        $unidad = [
            "cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve",
            "diez", "once", "doce", "trece", "catorce", "quince", "dieciséis", "diecisiete", "dieciocho", "diecinueve"
        ];
        $decena = [
            "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa"
        ];
        $centena = [
            "", "cien", "doscientos", "trescientos", "cuatrocientos", "quinientos", "seiscientos", "setecientos", "ochocientos", "novecientos"
        ];
    
        if ($numero == 0) {
            return "cero";
        }
    
        if ($numero < 20) {
            return $unidad[$numero];
        } elseif ($numero < 100) {
            return $decena[intval($numero / 10)] . ($numero % 10 == 0 ? "" : " y " . $unidad[$numero % 10]);
        } elseif ($numero < 1000) {
            return ($numero == 100 ? "cien" : $centena[intval($numero / 100)] . ($numero % 100 == 0 ? "" : " " . numeroALetras($numero % 100)));
        } elseif ($numero < 1000000) {
            return numeroALetras(intval($numero / 1000)) . " mil" . ($numero % 1000 == 0 ? "" : " " . numeroALetras($numero % 1000));
        } elseif ($numero < 1000000000) {
            return numeroALetras(intval($numero / 1000000)) . " millón" . ($numero % 1000000 == 0 ? "" : " " . numeroALetras($numero % 1000000));
        } else {
            return "Número demasiado grande";
        }
        }
    
        function numeroAmoneda($numero) {
            $partes = explode(".", number_format($numero, 2, ".", ""));
            $parteEntera = intval($partes[0]);
            $parteDecimal = intval($partes[1]);
    
            $texto = numeroALetras($parteEntera) . " dólares";
            if ($parteDecimal > 0) {
                $texto .= " con " . numeroALetras($parteDecimal) . " centavos";
            }
    
            return ucfirst($texto);
        }

    $item = "id";
        $orden = "id";
        $valor = $_GET["idFactura"];
        $optimizacion = "no";

        // Obtiene los datos de la factura
        $factura = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        $modoTexto = "";

        if($factura["modo"] != "Contingencia"){
            $modoTexto = "Transmisión normal";
        } else {
            $modoTexto = "Transmisión en contingencia";
        }

        switch ($factura["tipoDte"]) {
            case "01":
                $tipoFacturaTexto = "Factura";
                break;
            case "03":
                $tipoFacturaTexto = "Comprobante de crédito fiscal";
                break;
            case "04":
                $tipoFacturaTexto = "Nota de remisión";
                break;
            case "05":
                $tipoFacturaTexto = "Nota de crédito";
                break;
            case "06":
                $tipoFacturaTexto = "Nota de débito";
                break;
            case "07":
                $tipoFacturaTexto = "Comprobante de retención";
                break;
            case "08":
                $tipoFacturaTexto = "Comprobante de liquidación";
                break;
            case "09":
                $tipoFacturaTexto = "Documento contable de liquidación";
                break;
            case "11":
                $tipoFacturaTexto = "Factura de exportación";
                break;
            case "14":
                $tipoFacturaTexto = "Factura de sujeto excluido";
                break;
            case "15":
                $tipoFacturaTexto = "Comprobante de donación";
                break;

            default:
                echo "Factura no válida";
                break;
        }

  }

  // URL que deseas codificar en el QR
  $url = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen=" . $factura["codigoGeneracion"];
  $url .= "&fechaEmi=" . $factura["fecEmi"];  

// Nombre del archivo donde se guardará el QR
$archivoQR = 'codigo_qr.png';

// Genera el código QR y guárdalo como imagen
QRcode::png($url, $archivoQR, QR_ECLEVEL_L, 10);

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        
        

        

        
    }
}

// create new PDF document
$pdf = new TCPDF('P', 'mm', array($ancho, $largo), true, 'UTF-8', false);
$pdf->SetAutoPageBreak(true, 10);


// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Rentaly El Salvador');
$pdf->setTitle('Factura '.$factura["codigoGeneracion"].'');
$pdf->setSubject('Reservación Rentaly El Salvador');
$pdf->setKeywords('	TCPDF, PDF, example, test, guide');


// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(5, 5, 5); // Márgenes mínimos para tickets
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->setFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

// Obtener la fecha de ahora
$dia = date('d');
$mes = date('m');
$ano = date('Y');

function fechaEnEspanol($fecha) {
    $meses = array(
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre'
    );

    $timestamp = strtotime($fecha);
    $dia = date('d', $timestamp);
    $mes = $meses[date('n', $timestamp)];
    $anio = date('Y', $timestamp);

    return "$dia de $mes del $anio";
}
if($factura == null){
    $html = 'Factura eliminada';
    $pdf->writeHTML($html, true, false, true, false, '');
    
    //Close and output PDF document
    $pdf->Output($factura["codigoGeneracion"].'.pdf', 'I');

    //============================================================+
    // END OF FILE
    //============================================================+

} else {

    $departamentos = [
        "00" => "Extranjero",
        "01" => "Ahuachapan",
        "02" => "Santa Ana",
        "03" => "Sonsonate",
        "04" => "Chalatenango",
        "05" => "La Libertad",
        "06" => "San Salvador",
        "07" => "Cuscatlán",
        "08" => "La Paz",
        "09" => "Cabañas",
        "10" => "San Vicente",
        "11" => "Usulután",
        "12" => "San Miguel",
        "13" => "Morazán",
        "14" => "La Unión"
    ];
    
    $municipios = [
        "01" => [ // Ahuachapan
            "13" => "Ahuachapan norte",
            "14" => "Ahuachapan centro",
            "15" => "Ahuachapan sur"
        ],
        "02" => [ // Santa Ana
            "14" => "Santa Ana norte",
            "15" => "Santa Ana centro",
            "16" => "Santa Ana este",
            "17" => "Santa Ana oeste"
        ],
        "03" => [ // Sonsonate
            "17" => "Sonsonate norte",
            "18" => "Sonsonate centro",
            "19" => "Sonsonate este",
            "20" => "Sonsonate oeste"
        ],
        "04" => [ // Chalatenango
            "34" => "Chalatenango norte",
            "35" => "Chalatenango centro",
            "36" => "Chalatenango sur"
        ],
        "05" => [ // La Libertad
            "23" => "La Libertad norte",
            "24" => "La Libertad centro",
            "25" => "La Libertad oeste",
            "26" => "La Libertad este",
            "27" => "La Libertad costa",
            "28" => "La Libertad sur"
        ],
        "06" => [ // San Salvador
            "20" => "San Salvador norte",
            "21" => "San Salvador oeste",
            "22" => "San Salvador este",
            "23" => "San Salvador centro",
            "24" => "San Salvador sur"
        ],
        "07" => [ // Cuscatlán
            "17" => "Cuscatlán norte",
            "18" => "Cuscatlán sur"
        ],
        "08" => [ // La Paz
            "23" => "La Paz oeste",
            "24" => "La Paz centro",
            "25" => "La Paz este"
        ],
        "09" => [ // Cabañas
            "10" => "Cabañas oeste",
            "11" => "Cabañas este"
        ],
        "10" => [ // San Vicente
            "14" => "San Vicente norte",
            "15" => "San Vicente sur"
        ],
        "11" => [ // Usulután
            "24" => "Usulután norte",
            "25" => "Usulután este",
            "26" => "Usulután oeste"
        ],
        "12" => [ // San Miguel
            "21" => "San Miguel norte",
            "22" => "San Miguel centro",
            "23" => "San Miguel oeste"
        ],
        "13" => [ // Morazán
            "27" => "Morazán norte",
            "28" => "Morazán sur"
        ],
        "14" => [ // La Unión
            "19" => "La Unión norte",
            "20" => "La Unión sur"
        ]
    ];
    
    $item = null;
    $valor = null;

    $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

    $nombreVendedor = "";
    $nombreFacturador = "";

    foreach ($usuarios as $key => $value){
        if($value["id"] == $factura["id_vendedor"]){
            $nombreVendedor = $value["nombre"];
        }

        if($value["id"] == $factura["id_usuario"]){
            $nombreFacturador = $value["nombre"];
        }
    }
// create some HTML content
$html = '
<div style="text-align: center; font-size: 5px">
    <div class"row">
        <div class="col-xl-12 col-xs-12">
            '.$empresa["nombre"].'<br>
            NRC: '.$empresa["nrc"].'<br>
            NIT: '.$empresa["nit"].'<br><br>
            '.$tipoFacturaTexto.'<br>
            '.$departamentos[$empresa["departamento"]] . ', ' .$municipios[$empresa["departamento"]][$empresa["municipio"]] . ', ' .$empresa["direccion"].'<br><br>
            Codigo de generación<br>'.$factura["codigoGeneracion"].'<br><br>
            Sello de recepción<br>'.$factura["sello"].'<br><br>
            Número de control del DTE<br>'.$factura["numeroControl"].'<br><br>
            Nombre: '.$cliente["nombre"].'<br>
            NIT: '.$cliente["NIT"].'<br>
        </div>
    </div>

';


$condicionTexto = "";
if($factura["condicionOperacion"] == "1"){
    $condicionTexto = "Contado";
}
if($factura["condicionOperacion"] == "2"){
    $condicionTexto = "Crédito";
}
if($factura["condicionOperacion"] == "3"){
    $condicionTexto = "Otro";
}


if($factura["tipoDte"] === "01" && $cliente["tipo_cliente"] === "00"){// Factura, Persona normal y declarante de IVA
    $html .= '<table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
    <tr>
        <td style="text-align:center; background-color: #ffffff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#ffffff';
        if($productoLei){
            $html .= '
            <tr style="background-color: '.$bgColor.'">
                <td style="text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                <td style="text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                <td style="text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                <td style="text-align:center;" colspan="3">$'.number_format(($producto["precioConIva"]), 2, '.', ',').'</td>
                <td style="text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                <td style="text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                <td style="text-align:center;" colspan="3">$'.number_format((($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"]), 2, '.', ',').'</td>                    
            </tr>
            
            ';
            $contador++;
        } else {

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>Venta no sujeta:</b> $0.00<br>
                <b>Venta exenta:</b> $0.00<br>
                <b>Total gravada:</b> $'.number_format(($factura["total"]), 2, '.', ',').'<br>
                <b>Sub-Total:</b> $'.number_format(($factura["total"]), 2, '.', ',').'<br>
                <b>IVA 13%:</b> $0.00<br>
                <b>Retención Renta:</b> $0.00<br>
                <b>Monto total de la operación:</b> $'.number_format(($factura["total"]), 2, '.', ',').'<br>
                <b>'.numeroAmoneda($factura["total"]).'</b>
                <br>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                '.$factura["fecEmi"].' '.$factura["horEmi"].'
            </td>
        </tr>
        <tr>
            <td style="text-align:center;" colspan="14">
                <img src="'.$archivoQR.'" style="width: 100px !important">
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "01"){// Factura, Persona normal y declarante de IVA
    $html .= '<table border="0" cellspacing="0" cellpadding="2 style="font-size: 4px"">
    <tr>

        <td style="text-align:center; background-color: #ffffff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#ffffff';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format(($producto["precioConIva"]), 2, '.', ',').'</td>
                    <td style="text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format((($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"]), 2, '.', ',').'</td>                    
                </tr>
                
        ';
        $contador++;
        } else {

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>Venta no sujeta:</b> $0.00<br>
                <b>Venta exenta:</b> $0.00<br>
                <b>Total gravada:</b> $'.number_format(($factura["total"]), 2, '.', ',').'<br>
                <b>Sub-Total:</b> $'.number_format(($factura["total"]), 2, '.', ',').'<br>
                <b>IVA 13%:</b> $0.00<br>
                <b>Retención Renta:</b> $0.00<br>
                <b>Monto total de la operación:</b> $'.number_format(($factura["total"]), 2, '.', ',').'<br>
                <b>'.numeroAmoneda($factura["total"]).'</b>
                <br>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                '.$factura["fecEmi"].' '.$factura["horEmi"].'
            </td>
        </tr>
        <tr>
            <td style="text-align:center;" colspan="14">
                <img src="'.$archivoQR.'" style="width: 100px !important">
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "02"){// Factura, Empresa con beneficios fiscales
    $html .= '<table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
    <tr>

        <td style="text-align:center; background-color: #ffffff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Venta no sujeta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#ffffff';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]), 2, '.', ',').'</td>                    
                </tr>
                
        ';
        $contador++;
        } else {

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
        
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>Venta no sujeta:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>Venta exenta:</b> $0.00<br>
                <b>Total gravada:</b> $0.00<br>
                <b>Sub-Total:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>IVA 13%:</b> $0.00<br>
                <b>Retención Renta:</b> $0.00<br>
                <b>Monto total de la operación:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>'.numeroAmoneda($factura["totalSinIva"]).'</b>
                <br>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                '.$factura["fecEmi"].' '.$factura["horEmi"].'
            </td>
        </tr>
        <tr>
            <td style="text-align:center;" colspan="14">
                <img src="'.$archivoQR.'" style="width: 100px !important">
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "03"){// Factura, Diplomáticos
    $html .= '<table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
    <tr>

        <td style="text-align:center; background-color: #ffffff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Venta exenta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#ffffff';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]), 2, '.', ',').'</td>                    
                </tr>
                
        ';
        $contador++;
        } else {

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>Venta no sujeta:</b> $0.00<br>
                <b>Venta exenta:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>Total gravada:</b> $0.00<br>
                <b>Sub-Total:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>IVA 13%:</b> $0.00<br>
                <b>Retención Renta:</b> $0.00<br>
                <b>Monto total de la operación:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>'.numeroAmoneda($factura["totalSinIva"]).'</b>
                <br>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                '.$factura["fecEmi"].' '.$factura["horEmi"].'
            </td>
        </tr>
        <tr>
            <td style="text-align:center;" colspan="14">
                <img src="'.$archivoQR.'" style="width: 100px !important">
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "01"){// CCF, Declarantes de IVA
    $html .= '<table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
    <tr>

        <td style="text-align:center; background-color: #ffffff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#ffffff';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                    
                </tr>
                
        ';
        $contador++;
        } else {

        }
        
    }

    $retencionGranContribuyente = 0.0;
    if($factura["gran_contribuyente"] == "Si"){
        $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">

        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>Venta no sujeta:</b> $0.00<br>
                <b>Venta exenta:</b> $0.00<br>
                <b>Total gravada:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>Sub-Total:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>IVA 13%:</b> $'.number_format((($factura["total"] - $factura["totalSinIva"])), 2, '.', ',').'<br>
                <b>IVA 1%:</b> $'.$retencionGranContribuyente.'<br>
                <b>Retención Renta:</b> $0.00<br>
                <b>Monto total de la operación:</b> $'.number_format(($factura["total"] - $retencionGranContribuyente), 2, '.', ',').'<br>
                <b>'.numeroAmoneda($factura["total"] - $retencionGranContribuyente).'</b>
                <br>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                '.$factura["fecEmi"].' '.$factura["horEmi"].'
            </td>
        </tr>
        <tr>
            <td style="text-align:center;" colspan="14">
                <img src="'.$archivoQR.'" style="width: 100px !important">
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "02"){// CCF, Empresa con beneficios fiscales
    $html .= '<table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
    <tr>

        <td style="text-align:center; background-color: #ffffff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Venta no sujeta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#ffffff';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                    
                </tr>
                
        ';
        $contador++;
        } else {

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>Venta no sujeta:</b> $'.$factura["totalSinIva"].'<br>
                <b>Venta exenta:</b> $0.00<br>
                <b>Total gravada:</b> $0.00<br>
                <b>Sub-Total:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>IVA 13%:</b> $0.00<br>
                <b>Retención Renta:</b> $0.00<br>
                <b>Monto total de la operación:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>'.numeroAmoneda($factura["totalSinIva"]).'</b>
                <br>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                '.$factura["fecEmi"].' '.$factura["horEmi"].'
            </td>
        </tr>
        <tr>
            <td style="text-align:center;" colspan="14">
                <img src="'.$archivoQR.'" style="width: 100px !important">
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "03"){// CCF, Diplomáticos
    $html .= '<table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
    <tr>

        <td style="text-align:center; background-color: #ffffff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Venta exenta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#ffffff';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                </tr>
                
        ';
        $contador++;
        } else {

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>Venta no sujeta:</b> $0.00<br>
                <b>Venta exenta:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>Total gravada:</b> $0.00<br>
                <b>Sub-Total:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>IVA 13%:</b> $0.00<br>
                <b>Retención Renta:</b> $0.00<br>
                <b>Monto total de la operación:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>'.numeroAmoneda($factura["totalSinIva"]).'</b>
                <br>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                '.$factura["fecEmi"].' '.$factura["horEmi"].'
            </td>
        </tr>
        <tr>
            <td style="text-align:center;" colspan="14">
                <img src="'.$archivoQR.'" style="width: 100px !important">
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "11" && ($cliente["tipo_cliente"] == "01" || $cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){// Exportación, Declarantes de IVA
    $html .= '<table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
    <tr>

        <td style="text-align:center; background-color: #ffffff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#ffffff';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                </tr>
                
        ';
        $contador++;
        } else {

        }
        
    }
    $totalOpera = $factura["flete"] + $factura["seguro"] + $factura["totalSinIva"];
    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>Total gravada:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>Flete:</b> $'.number_format(($factura["flete"]), 2, '.', ',').'<br>
                <b>Seguro:</b> $'.number_format(($factura["seguro"]), 2, '.', ',').'<br>
                <b>Monto total de la operación:</b> $'.number_format(($totalOpera), 2, '.', ',').'<br>
                <b>'.numeroAmoneda($totalOpera).'</b>
                <br>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                '.$factura["fecEmi"].' '.$factura["horEmi"].'
            </td>
        </tr>
        <tr>
            <td style="text-align:center;" colspan="14">
                <img src="'.$archivoQR.'" style="width: 100px !important">
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "14" && $cliente["tipo_cliente"] == "00"){// Sujeto no excluido, persona normal
    $html .= '<table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
    <tr>

        <td style="text-align:center; background-color: #ffffff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Porcentaje</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #ffffff" colspan="3">
            <br><br><b>Venta efecto renta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#ffffff';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                </tr>
                
        ';
        $contador++;
        } else {

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2" style="font-size: 4px">
        <tr>
        <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>Sumas:</b> $'.number_format(($factura["totalSinIva"]), 2, '.', ',').'<br>
                <b>Renta retenida:</b> $'.number_format((($factura["totalSinIva"] * 0.10)), 2, '.', ',').'<br>
                <b>Total:</b> $'.number_format(($factura["totalSinIva"]-($factura["totalSinIva"] * 0.10)), 2, '.', ',').'<br>
                <b>'.numeroAmoneda($factura["totalSinIva"]-($factura["totalSinIva"]*0.10)).'</b>
                <br>
            </td>
            
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                '.$factura["fecEmi"].' '.$factura["horEmi"].'
            </td>
        </tr>
        <tr>
            <td style="text-align:center;" colspan="14">
                <img src="'.$archivoQR.'" style="width: 100px !important">
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "05" && $cliente["tipo_cliente"] == "01"){// Nota de crédito, Declarantes de IVA
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        if($producto["descuento"] != "0"){
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

            $des = $producto["descuento"];
            $desR = floatval(number_format($des, 2, '.', ''));

            $totalProD = (($producto["descuento"] * $producto["cantidad"]));
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
            // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["descuento"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["descuento"] * $producto["cantidad"])), 2, '.', ',').'</td>                    
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
    
            }
            
        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $'.number_format(($totalGravado * 0.13), 2, '.', ',').'<br>
                <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado + ($totalGravado * 0.13)), 2, '.', ',').'<br>
                </p>
                <b>'.numeroAmoneda($totalGravado + ($totalGravado * 0.13)).'</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "05" && $cliente["tipo_cliente"] == "02"){// Nota de crédito, Beneficios fiscales
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Venta no sujeta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        if($producto["descuento"] != "0"){
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
    
            $des = $producto["descuento"];
            $desR = floatval(number_format($des, 2, '.', ''));
    
            $totalProD = (($producto["descuento"] * $producto["cantidad"]));
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
             // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["descuento"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["descuento"] * $producto["cantidad"]), 2, '.', ',').'</td>                    
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
    
            }
            
        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $0.00<br>
                <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                </p>
                <b>'.numeroAmoneda($totalGravado).'</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "05" && $cliente["tipo_cliente"] == "03"){// Nota de crédito, Diplomático
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Venta exenta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        if($producto["descuento"] != "0"){
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

            $des = $producto["descuento"];
            $desR = floatval(number_format($des, 2, '.', ''));

            $totalProD = (($producto["descuento"] * $producto["cantidad"]));
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
            // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["descuento"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["descuento"] * $producto["cantidad"])), 2, '.', ',').'</td>
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
    
            }
            
        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $0.00<br>
                <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                </p>
                <b>'.numeroAmoneda($totalGravado).'</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "06" && $cliente["tipo_cliente"] == "01"){// Nota de dédito, Declarantes de IVA
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Aumento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        if($producto["descuento"] != "0"){
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
    
            $des = $producto["descuento"];
            $desR = floatval(number_format($des, 2, '.', ''));
    
            $totalProD = ($producto["descuento"] * $producto["cantidad"]);
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
             // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["descuento"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["descuento"] * $producto["cantidad"])), 2, '.', ',').'</td>
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
    
            }
            
        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $'.number_format(($totalGravado * 0.13), 2, '.', ',').'<br>
                <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado + ($totalGravado * 0.13)), 2, '.', ',').'<br>
                </p>
                <b>'.numeroAmoneda($totalGravado + ($totalGravado * 0.13)).'</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "06" && $cliente["tipo_cliente"] == "02"){// Nota de dédito, beneficios fiscales
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Aumento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Venta no sujeta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        if($producto["descuento"] != "0"){
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

            $des = $producto["descuento"];
            $desR = floatval(number_format($des, 2, '.', ''));

            $totalProD = ($producto["descuento"] * $producto["cantidad"]);
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
            // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["descuento"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["descuento"] * $producto["cantidad"])), 2, '.', ',').'</td>
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
    
            }
            
        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> '.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $0.0<br>
                <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                </p>
                <b>'.numeroAmoneda($totalGravado).'</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "06" && $cliente["tipo_cliente"] == "03"){// Nota de dédito, Diplomáticos
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Aumento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Venta exenta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        if($producto["descuento"] != "0"){
            $item = "id";
            $valor = $producto["idProducto"];
        
            $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
    
            $des = $producto["descuento"];
            $desR = floatval(number_format($des, 2, '.', ''));
    
            $totalProD = ($producto["descuento"] * $producto["cantidad"]);
            $totalProF = floatval(number_format($totalProD, 2, '.', ''));
             // Alternar color de fondo
            $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
            if($productoLei){
                $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["descuento"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["descuento"] * $producto["cantidad"])), 2, '.', ',').'</td>
                    </tr>
                    
            ';
            $totalGravado += $totalProF;
            $contador++;
            } else {
    
            }
            
        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta no sujeta:</b> '.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $0.0<br>
                <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                </p>
                <b>'.numeroAmoneda($totalGravado).'</b>
                <br>
            </td>
        </tr>
    </table>';
}
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "01" && $facturaOriginal["tipoDte"] == "03"){// Nota de remisión, ccf contribuyente
    

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

        
        $totalProD = (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]);
        $totalProF = floatval(number_format($totalProD, 2, '.', ''));
         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                </tr>
                
        ';
        $totalGravado += $totalProF;
        $contador++;
        } else {

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta gravada:</b> '.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $'.number_format(($totalGravado*0.13), 2, '.', ',').'<br>
                <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado+($totalGravado*0.13)), 2, '.', ',').'<br>
                </p>
                <b>'.numeroAmoneda($totalGravado + ($totalGravado*0.13)).'</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "02" && $facturaOriginal["tipoDte"] == "03"){// Nota de remisión, ccf beneficios
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Venta no sujeta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

        
        $totalProD = (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]);
        $totalProF = floatval(number_format($totalProD, 2, '.', ''));
         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                </tr>
                
        ';
        $totalGravado += $totalProF;
        $contador++;
        } else {

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta gravada:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $0.0<br>
                <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                </p>
                <b>'.numeroAmoneda($totalGravado).'</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "03" && $facturaOriginal["tipoDte"] == "03"){// Nota de remisión, ccf diplomas
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Venta exenta</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

        
        $totalProD = (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]);
        $totalProF = floatval(number_format($totalProD, 2, '.', ''));
         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                </tr>
                
        ';
        $totalGravado += $totalProF;
        $contador++;
        } else {

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta gravada:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Iva 13%:</b> $0.0<br>
                <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                </p>
                <b>'.numeroAmoneda($totalGravado).'</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "01" && $facturaOriginal["tipoDte"] == "11"){// Nota de remisión, export contribuyente
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

        
        $totalProD = (($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"]);
        $totalProF = floatval(number_format($totalProD, 2, '.', ''));
         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.$producto["descuento"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] - $producto["descuento"] ) * $producto["cantidad"]), 2, '.', ',').'</td>                                        
                </tr>
                
        ';
        $totalGravado += $totalProF;
        $contador++;
        } else {

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta gravada:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                </p>
                <b>'.numeroAmoneda($totalGravado).'</b>
                <br>
            </td>
        </tr>
    </table>';
}

if($factura["tipoDte"] == "04" && ($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03") && $facturaOriginal["tipoDte"] == "11"){// Nota de remisión, export beneficios diplomas
    $item = "id";
    $orden = "id";
    $valor = $factura["idFacturaRelacionada"];
    $optimizacion = "no";

    $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    $html .= '<br><br> Código de generación de factura que afecta: '.$facturaOriginal["codigoGeneracion"].'<br><br><table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Unidad</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="7">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Porcentaje descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Descuento</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Ventas</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    $totalGravado = 0.0;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

        
        $totalProD = ($producto["precioSinImpuestos"] * $producto["cantidad"]);
        $totalProF = floatval(number_format($totalProD, 2, '.', ''));
         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei){
            $html .= '
                <tr style="background-color: '.$bgColor.'">
                    <td style="border: 1px solid black; text-align:center;" colspan="2">'.$contador.'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["cantidad"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="4">Unidad</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="7">'.$productoLei["nombre"].'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioSinImpuestos"]), 2, '.', ',').'</td>
                    <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioSinImpuestos"] * $producto["cantidad"])), 2, '.', ',').'</td>
                </tr>
                
        ';
        $totalGravado += $totalProF;
        $contador++;
        } else {

        }
        
    }

    $html .= '</table>
    <br><br>
    <table border="0" cellspacing="0" cellpadding="2">
        <tr>

            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>DETALLES</b>
            </td>
            <td style="text-align:left; background-color: #abebff; border: 1px solid black" colspan="7">
                <b>SUMA TOTAL DE OPERACIONES</b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;" colspan="7">
                <br><br>
                <b>Condición de la operación:</b> '.$condicionTexto.'<br>
            </td>
            <td style="text-align:left" colspan="7">
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta gravada:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venta exenta:</b> $0.00<br>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total gravada:</b> $0.0<br>
                <p style="background-color: #abebff; line-height: 5px; text-align: left;">
                    <b><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar:</b> $'.number_format(($totalGravado), 2, '.', ',').'<br>
                </p>
                <b>'.numeroAmoneda($totalGravado).'</b>
                <br>
            </td>
        </tr>
    </table>';
}
    
$html .= '</div>';



$pdf->writeHTML($html, true, false, true, false, '');


// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Factura '.$factura["codigoGeneracion"].'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
}

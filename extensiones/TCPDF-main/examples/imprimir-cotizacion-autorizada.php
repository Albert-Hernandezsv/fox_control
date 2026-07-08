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


if(isset($_GET["idCotizacionAutorizada"]) && isset($_GET["idCotizacionAutorizada"])){

    $item = "id";
    $orden = "id";
    $valor = $_GET["idCotizacionAutorizada"];
    $optimizacion = "no";

    // Obtiene los datos de la factura
    $cotizacion= ControladorFacturas::ctrMostrarCotizacionesAutorizadas($item, $valor, $orden, $optimizacion);

    $item = "id";
    $orden = "id";
    $valor = $cotizacion["id_cliente"];

    // Obtiene los datos de la factura
    $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);

    $item = "id";
    $orden = "id";
    $valor = "1";

    // Obtiene los datos de la factura
    $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);


  }




// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        
        $item = "id";
        $orden = "id";
        $valor = $_GET["idCotizacionAutorizada"];
        $optimizacion = "no";

        // Obtiene los datos de la factura
        $cotizacion= ControladorFacturas::ctrMostrarCotizacionesAutorizadas($item, $valor, $orden, $optimizacion);

        $item = "id";
        $orden = "id";
        $valor = "1";

        // Obtiene los datos de la factura
        $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

        $this->Ln(5); // Agrega un espacio vertical de 10 unidades (puedes ajustar el valor)
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(250, 10, $empresa["nombre"], 0, 1, 'C', 0, ' ', 1, false, 'M', 'M');
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(250, 0, "COTIZACIÓN AUTORIZADA", 0, 1, 'C', 0, ' ', 1, false, 'M', 'M');

        $this->Ln(5); // Agrega un espacio vertical de 10 unidades (puedes ajustar el valor)
        $this->SetFont('helvetica', '', 8);
        $this->Cell(220, 20, "", 0, true, 'C', 0, ' ', 1, false, 'B', 'M');

        
        // Logo
        $image_file = K_PATH_IMAGES.'tcpdf_logo.jpg';
        $this->Image($image_file, 10, 5, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAutoPageBreak(true, 10);


// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Rentaly El Salvador');
$pdf->setTitle('Factura '. $cotizacion["codigo"].'');
$pdf->setSubject('Reservación Rentaly El Salvador');
$pdf->setKeywords('	TCPDF, PDF, example, test, guide');


// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);



// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
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
if ($cotizacion == null){
    $html = 'Factura eliminada';
    $pdf->writeHTML($html, true, false, true, false, '');
    
    //Close and output PDF document
    $pdf->Output($cotizacion["codigoGeneracion"].'.pdf', 'I');

    //============================================================+
    // END OF FILE
    //============================================================+

} else {

    $item = null;
    $valor = null;

    $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

    $nombreVendedor = "";
    $nombreFacturador = "";
// create some HTML content
$html = '<div style="font-family: Arial, sans-serif; font-size: 8px;">
<hr>
<table border="0" cellspacing="0" cellpadding="2">
    <tr>
        
        <td style="text-align:left;" colspan="7">
            <p style="font-size: 11px"><b>Cotización: </b>'.$cotizacion["codigo"].'</p><br>
        </td>
        <td style="text-align:left; border-left: 1px solid black; height: 60px;" colspan="7">
            <br><br>
            <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sistema de facturación:</b> Fox Control<br>
            <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Estado:</b> '.$cotizacion["estado"].'<br>
            <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha y hora:</b> '.$cotizacion["fecEmi"].'
        </td>
    </tr>
</table>

<table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #dddcdc" colspan="7">
            <b>EMISOR</b>
        </td>
        <td style="text-align:center; background-color: #dddcdc" colspan="7">
            <b>RECEPTOR</b>
        </td>
    </tr>
    <tr>
        <td style="text-align:left;" colspan="7">

            <b>Nombre o razón social:</b> '.$empresa["nombre"].'<br>
            <b>NIT:</b> '.$empresa["nit"].'<br>
            <b>NRC:</b> '.$empresa["nrc"].'<br>
            <b>Actividad Económica:</b> '.$empresa["desActividad"].'<br>
            <b>Número de teléfono:</b> '.$empresa["telefono"].'<br>
            <b>Correo Electrónico:</b> '.$empresa["correo"].'<br>
        </td>
        <td style="text-align:left" colspan="7">

            <b>Nombre o razón social:</b> '.$cliente["nombre"].'<br>
            <b>NIT:</b> '.$cliente["NIT"].'<br>
            <b>NRC:</b> '.$cliente["NRC"].'<br>
            <b>Actividad Económica:</b> '.$cliente["descActividad"].'<br>
            <b>Dirección:</b> '.$cliente["direccion"].'<br>
            <b>Número de teléfono:</b> '.$cliente["telefono"].'<br>
            <b>Correo Electrónico:</b> '.$cliente["correo"].'<br>
        </td>
        
    </tr>
    
</table>
<hr>';


$unidades = [
    "59" => "Unidad",
    "57" => "Ciento",
    "58" => "Docena",
    "1"  => "Metro",
    "2"  => "Yarda",
    "6"  => "Milímetro",
    "9"  => "Kilómetro cuadrado",
    "10" => "Hectárea",
    "13" => "Metro cuadrado",
    "15" => "Vara cuadrada",
    "18" => "Metro cúbico",
    "20" => "Barril",
    "22" => "Galón",
    "23" => "Litro",
    "24" => "Botella",
    "26" => "Mililitro",
    "30" => "Tonelada",
    "32" => "Quintal",
    "33" => "Arroba",
    "34" => "KG",
    "36" => "Libra",
    "37" => "Onza troy",
    "38" => "Onza",
    "39" => "Gramo",
    "40" => "Miligramo",
    "42" => "Megawatt",
    "43" => "Kilowatt",
    "44" => "Watt",
    "45" => "Megavoltio-amperio",
    "46" => "Kilovoltio-amperio",
    "47" => "Voltio-amperio",
    "49" => "Gigawatt-hora",
    "50" => "Megawatt-hora",
    "51" => "Kilowatt-hora",
    "52" => "Watt-hora",
    "53" => "Kilovoltio",
    "54" => "Voltio",
    "55" => "Millar",
    "56" => "Medio millar",
    "99" => "Otra"
];

    $html .= '<table border="0" cellspacing="0" cellpadding="2">
    <tr>

        <td style="text-align:center; background-color: #abebff" colspan="1">
            <br><br><b>N°</b><br>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Código</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>Origen</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Peso</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Marca</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="4">
            <br><br><b>Modelo</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="11">
            <br><br><b>Descripción</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="2">
            <br><br><b>Cant.</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Precio unitario</b>
        </td>
        <td style="text-align:center; background-color: #abebff" colspan="3">
            <br><br><b>Venta gravada</b>
        </td>
    </tr>';

    // Decodificar los productos de la factura
    $productos = json_decode($cotizacion["productos"], true); // true para obtener un array asociativo
    $contador = 1;
    // Recorrer cada producto y mapear los datos
    foreach ($productos as $producto) {
        $item = "id";
        $valor = $producto["idProducto"];
    
        $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

         // Alternar color de fondo
        $bgColor = ($contador % 2 == 0) ? '#ffffff' : '#dddcdc';
        if($productoLei) {
            $html .= '
                    <tr style="background-color: '.$bgColor.'">
                        <td style="border: 1px solid black; text-align:center;" colspan="1">'.$contador.'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$producto["codigo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$producto["origen"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">'.$producto["peso"].' '.$unidades[$productoLei["unidadMedida"]].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$productoLei["marca"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="4">'.$productoLei["modelo"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="11">'.$productoLei["nombre"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="2">'.$producto["cantidad"].'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format(($producto["precioConIva"]), 2, '.', ',').'</td>
                        <td style="border: 1px solid black; text-align:center;" colspan="3">$'.number_format((($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"]), 2, '.', ',').'</td>                    
                    </tr>
                    
            ';
            $contador++;
        } else {
            $html .= '<tr style="background-color: '.$bgColor.'">
                            <td style="border: 1px solid black; text-align:center;" colspan="100">Producto eliminado</td>
                     </tr>   ';

        }
    }

    $html .= '</table>';



$pdf->writeHTML($html, true, false, true, false, '');


// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Factura '.$cotizacion["codigo"].'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
}

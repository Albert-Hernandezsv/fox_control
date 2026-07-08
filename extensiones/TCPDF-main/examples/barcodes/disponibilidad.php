<?php

require_once "../../../controladores/carros.controlador.php";
require_once "../../../modelos/carros.modelo.php";

require_once "../../../controladores/clientes.controlador.php";
require_once "../../../modelos/clientes.modelo.php";

require_once "../../../controladores/contratos.controlador.php";
require_once "../../../modelos/contratos.modelo.php";

require_once "../../../controladores/coberturas.controlador.php";
require_once "../../../modelos/coberturas.modelo.php";

require_once "../../../controladores/paquetes.controlador.php";
require_once "../../../modelos/paquetes.modelo.php";

if(isset($_GET["placa"])){

		$item = "numero_placa";
	  	$valor = $_GET["placa"];
	  	$orden = "id";

		$carro = ControladorCarros::ctrMostrarCarros($item, $valor, $orden);

  }

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Set font
        $this->SetFont('helvetica', '', 8);
        // Title
        $this->Cell(28, 0, "+503-2362-7388 Oficina", 0, true, 'C', 0, ' ', 1, false, 'M', 'M');
        $this->Cell(40, 0, " ", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        $this->Cell(38, 0, "+503-7308-4876 Reservaciones", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        $this->Cell(40, 0, " ", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        $this->Cell(38, 0, "+503-7420-3455 Emergencias", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        $this->Cell(40, 0, " ", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        $this->Cell(34, 0, "www.rentalyelsalvador.com", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        $this->Cell(40, 0, " ", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        $this->Cell(34, 0, "info@rentalyelsalvador.com", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        // Logo
        $image_file = K_PATH_IMAGES.'tcpdf_logo.jpg';
        $this->Image($image_file, 130, 10, 75, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAutoPageBreak(true, 10);


// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Rentaly El Salvador');
$pdf->setTitle('Disponibilidad de automovil');
$pdf->setSubject('Disponibilidad Rentaly El Salvador');
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
if($carro == null){
    $html = 'Carro eliminado';
    $pdf->writeHTML($html, true, false, true, false, '');
    
    //Close and output PDF document
    $pdf->Output('example_006.pdf', 'I');

    //============================================================+
    // END OF FILE
    //============================================================+

} else {

// create some HTML content
$html = '<br><br><div style="font-family: Open Sans, sans-serif">
<div style="font-size:20px">
Automóvil '.$carro["marca"].' '.$carro["modelo"].'  '.$carro["ano"].'
</div>
<br><br>

<div style="font-size:12px; font-family: Open Sans, sans-serif">
Información general
<table border="0" cellspacing="0" cellpadding="4">
<br><br>
    <tr>
        <th>
            <img src="../../../vistas/img/plantilla/rayo.png" width="15px"> <b>Velocidades:</b> '.$carro["velocidades"].'
        </th>
        <th>
            <img src="../../../vistas/img/plantilla/carro-icono.png" width="15px"> <b>Marca:</b> '.$carro["marca"].'
        </th>
        <th>
            <img src="../../../vistas/img/plantilla/carro-icono.png" width="15px"> <b>Modelo:</b> '.$carro["modelo"].'
        </th>
        <th>
            <img src="../../../vistas/img/plantilla/calendario.jpg" width="15px"> <b>Año:</b> '.$carro["ano"].'
        </th>
    </tr>
    <tr>
        <th>
            <img src="../../../vistas/img/plantilla/tuercas.png" width="15px"> <b>Peso:</b> '.$carro["peso"].' lb
        </th>
        <th>
            <img src="../../../vistas/img/plantilla/tuercas.png" width="15px"> <b>Caja:</b> '.$carro["caja"].'
        </th>
        <th>
            <img src="../../../vistas/img/plantilla/carro-icono.png" width="15px"> <b>Especial:</b> '.$carro["especial"].'
        </th>
        <th>
            <img src="../../../vistas/img/plantilla/admiracion.png" width="15px"> <b>Puertas:</b> '.$carro["puertas"].'
        </th>
    </tr>
    <tr>
        <th>
            <img src="../../../vistas/img/plantilla/admiracion.png" width="15px"> <b>Asientos:</b> '.$carro["asientos"].'
        </th>
        <th>
            <img src="../../../vistas/img/plantilla/rayo.png" width="15px"> <b>Vel max:</b> '.$carro["vel_max"].' km/h
        </th>
    </tr>
</table>
</div>
</div>
<br>
Fotos ilustrativas:asdasdasdas
<br><br>
<table border="1" cellspacing="0" cellpadding="4">
    <tr>
        <th>
            <img src="../../../'.$carro["imagen1"].'" width="150px">
        </th>
        <th>
            
        </th>
        <th>
            <img src="../../../'.$carro["imagen2"].'" width="150px">
        </th>
    </tr>
    <tr>
        <th>
            
        </th>
        <th>
            <img src="../../../'.$carro["imagen3"].'" width="150px">
        </th>
        <th>
            
        </th>
    </tr>
    <tr>
        <th>
            <img src="../../../'.$carro["imagen4"].'" width="150px">
        </th>
        <th>
            
        </th>
        <th>
            <img src="../../../'.$carro["imagen5"].'" width="150px">
        </th>
    </tr>
</table>



</div>
';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Disponibilidad de automovil');

//============================================================+
// END OF FILE
//============================================================+
}
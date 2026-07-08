<?php
require_once "../../../controladores/productos.controlador.php";
require_once "../../../modelos/productos.modelo.php";

// Verificar si los parámetros existen y asignarlos a variables
$idProducto = $_GET['idProducto'];

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        $this->Ln(15); // Agrega un espacio vertical de 10 unidades (puedes ajustar el valor)
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(50, 0, "", 0, true, 'C', 0, ' ', 1, false, 'M', 'M');
        $this->Ln(15); // Agrega un espacio vertical de 15 unidades
        $this->SetFont('helvetica', 'B', 14);

        // Inserta el código QR en el PDF
        $this->Image("", 50, 10, 30, 30, 'PNG', '', 'C', false, 300, '', false, false, 0, false, false, false);
        $this->Ln(5); // Agrega un espacio vertical de 10 unidades (puedes ajustar el valor)
        $this->SetFont('helvetica', '', 14);
        $this->Cell(275, 0, "", 0, true, 'C', 0, ' ', 1, false, 'B', 'M');
        $this->Cell(275, 20, "", 0, true, 'C', 0, ' ', 1, false, 'B', 'M');
        $this->Image("", 10, 5, 35, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAutoPageBreak(true, 10);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Fox Control');
$pdf->setTitle('Codigo de barras');
$pdf->setSubject('Codigo barras Fox Control');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->setPrintFooter(false);


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
$pdf->AddPage('L');

include '../../php-barcode-master/Barcode.php';
$item = "id";
$valor = $idProducto;

$producto = ControladorProductos::ctrMostrarProductos($item, $valor);

$codigo = $producto["codigo"]; // Código que deseas convertir

$barcodeFilePath = '../../php-barcode-master/barcode.png';
barcode($barcodeFilePath, $codigo, '50', 'horizontal', 'code128', true, '2');

$pageWidth = $pdf->getPageWidth();

// create some HTML content
// Insertar la imagen centrada y ajustada al ancho de la página
$pdf->Image("../../php-barcode-master/barcode.png", 0, 50, $pageWidth, 0, 'PNG', '', 'C');

// Escribe el HTML en el PDF


// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

// Close and output PDF document
$pdf->Output('codigoBarras.pdf', 'I');
?>

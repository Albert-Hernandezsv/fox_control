<?php

require_once "../../../controladores/ordenes.controlador.php";
require_once "../../../modelos/ordenes.modelo.php";

require_once "../../../controladores/clientes.controlador.php";
require_once "../../../modelos/clientes.modelo.php";

require_once "../../../controladores/productos.controlador.php";
require_once "../../../modelos/productos.modelo.php";

require_once "../../../controladores/usuarios.controlador.php";
require_once "../../../modelos/usuarios.modelo.php";

require_once '../../phpqrcode/qrlib.php';


if(isset($_GET["idOrden"]) && isset($_GET["idOrden"])){

    $item = "id";
    $orden = "id";
    $valor = $_GET["idOrden"];

    // Obtiene los datos de la factura
    $ordens = ControladorOrdenes::ctrMostrarOrdenes($item, $valor, $orden);

    $item = "id";
    $orden = "id";
    $valor = $ordens["id_proveedor"];

    // Obtiene los datos de la factura
    $proveedor = ControladorClientes::ctrMostrarProveedores($item, $valor, $orden);

    $item = "id";
    $orden = "id";
    $valor = "1";

    // Obtiene los datos de la factura
    $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);
          
  }

  function numeroALetras($numero) {
    $unidad = [
        "", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve",
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

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
   

    public function Header() {
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
        
         

        $item = "id";
        $orden = "id";
        $valor = $_GET["idOrden"];
    
        // Obtiene los datos de la factura
        $ordens = ControladorOrdenes::ctrMostrarOrdenes($item, $valor, $orden);
    
        $item = "id";
        $orden = "id";
        $valor = "1";
    
        // Obtiene los datos de la empresa
        $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

        $item = "id";
        $orden = "id";
        $valor = $ordens["id_proveedor"];

        // Obtiene los datos de la factura
        $proveedor = ControladorClientes::ctrMostrarProveedores($item, $valor, $orden);
    
        // Espacio vertical
        $this->Ln(15); 
    
        // Título de la orden de compra
        $this->SetFont('helvetica', 'B', 18);
        $this->Cell(0, 10, "Orden de compra - ".$ordens["id"], 0, 1, 'C'); 
    
        $this->Ln(15); // Agrega un espacio vertical de 15 unidades
        $this->SetFont('helvetica', 'B', 14);
    
        $this->Ln(5); // Agrega un espacio vertical de 5 unidades
        $this->SetFont('helvetica', '', 10);  // Cambié a tamaño 12
    
        // Usamos MultiCell con ancho automático para ajustarse al contenido y alinearlo a la izquierda
        // Alineación a la izquierda con 'L' y ajustamos el ancho a 190 para evitar que se extienda demasiado.

        // Posición inicial en la izquierda
        $x_left = 10;   // Margen izquierdo
        $x_right = 110; // Margen derecho para la segunda columna
        $y = $this->GetY(); // Obtiene la posición actual en Y para mantener alineado

        // Columna Izquierda
        $this->SetXY($x_left, $y);
        $this->Cell(90, 5, 'Fecha: ' . $ordens["fecha"], 0, 0, 'L');

        $this->SetXY($x_left, $this->GetY() + 5);
        $this->Cell(90, 5, 'Empresa: ' . $empresa["nombre"], 0, 0, 'L');

        $this->SetXY($x_left, $this->GetY() + 5);
        $this->Cell(90, 5, 'NIT: ' . $empresa["nit"], 0, 0, 'L');

        $this->SetXY($x_left, $this->GetY() + 5);
        $this->Cell(90, 5, 'NRC: ' . $empresa["nrc"], 0, 0, 'L');
        
        $this->SetXY($x_left, $this->GetY() + 5);
        $this->Cell(90, 5, 'Teléfono: ' . $empresa["telefono"], 0, 0, 'L');

        $this->SetXY($x_left, $this->GetY() + 5);
        $this->Cell(90, 5, 'Correo: ' . $empresa["correo"], 0, 0, 'L');

        $this->SetXY($x_left, $this->GetY() + 5);
        $this->Cell(90, 5, 'Dirección: ' . $departamentos[$empresa["departamento"]] . ', ' .$municipios[$empresa["departamento"]][$empresa["municipio"]] . ', ' .$empresa["direccion"], 0, 0, 'L');

        // Columna Derecha
        $this->SetXY($x_right, $y); 
        $this->Cell(90, 5, 'Proveedor: ' . $proveedor["nombre"], 0, 0, 'L');

        $this->SetXY($x_right, $this->GetY() + 5);
        $this->Cell(90, 5, 'Nit: ' . $proveedor["nit"], 0, 0, 'L');

        $this->SetXY($x_right, $this->GetY() + 5);
        $this->Cell(90, 5, 'Teléfono: ' . $proveedor["telefono"], 0, 0, 'L');

        $this->SetXY($x_right, $this->GetY() + 5);
        $this->Cell(90, 5, 'Correo: ' . $proveedor["correo"], 0, 0, 'L');

        $this->SetXY($x_right, $this->GetY() + 5);
        $this->Cell(90, 5, 'Condición pago: ' . $proveedor["condicion_pago"], 0, 0, 'L');

        $this->SetXY($x_right, $this->GetY() + 5);
        $this->Cell(90, 5, 'Dirección: ' . $proveedor["direccion"], 0, 0, 'L');
    
        // Logo
        $image_file = K_PATH_IMAGES.'tcpdf_logo.jpg';
        $this->Image($image_file, 10, 5, 35, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
    
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAutoPageBreak(true, 10);


// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor($empresa["nombre"]);
$pdf->setTitle('Orden de compra '.$ordens["id"].'');


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
if($ordens == null){
    $html = 'Orden eliminada';
    $pdf->writeHTML($html, true, false, true, false, '');

} else {


    $html = '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><div><hr><p>Sirvanse por este medio suministrarnos los siguientes artículos</p>
    
    <table border="1" cellspacing="0" cellpadding="2" style="text-align: center">
        <tr>
            <td>#</td>
            <td>Producto</td>
            <td>Cantidad</td>
            <td>Precio</td>
            <td>Total</td>
        </tr>';
        $productos = json_decode($ordens["productos"], true);  // 'true' convierte el JSON en un array asociativo
        $contador = 1;
        $subTotalGeneral = 0.0;
        if (!empty($productos)) {
            foreach ($productos as $producto) {
                if($producto["idProductoViejo"] == ""){
                    $nombre = !empty($producto['nombreProductoNuevo']) ? $producto['nombreProductoNuevo'] : 'N/A';
                    $cantidad = !empty($producto['cantidadProductoNuevo']) ? $producto['cantidadProductoNuevo'] : '0';
                    $precio = !empty($producto['precioProductoNuevo']) ? $producto['precioProductoNuevo'] : '0.00';
                    $subtotal = number_format($cantidad * $precio, 2); // Calcular subtotal
                    
                    // Concatenar correctamente cada fila con datos reales
                    $html .= '<tr>
                                <td>' . $contador . '</td>
                                <td>' . $nombre . '</td>
                                <td>' . $cantidad . '</td>
                                <td>$' . $precio . '</td>
                                <td>$' . $subtotal . '</td>
                              </tr>';
                    $subTotalGeneral += floatval($subtotal);
                    $contador++;
                } else {
                    $idProductoViejo = $producto['idProductoViejo'];
                    $item = "id";
                    $valor = $idProductoViejo;

                    // Obtener el producto con el controlador
                    $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

                    if(!empty($productoLei)){
                        $nombre = $productoLei["nombre"];
                        $cantidad = !empty($producto['cantidadProductoViejo']) ? $producto['cantidadProductoViejo'] : '0';
                        $precio = $productoLei['precio_compra'];
                        $subtotal = number_format($cantidad * $precio, 2); // Calcular subtotal
                        
                        // Concatenar correctamente cada fila con datos reales
                        $html .= '<tr>
                                    <td>' . $contador . '</td>
                                    <td>' . $nombre . '</td>
                                    <td>' . $cantidad . '</td>
                                    <td>$' . $precio . '</td>
                                    <td>$' . $subtotal . '</td>
                                  </tr>';
                    }
                    $subTotalGeneral += floatval($subtotal);
                    $contador++;
                }
                
            }
        } else {
            $html .= '<tr><td colspan="5">No hay productos disponibles.</td></tr>';
        }
    $html .= '
        <tr style="text-align: right">
            <td colspan="5">Sub total $'.$subTotalGeneral.'</td>
        </tr>
        <tr style="text-align: right">
            <td colspan="5">IVA $'.$subTotalGeneral*0.13.'</td>
        </tr>
        <tr style="text-align: right">
            <td colspan="5">Total $'.($subTotalGeneral*0.13)+$subTotalGeneral.'</td>
        </tr>
    </table>
    <br>
    <p>Sello y/o firma de autorización:</p>
    <br><br><br>
    <p>Nombre de quién autoriza:</p>
    <br><br><br>
    <p>Esta orden no tiene valides sin su respectiva autorización</p>
    
    </div>';

    $pdf->writeHTML($html, true, false, true, false, '');


    // reset pointer to the last page
    $pdf->lastPage();

    // ---------------------------------------------------------

    //Close and output PDF document
    $pdf->Output('Orden de compra '.$ordens["id"].'.pdf', 'I');

    //============================================================+
    // END OF FILE
    //============================================================+
}

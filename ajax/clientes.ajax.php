<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

require_once "../controladores/facturas.controlador.php";
require_once "../modelos/facturas.modelo.php";

class AjaxClientes{

    /*=============================================
	VALIDAR CLIENTE CON FACTURAS
	=============================================*/	
	public $validarCliente;
    public function ajaxValidarCliente() {

        $item = "id_cliente";
        $valor = $this->validarCliente;
        $orden = "id";
        $optimizacion = "no";

        // Obtener los productos relacionados con esta categoría
        $respuesta = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        // Devolver la respuesta como JSON
        echo json_encode($respuesta);

    }

    /*=============================================
	VALIDAR MOTORISTA CON FACTURAS
	=============================================*/	
	public $validarMot;
    public function ajaxValidarMotorista() {

        $item = "id_motorista";
        $valor = $this->validarMot;
        $orden = "id";
        $optimizacion = "no";

        // Obtener los productos relacionados con esta categoría
        $respuesta = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        // Devolver la respuesta como JSON
        echo json_encode($respuesta);

    }

    /*=============================================
    EDITAR CLIENTE
    =============================================*/    

    public $idCliente;

    public function ajaxEditarCliente(){

        $item = "id";
        $orden = "id";
        $valor = $this->idCliente;

        $respuesta = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);

        echo json_encode($respuesta);

    }

    /*=============================================
    EDITAR MOTORISTA
    =============================================*/    

    public $idMotorista;

    public function ajaxEditarMotorista(){

        $item = "id";
        $orden = "id";
        $valor = $this->idMotorista;

        $respuesta = ControladorClientes::ctrMostrarMotoristas($item, $valor, $orden);

        echo json_encode($respuesta);

    }

    /*=============================================
    EDITAR PROVEEDOR
    =============================================*/    

    public $idProveedor;

    public function ajaxEditarProveedor(){

        $item = "id";
        $orden = "id";
        $valor = $this->idProveedor;

        $respuesta = ControladorClientes::ctrMostrarProveedores($item, $valor, $orden);

        echo json_encode($respuesta);

    }

    /*=============================================
    EDITAR COMPRA
    =============================================*/    

    public $idCompra;

    public function ajaxEditarCompra(){

        $item = "id";
        $orden = "id";
        $valor = $this->idCompra;

        $respuesta = ControladorFacturas::ctrMostrarCompras($item, $valor, $orden, "no");

        echo json_encode($respuesta);

    }

    /*=============================================
    MOSTRAR PROVEEDOR
    =============================================*/    

    public $idProveedorM;

    public function ajaxEditarProveedorM(){

        $item = "nit";
        $orden = "id";
        $valor = $this->idProveedorM;

        $respuesta = ControladorClientes::ctrMostrarProveedores($item, $valor, $orden);

        echo json_encode($respuesta);

    }

    /*=============================================
    EDITAR DATOS EMPRESARIALES
    =============================================*/    

    public $idEmpresa;

    public function ajaxEditarEmpresa(){

        $item = "id";
        $orden = "id";
        $valor = $this->idEmpresa;

        $respuesta = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

        echo json_encode($respuesta);

    }

    /*=============================================
    LISTAR CLIENTES EN MODAL DE FACTURACION
    =============================================*/    

    public $paginaClientes = 1;
    public $busquedaClientes = "";
    public $contextoClientes = "facturacion";

    private function textoSeguro($valor){

        return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
    }

    public function ajaxListarClientesFacturacion(){

        $limite = 10;
        $pagina = max(1, (int) $this->paginaClientes);
        $busqueda = trim((string) $this->busquedaClientes);
        $contexto = (string) $this->contextoClientes;
        $total = ControladorClientes::ctrContarClientesPaginados($busqueda);
        $totalPaginas = max(1, (int) ceil($total / $limite));
        $pagina = min($pagina, $totalPaginas);
        $offset = ($pagina - 1) * $limite;
        $clientes = ControladorClientes::ctrMostrarClientesPaginados($busqueda, $limite, $offset);

        $puedeFacturar = isset($_SESSION["tokenInicioSesionMh"]) && $_SESSION["tokenInicioSesionMh"] !== "";
        $html = "";

        foreach($clientes as $key => $value){

            if($contexto === "cotizacion-autorizada"){
                $html .= '<tr>
                    <td>'.($offset + $key + 1).'</td>
                    <td>'.$this->textoSeguro($value["nombre"]).'</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-info btnCrearCotizacionAutorizada" idCliente="'.$this->textoSeguro($value["id"]).'"><i class="fa fa-file-text"></i> Seleccionar cliente</button>
                        </div>
                    </td>
                </tr>';
                continue;
            }

            $tipo = "";
            if($value["tipo_cliente"] == "00"){
                $tipo = "Persona normal";
            }
            if($value["tipo_cliente"] == "01"){
                $tipo = "Declarante IVA";
            }
            if($value["tipo_cliente"] == "02"){
                $tipo = "Empresa con beneficios fiscales";
            }
            if($value["tipo_cliente"] == "03"){
                $tipo = "Diplomático";
            }

            $tipoPersona = "";
            if($value["tipoPersona"] == "1"){
                $tipoPersona = "Persona natural";
            }
            if($value["tipoPersona"] == "2"){
                $tipoPersona = "Persona Juridica";
            }

            $direccion = $value["departamento"].", ".$value["municipio"].", ".$value["distrito"].", ".$value["direccion"];

            $html .= '<tr>
                <td>'.($offset + $key + 1).'</td>
                <td>'.$this->textoSeguro($value["nombre"]).'</td>
                <td>'.$this->textoSeguro($value["correo"]).'</td>
                <td>'.$this->textoSeguro($value["telefono"]).'</td>
                <td>'.$this->textoSeguro($direccion).'</td>
                <td>'.$this->textoSeguro($value["NIT"]).'</td>
                <td>'.$this->textoSeguro($value["DUI"]).'</td>
                <td>'.$this->textoSeguro($value["NRC"]).'</td>
                <td>'.$this->textoSeguro($tipo).'</td>
                <td>'.$this->textoSeguro($value["nombrePais"]).'</td>
                <td>'.$this->textoSeguro($tipoPersona).'</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-warning btnEditarCliente" idCliente="'.$this->textoSeguro($value["id"]).'" data-toggle="modal" data-target="#modalEditarCliente"><i class="fa fa-pencil"></i></button>
                        <button class="btn btn-danger btnEliminarCliente" idCliente="'.$this->textoSeguro($value["id"]).'"><i class="fa fa-times"></i></button>';

            if($puedeFacturar){
                $html .= '<button class="btn btn-info btnEscogerFactura" idCliente="'.$this->textoSeguro($value["id"]).'"><i class="fa fa-file-text"></i></button>';
            }

            $html .= '</div>
                </td>
            </tr>';
        }

        if($html === ""){
            $columnas = $contexto === "cotizacion-autorizada" ? 3 : 12;
            $html = '<tr><td colspan="'.$columnas.'" class="text-center">No se encontraron clientes.</td></tr>';
        }

        $paginacion = "";
        if($totalPaginas > 1){
            $inicio = max(1, $pagina - 2);
            $fin = min($totalPaginas, $pagina + 2);
            $paginacion .= '<ul class="pagination justify-content-center flex-wrap">';
            for($numeroPagina = $inicio; $numeroPagina <= $fin; $numeroPagina++){
                $activo = $numeroPagina === $pagina ? " active" : "";
                $paginacion .= '<li class="page-item'.$activo.'"><button type="button" class="page-link btnPaginaClientesFacturacion" data-pagina="'.$numeroPagina.'">'.$numeroPagina.'</button></li>';
            }
            $paginacion .= '</ul>';
        }

        echo json_encode(array(
            "html" => $html,
            "paginacion" => $paginacion,
            "total" => $total,
            "pagina" => $pagina,
            "totalPaginas" => $totalPaginas,
            "mostrando" => count($clientes)
        ));
    }

}

/*=============================================
EDITAR CLIENTE
=============================================*/
if(isset($_POST["idCliente"])){

    $editar = new AjaxClientes();
    $editar -> idCliente = $_POST["idCliente"];
    $editar -> ajaxEditarCliente();

}

/*=============================================
EDITAR MOTORISTA
=============================================*/
if(isset($_POST["idMotorista"])){

    $editarM = new AjaxClientes();
    $editarM -> idMotorista = $_POST["idMotorista"];
    $editarM -> ajaxEditarMotorista();

}

/*=============================================
EDITAR PROVEEDOR
=============================================*/
if(isset($_POST["idProveedor"])){

    $editarP = new AjaxClientes();
    $editarP -> idProveedor = $_POST["idProveedor"];
    $editarP -> ajaxEditarProveedor();

}

/*=============================================
EDITAR COMPRA
=============================================*/
if(isset($_POST["idCompra"])){

    $editarC = new AjaxClientes();
    $editarC -> idCompra = $_POST["idCompra"];
    $editarC -> ajaxEditarCompra();

}

/*=============================================
MOSTRAR PROVEEDOR
=============================================*/
if(isset($_POST["idProveedorM"])){

    $editarPM= new AjaxClientes();
    $editarPM -> idProveedorM = $_POST["idProveedorM"];
    $editarPM -> ajaxEditarProveedorM();

}

/*=============================================
EDITAR DATO EMPREARIALES
=============================================*/
if(isset($_POST["idEmpresa"])){

    $editarEmpresa = new AjaxClientes();
    $editarEmpresa -> idEmpresa = $_POST["idEmpresa"];
    $editarEmpresa -> ajaxEditarEmpresa();

}

/*=============================================
VALIDAR NO ELIMINAR CLIENTE QUE TENGA FACTURAS
=============================================*/

if(isset($_POST["idClienteValidar"])){
    
    $valCliente = new AjaxClientes();
    $valCliente -> validarCliente = $_POST["idClienteValidar"];
    $valCliente -> ajaxValidarCliente();

}

/*=============================================
LISTAR CLIENTES EN MODAL DE FACTURACION
=============================================*/
if(isset($_POST["listarClientesFacturacion"])){

    $listarClientes = new AjaxClientes();
    $listarClientes -> paginaClientes = isset($_POST["pagina"]) ? $_POST["pagina"] : 1;
    $listarClientes -> busquedaClientes = isset($_POST["busqueda"]) ? $_POST["busqueda"] : "";
    $listarClientes -> contextoClientes = isset($_POST["contexto"]) ? $_POST["contexto"] : "facturacion";
    $listarClientes -> ajaxListarClientesFacturacion();

}

/*=============================================
VALIDAR NO ELIMINAR MOTORISTA QUE TENGA FACTURAS
=============================================*/

if(isset($_POST["idMotoristaValidar"])){
    
    $valMot = new AjaxClientes();
    $valMot -> validarMot = $_POST["idMotoristaValidar"];
    $valMot -> ajaxValidarMotorista();

}

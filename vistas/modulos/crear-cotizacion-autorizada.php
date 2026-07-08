<?php

    if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Facturación" || $_SESSION["rol"] == "Contabilidad" || $_SESSION["rol"] == "Bodega"){
    } else {
        echo '<script>
        window.location = "inicio";
        </script>';
    return;
    }

?>

<div class="main-content content-wrapper">

  <section class="content-header">
    <?php
        $item = "id";
        $valor = $_GET["idClienteEscogerFactura"];
        $orden = "id";

        $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);
    ?>
    <button class="btn btn-primary" onclick="location.href='cotizaciones-autorizadas'">
          
          Regresar

    </button>
    <br><br>
    <h1>
      
      Cotización autorizada
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio </a></li>
      
      <li class="active">&nbsp;Sistema de facturación</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
        Datos de facturación
<br><br>
      </div>

      <div class="box-body">
        
       <!--=====================================
        FORMULARIO CREAR FACTURA
        ======================================-->

        <form role="form" method="post" id="enviarFacturaLoca" enctype="multipart/form-data">

            <!--=====================================
            CABEZA DEL MODAL
            ======================================-->

            <div class="modal-header" style="background:grey; color:white">
                <h4 class="modal-title">Crear cotización autorizada para el cliente 
                    <?php 
                        $tipo = "";
                        if($cliente["tipo_cliente"] == "00"){
                            $tipo = "Persona normal";
                        }
                        if($cliente["tipo_cliente"] == "01"){
                            $tipo = "Declarante IVA";
                        }
                        if($cliente["tipo_cliente"] == "02"){
                            $tipo = "Empresa con beneficios fiscales";
                        }
                        if($cliente["tipo_cliente"] == "03"){
                            $tipo = "Diplomático";
                        }
                        echo($cliente["nombre"]." - ");
                        echo($tipo)
                    ?>
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!--=====================================
            CUERPO DEL MODAL
            ======================================-->
            
            <div class="modal-body">

                <div class="box-body">

                    <div class="row">

                        <div class="col-xl-6 col-xs-12">
                            Identificador de la cotización:
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="identificadorCotizacion" required>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ENTRADA PARA EL CLIENTE -->
                    <div class="form-group">
                        <input type="number" id="contador" value="0" hidden>
                        <input type="text" name="nuevoCliente" value="<?php echo($_GET["idClienteEscogerFactura"]) ?>" hidden>
                        <input type="text" id="productos" name="productos" hidden>
                        <input type="text" name="nuevoClienteFactura" id="nuevoClienteFactura" value="<?php echo($cliente["id"]) ?>" hidden>
                    </div>

                    <hr style="border: 1px solid black; width: 80%; margin: 20px auto;">
                    <!-- Contenedor donde se agregarán los productos -->
                    <div id="productosContainer"></div>

                    <div class="form-group">
                        <div class="input-group mb-3">
                            <button type="button" class="btn btn-success btn-block btnAgregarProductoFactura">Agregar producto</button>
                        </div>
                    </div>

                    <!-- Plantilla del producto -->
                    <div class="producto-template" style="display:none;">
                        <!-- Contenedor del producto -->
                        <div class="producto-item">
                            <div class="row">
                                <!-- Seleccionar producto -->
                                <div class="col-xl-6 col-xs-12">

                                    <div class="form-group">
                                        Seleccionar producto
                                   
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <span><i class="fa fa-text-width"></i></span>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control codigoProducto" name="codigoProducto[]" placeholder="Ingrese el código del producto">

                                            <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura">
                                                <option value="" selected disabled>Seleccione un producto</option>
                                                <?php
                                                    $item = null;
                                                    $valor = null;
                                                    $orden = "id";

                                                    $productos = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);

                                                    foreach ($productos as $key => $value){
                                                        if($value["stock"] > 0){
                                                            echo '<option data-modelo="'.$value["modelo"].'" data-marca="'.$value["marca"].'" data-origen="'.$value["origen"].'" data-peso="'.$value["peso"].'" data-exento="'.$value["exento_iva"].'" data-value="'.$value["id"].'" data-precio="'.$value["precio_venta"].'" data-codigo="'.htmlspecialchars($value["codigo"], ENT_QUOTES, 'UTF-8').'">'.$value["nombre"].' '.$value["codigo"].'</option>';
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                </div>

                                <!-- Cantidad -->
                                <div class="col-xl-2 col-xs-12">
                                    Cantidad
                                    
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                            </div>
                                            <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" min="1">
                                        </div>
                                    </div>
                                </div>

                                <!-- Precio unitario sin impuestos -->
                                <div class="col-xl-2 col-xs-12">
                                    Precio unitario sin impuestos
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" id="nuevoPrecioJc">
                                    </div>
                                </div>

                                <!-- Venta grabada individual (más IVA) -->
                                <div class="col-xl-2 col-xs-12">
                                    Venta grabada individual (+ IVA)
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly>
                                    </div>
                                </div>

                                <!-- Total -->
                                <div class="col-xl-2 col-xs-12">
                                    Total sin IVA
                                    <br><br>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividualSin[]" readonly>
                                    </div>
                                </div>

                                <!-- Total -->
                                <div class="col-xl-2 col-xs-12">
                                    Total
                                    <br><br>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly>
                                    </div>
                                </div>

                                <div class="col-xl-2 col-xs-12">
                                    Peso: <br><br>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-text-width"></i></span>
                                        </div>
                                        <input type="text" class="form-control peso" name="peso[]" id="peso">
                                    </div>
                                </div>

                                <div class="col-xl-2 col-xs-12">
                                    Origen:<br><br>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-text-width"></i></span>
                                        </div>
                                        <input type="text" class="form-control origen" name="origen[]" id="origen">
                                    </div>
                                </div>

                                <div class="col-xl-2 col-xs-12">
                                    Marca:<br><br>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-text-width"></i></span>
                                        </div>
                                        <input type="text" class="form-control marca" name="marca[]" id="marca">
                                    </div>
                                </div>

                                <div class="col-xl-2 col-xs-12">
                                    Modelo:<br><br>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-text-width"></i></span>
                                        </div>
                                        <input type="text" class="form-control modelo" name="modelo[]" id="modelo">
                                    </div>
                                </div>

                                <div class="col-xl-3 col-xs-12">
                                    Total a disminuir por cada uno de los items - colocar sin iva (si lleva iva se suma automaticamente)
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                        </div>
                                        <input type="text" class="form-control descuentoItem" name="descuentoItem[]" min="0" max="'.$producto["precioSinImpuestos"].'" readonly>
                                    </div>
                                </div>

                                <div class="col-xl-2 col-xs-12">
                                    Porcentaje de descuento según lo ingresado (ejemplo 40, 33, SIN EL PORCENTAJE)
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                        </div>
                                        <input type="text" class="form-control porcentajeDescuentoItem" name="porcentajeDescuento[]" min="0" max="100" readonly>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-xs-12">
                                    Contraseña desbloqueo de descuentos
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-font"></i></span>
                                        </div>
                                        <input type="password" id="contraDesbloqueo" class="form-control desbloqueoDescuentos" name="contraDescuentos[]">
                                    </div>
                                </div>

                                <div class="col-xl-2 col-xs-12">
                                    <br>
                                    <button type="button" class="btn btn-warning btnEliminarAutorizacionDescuentos">Eliminar autorización descuentos</button>
                                </div>

                                <!-- Botón de eliminación -->
                                <div class="col-xl-12 col-xs-12 text-right">
                                    <button type="button" class="btn btn-danger btnEliminarProducto1">Eliminar producto</button>
                                </div>
                                <br><br><hr style="border: 1px solid black; width: 80%; margin: 20px auto;">
                            </div>
                        </div>
                    </div>


                    <?php

                        if($cliente["tipo_cliente"] == "01" || $cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03"){ // Contribuyente, beneficios y diplomas
                            echo '<div class="row">

                            <div class="col-xl-2 col-xs-12 ml-auto">
                                <p>Total factura sin IVA:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                    </div>
                                    <input type="number" class="form-control" name="nuevoTotalFacturaSin" readonly>
                                </div>
                            </div>

                            <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                <p>Total factura con IVA:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                    </div>
                                    <input type="number" class="form-control" name="nuevoTotalFactura" readonly>
                                </div>
                            </div>

                            <div class="col-xl-2 col-xs-12">
                                <p>Total descuento sin IVA:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                    </div>
                                    <input type="number" class="form-control" name="totalDescuentoSin" id="" readonly>
                                </div>
                            </div>

                            <div class="col-xl-2 col-xs-12" hidden>
                                <p>Total descuento con IVA:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                    </div>
                                    <input type="number" class="form-control" name="totalDescuento" id="" readonly>
                                </div>
                            </div>

                        </div>';
                        }
                        
                    ?>

                    

                </div>
            </div>

            <!--=====================================
            PIE DEL MODAL
            ======================================-->

            <div class="modal-footer">
                <button type="submit" class="btn btn-dark">Crear cotización autorizada</button>
            </div>

        </form>

        <?php

          $crearFactura = new ControladorFacturas();
          $crearFactura -> ctrCrearCotizacion();

        ?> 

        </div>

    </div>


  </section>

</div>
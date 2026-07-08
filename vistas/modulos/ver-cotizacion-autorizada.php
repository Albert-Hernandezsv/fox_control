<?php

    if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Facturación" || $_SESSION["rol"] == "Contabilidad" || $_SESSION["rol"] == "Vendedor" || $_SESSION["rol"] == "Bodega"){
    } else {
        echo '<script>
        window.location = "inicio";
        </script>';
    return;
    }

?>
<div id="loader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(148, 148, 148, 0.37); display: flex; justify-content: center; align-items: center; z-index: 9999;">
    <h2>Cargando...</h2>
</div>
<script>
    $(window).on('load', function() {
        $('#loader').fadeOut();
    });
</script>
<div class="main-content content-wrapper">

  <section class="content-header">
    <?php

        $item = "id";
        $valor = $_GET["idCotizacionAutorizada"];
        $orden = "fecEmi";
        $optimizacion = "no";

        $cotizacion= ControladorFacturas::ctrMostrarCotizacionesAutorizadas($item, $valor, $orden, $optimizacion);

        $item = "id";
        $valor = $cotizacion["id_cliente"];
        $orden = "id";

        $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);
    ?>
    <button class="btn btn-success" onclick="location.href='cotizaciones-autorizadas'">Cancelar</button>
    <button class="btn btn-info" onclick="location.href='extensiones/TCPDF-main/examples/imprimir-cotizacion-autorizada.php?idCotizacionAutorizada='+<?php echo $cotizacion['id'] ?>">Generar pdf</button>
    <br><br>
    <h1>
      
      Cotización <?php echo($cotizacion["codigo"]) ?> del cliente <?php echo($cliente["nombre"]) ?>
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio </a></li>
      
      <li class="active">&nbsp;Sistema de facturación</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
        Datos de cotización
<br><br>
      </div>

      
        
      <div class="box-body">
        
       <!--=====================================
        FORMULARIO CREAR FACTURA
        ======================================-->

        <form role="form" method="post" id="enviarFacturaLoca" enctype="multipart/form-data">
            <input type="text" name="idCotizacion" value="<?php echo($cotizacion["id"]) ?>" hidden>
            <input type="text" name="nuevoCliente" id="nuevoCliente" value="<?php echo($cliente["id"]) ?>" hidden>

            <!--=====================================
            CABEZA DEL MODAL
            ======================================-->

            <div class="modal-header" style="background:grey; color:white">
                <h4 class="modal-title">Productos</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!--=====================================
            CUERPO DEL MODAL
            ======================================-->

            <div class="modal-body">

                <div class="box-body">

                <div class="row">

                        <div class="col-xl-2 col-xs-12">
                            <p style="font-weight: bold;">Fecha de emisión:</p>
                        </div>

                        <div class="col-xl-2 col-xs-12">
                            <p><?php echo $cotizacion["fecEmi"] ?></p>
                        </div>

                </div>


                <div class="row">

                        <div class="col-xl-1 col-xs-12">
                            <p style="font-weight: bold;">Estado:</p>
                        </div>

                        <div class="col-xl-1 col-xs-12">
                            <p><?php echo $cotizacion["estado"] ?></p>
                        </div>

                        <?php
                             $item = "id";
                             $valor = $cotizacion["id_usuario"];
                     
                             $usuario = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
                            
                             $nombreVendedor = $usuario["nombre"];
                        ?>

                        <div class="col-xl-1 col-xs-12">
                            <p style="font-weight: bold;">Vendedor:</p>
                        </div>

                        <div class="col-xl-2 col-xs-12">
                            <p><?php echo $nombreVendedor ?></p>
                        </div>
                        

                </div>
                <form role="form" method="post" id="enviarFacturaLoca" enctype="multipart/form-data">
                    <!-- ENTRADA PARA EL CLIENTE -->
                    <div class="form-group">
                        Cliente:
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                            </div>
                            <input type="number" id="contador" value="0" hidden>
                            <input type="text" id="productos" name="productos" hidden>
                            <select name="editarClienteFactura" id="nuevoClienteFactura" class="form-control" required readonly>
                                <?php

                                    $item = "id";
                                    $valor = $cotizacion["id_cliente"];
                                    $orden = "id";

                                    $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);
                                    echo '<option value="'.$cliente["id"].'">'.$cliente["nombre"].' '.$tipo.' </option>';
                                ?> 
                            </select>
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
                                                            echo '<option data-origen="'.$value["origen"].'" data-peso="'.$value["peso"].'" data-exento="'.$value["exento_iva"].'" data-value="'.$value["id"].'" data-precio="'.$value["precio_venta"].'" data-codigo="'.htmlspecialchars($value["codigo"], ENT_QUOTES, 'UTF-8').'">'.$value["nombre"].' '.$value["codigo"].'</option>';
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
                                    Peso:
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

                    <!-- Contenedor donde se agregarán los productos -->
                    <div id="productosContainer">
                        <?php
                            // JSON en $cotizacion["id_cliente"]
                            $jsonProductos = $cotizacion["productos"];

                            // Decodificar el JSON en un array PHP
                            $productos = json_decode($jsonProductos, true); // true convierte el JSON en un array asociativo
                            $contador = count($productos);

                            echo '<input type="number" id="contador" hidden value="'.$contador.'">';
                            // Verificar si la decodificación fue exitosa
                            if (is_array($productos)) {
                                $totalGravado = 0.0;
                                // Recorrer e imprimir cada producto
                                foreach ($productos as $producto) {
                                        echo '<!-- ENTRADA PARA EL PRODUCTO -->
                                            <div class="producto-item">
                                                <div class="row">
                                            
                                                    <div class="col-xl-6 col-xs-12">
                                                        <div class="form-group">
                                                            Producto
                                                            
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1">
                                                                        <span>1</span>
                                                                    </span>
                                                                </div>
                                                                <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>';
                                                                    
                                                                        $item = "id";
                                                                        $valor = $producto["idProducto"];
                                                                        
                                                                        $product = ControladorProductos::ctrMostrarProductos($item, $valor);

                                                                        echo '<option data-modelo="'.$producto["modelo"].'" data-marca="'.$value["marca"].'" data-codigo="'.htmlspecialchars($product["codigo"], ENT_QUOTES, 'UTF-8').'" data-value="'.$product["id"].'" data-precio="'.$product["precio_venta"].'">'.$product["nombre"].' - '.htmlspecialchars($product["codigo"], ENT_QUOTES, 'UTF-8').'</option>';
                                                                    
                                                                echo '</select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-2 col-xs-12">
                                                        Cantidad
                                                        
                                                        <div class="form-group">
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-desc"></i></span>
                                                                </div>
                                                                <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="'.$producto["cantidad"].'">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-2 col-xs-12">
                                                        Precio unitario sin impuestos
                                                        
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" value="'.$producto["precioSinImpuestos"].'" id="nuevoPrecioJc">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-2 col-xs-12">
                                                        Venta grabada individual (más IVA)
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="'.$producto["precioConIva"].'">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-2 col-xs-12">
                                                        Total sin IVA
                                                        <br><br>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.$producto["totalProducto"].'">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-2 col-xs-12">
                                                        Peso
                                                        <br><br>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-text-width"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control" name="peso[]" value="'.$producto["peso"].'" id="peso">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-2 col-xs-12">
                                                        Origen
                                                        <br><br>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-text-width"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control" name="origen[]" value="'.$producto["origen"].'" id="origen">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-2 col-xs-12">
                                                        Marca:<br><br>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-text-width"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control marca" name="marca[]" value="'.$producto["marca"].'" id="marca">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-2 col-xs-12">
                                                        Modelo:<br><br>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-text-width"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control modelo" name="modelo[]" value="'.$producto["modelo"].'" id="modelo">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-3 col-xs-12" hidden>
                                                        Total a disminuir por cada uno de los items - con iva
                                                        <br><br>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control descuentoItem" name="descuentoItem[]" min="0" value="'.(($producto["descuento"]*0.13)+$producto["descuento"]).'" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-3 col-xs-12" hidden>
                                                        Porcentaje de descuento según lo ingresado (ejemplo 40, 33, SIN EL PORCENTAJE)
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control porcentajeDescuentoItem" name="porcentajeDescuento[]" min="0" value="'.round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2).'%" readonly>
                                                        </div>
                                                    </div>

                                                    <!-- Total -->
                                                    <div class="col-xl-3 col-xs-12">
                                                        Total con IVA
                                                        <br><br>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                            </div>
                                                            <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="'.($producto["precioConIva"] - $producto["descuentoConIva"])*$producto["cantidad"].'">
                                                        </div>
                                                    </div>
                                                    <!-- Botón de eliminación -->
                                                    <div class="col-xl-12 col-xs-12 text-right">
                                                        <button type="button" class="btn btn-danger btnEliminarProducto1">Eliminar producto</button>
                                                    </div>
                                                    <br><br>
                                                </div>
                                            </div>
                                            ';
                                    
                                }
                            } else {
                                echo "Error: El formato de los datos de productos es incorrecto.";
                            }

                        ?>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <button type="button" class="btn btn-success btn-block btnAgregarProductoFactura">Agregar producto</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-dark">Guardar cotización</button>
                    </div>
                </form>
                <?php

                    $editarCotizacion = new ControladorFacturas();
                    $editarCotizacion -> ctrEditarCotizacionAutorizada();

                    ?>
                </div>
            </div>
            <!--=====================================
            PIE DEL MODAL
            ======================================-->

            
        </form>

        </div>

    </div>


  </section>

</div>

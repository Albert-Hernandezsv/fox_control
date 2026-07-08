<?php

    if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Facturación" || $_SESSION["rol"] == "Contabilidad" || $_SESSION["rol"] == "Vendedor"){
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
        $valor = $_GET["idCliente"];
        $orden = "id";

        $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);
    ?>
    <button class="btn btn-primary" onclick="location.href='facturacion'">Regresar</button>
    <button class="btn btn-success" data-toggle="modal" data-target="#modalVerCotizacionesAutorizadas">A partir de una cotización autorizada</button>
    <br><br>
    <h1>
      
      Factura
    
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
                <h4 class="modal-title">Crear factura para el cliente 
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

                    <!-- ENTRADA PARA EL CLIENTE -->
                    <div class="form-group">
                        <input type="number" id="contador" value="0" hidden>
                        <input type="text" name="tipoDte" id="tipoDte" value="01" hidden>
                        <input type="text" id="productos" name="productos" hidden>
                        <input type="text" name="nuevoClienteFactura" id="nuevoClienteFactura" value="<?php echo($cliente["id"]) ?>" hidden>
                    </div>

                    <!-- ENTRADA PARA EL VENDEDOR Y QUIEN FACTURA -->
                    <div class="form-group">
                        <p>Vendedor que realiza la venta:</p>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                            </div>
                            <input type="text" name="nuevoFacturadorId" value="<?php echo $_SESSION["id"]?>" hidden>
                            <select name="nuevoVendedorId" class="form-control" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                                <?php
                                     $item = null;
                                     $valor = null;
                             
                                     $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
                             
                                    foreach ($usuarios as $key => $value){
                                        echo '
                                            <option value="'.$value["id"].'">'.$value["nombre"]." - ".$value["rol"].'</option>
                                        ';
                                    }
                                ?>
                                
                                
                            </select>
                        </div>
                    </div>

                    <!-- ENTRADA PARA LA CONDICIÓN DE LA OPERACIÓN -->
                    <div class="form-group">
                        <p>Condición de la operación:</p>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                            </div>
                            <select name="condicionOperacion" id="condicionOperacion" class="form-control" required>
                                <option value="" disabled selected>Seleccione una opción</option>
                                <option value="1">Contado</option>
                                <option value="2">A crédito</option>
                                <option value="3">Otro</option>
                            </select>
                        </div>
                    </div>

                    <div class="row camposCreditoFactura" style="display:none;">
                        <div class="col-xl-6 col-xs-12">
                            <div class="form-group">
                                <p>Plazo de pago:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                                    </div>
                                    <select name="plazo_pago" class="form-control plazoPagoFactura">
                                        <option value="" disabled selected>Seleccione una opci&oacute;n</option>
                                        <option value="01">01 D&iacute;as</option>
                                        <option value="02">02 Meses</option>
                                        <option value="03">03 A&ntilde;os</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-xs-12">
                            <div class="form-group">
                                <p>Periodo de pago:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-sort-numeric-asc"></i></span>
                                    </div>
                                    <input type="number" name="periodo_pago" class="form-control periodoPagoFactura" min="1" step="1" placeholder="Ingrese un n&uacute;mero mayor a 0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ENTRADA PARA GRAN CONTRIBUYENTE -->
                    <div class="form-group">
                        <p>¿Retención del 1%? (si seleccionas que si la retención y su total solo aparece en el pdf y Hacienda):</p>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                            </div>
                            <select name="granContribuyente" id="granContribuyente" class="form-control" required>
                                <option value="No">No</option>
                                <option value="Si">Si</option>
                            </select>
                        </div>
                    </div>

                    <!-- Contenedor donde se agregarán los productos -->
                    <?php
                        if (isset($_GET["idCotizacionUsar"])) {
                            ?>
                            <div id="productosContainer">
                                <?php
                                // JSON en $cotizacion["productos"]
                                $item = "id";
                                $valor = $_GET["idCotizacionUsar"];
                                $orden = "fecEmi";
                                $optimizacion = "no";

                                $cotizacion= ControladorFacturas::ctrMostrarCotizacionesAutorizadas($item, $valor, $orden, $optimizacion);
                                $jsonProductos = $cotizacion["productos"];

                                // Decodificar el JSON en un array PHP
                                $productos = json_decode($jsonProductos, true); // true convierte el JSON en un array asociativo
                                $contador = count($productos);

                                echo '<input type="number" id="contador" hidden value="' . $contador . '">';

                                // Verificar si la decodificación fue exitosa
                                if (is_array($productos)) {
                                    // Recorrer e imprimir cada producto
                                    foreach ($productos as $producto) {
                                        ?>
                                        <!-- ENTRADA PARA EL PRODUCTO -->
                                        <div class="row productoItem">
                                            <div class="col-xl-6 col-xs-12">
                                                <div class="form-group">
                                                    Producto
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1">
                                                                <span>1</span>
                                                            </span>
                                                        </div>
                                                        <select name="nuevoIdProductoFactura[]" class="form-control select2 seleccionarProductoFactura" required readonly>
                                                            <?php
                                                            $item = "id";
                                                            $valor = $producto["idProducto"];
                                                            $product = ControladorProductos::ctrMostrarProductos($item, $valor);
                                                            echo '<option data-codigo="' . htmlspecialchars($product["codigo"], ENT_QUOTES, 'UTF-8') . '" data-value="' . $product["id"] . '" data-precio="' . $product["precio_venta"] . '">' . $product["nombre"] . ' - ' . htmlspecialchars($product["codigo"], ENT_QUOTES, 'UTF-8') . '</option>';
                                                            ?>
                                                        </select>
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
                                                        <input type="number" class="form-control nuevaCantidadProductoFactura" name="nuevaCantidadProductoFactura[]" required min="1" value="<?php echo $producto["cantidad"]; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-2 col-xs-12">
                                                Precio unitario sin impuestos
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                    </div>
                                                    <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" value="<?php echo $producto["precioSinImpuestos"]; ?>" id="nuevoPrecioJc">
                                                </div>
                                            </div>

                                            <div class="col-xl-2 col-xs-12">
                                                Venta grabada individual (más IVA)
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                    </div>
                                                    <input type="number" class="form-control" name="nuevoIvaProductoFactura[]" readonly value="<?php echo $producto["precioConIva"]; ?>">
                                                </div>
                                            </div>

                                            <div class="col-xl-2 col-xs-12">
                                                Total sin IVA
                                                <br><br>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                    </div>
                                                    <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="<?php echo ($producto["precioSinImpuestos"] * $producto["cantidad"]); ?>">
                                                </div>
                                            </div>

                                            <div class="col-xl-2 col-xs-12">
                                                Peso
                                                <br><br>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-text-width"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control" name="peso[]" value="<?php echo $producto["peso"]; ?>" id="peso">
                                                </div>
                                            </div>

                                            <div class="col-xl-2 col-xs-12">
                                                Origen
                                                <br><br>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-text-width"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control" name="origen[]" value="<?php echo $producto["origen"]; ?>" id="origen">
                                                </div>
                                            </div>

                                            <div class="col-xl-2 col-xs-12">
                                                Marca
                                                <br><br>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-text-width"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control" name="marca[]" value="<?php echo $producto["marca"]; ?>" id="marca">
                                                </div>
                                            </div>

                                            <div class="col-xl-2 col-xs-12">
                                                Modelo
                                                <br><br>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-text-width"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control" name="modelo[]" value="<?php echo $producto["modelo"]; ?>" id="modelo">
                                                </div>
                                            </div>

                                            <div class="col-xl-3 col-xs-12" hidden>
                                                Total a disminuir por cada uno de los items - con IVA
                                                <br><br>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control descuentoItem" name="descuentoItem[]" min="0" value="<?php echo (($producto["descuento"] * 0.13) + $producto["descuento"]); ?>" readonly>
                                                </div>
                                            </div>

                                            <div class="col-xl-3 col-xs-12" hidden>
                                                Porcentaje de descuento (ejemplo: 40, 33, SIN EL %)
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control porcentajeDescuentoItem" name="porcentajeDescuento[]" min="0" value="<?php echo round((($producto["descuento"] / $producto["precioSinImpuestos"]) * 100), 2) . '%'; ?>" readonly>
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
                                                    <input type="number" class="form-control" name="nuevoTotalProductoFacturaIndividual[]" readonly value="<?php echo ($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"]; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    echo '<button class="btn btn-warning" onclick="actualizarTotalFactura()" type="button">Aplicar cambios a la factura!!</button>';
                                    // Después de generar los elementos, imprime un script para llamar a la función JS
                        ;

                                } else {
                                    echo "Error: El formato de los datos de productos es incorrecto.";
                                }
                                ?>
                            </div>
                            <?php
                        } else {
                            ?>

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
                                        Venta grabada individual (más IVA)
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="text" class="form-control" name="nuevoIvaProductoFactura[]" readonly>
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
                                        Total con IVA
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

                                    <div class="col-xl-2 col-xs-12">
                                                    Marca
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-text-width"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control marca" name="marca[]" id="marca">
                                                    </div>
                                                </div>

                                                <div class="col-xl-2 col-xs-12">
                                                    Modelo
                                                    <br><br>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-text-width"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control modelo" name="modelo[]" id="modelo">
                                                    </div>
                                                </div>

                                    <div class="col-xl-3 col-xs-12" hidden>
                                        Total a disminuir por cada uno de los items - colocar sin iva (si lleva iva se suma automaticamente)
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="text" class="form-control descuentoItem" name="descuentoItem[]" min="0" max="'.$producto["precioSinImpuestos"].'" readonly>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-xs-12" hidden>
                                        Porcentaje de descuento según lo ingresado (ejemplo 40, 33, SIN EL PORCENTAJE)
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                            </div>
                                            <input type="text" class="form-control porcentajeDescuentoItem" name="porcentajeDescuento[]" min="0" max="100" readonly>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-xs-12" hidden>
                                        Contraseña desbloqueo de descuentos
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-font"></i></span>
                                            </div>
                                            <input type="password" id="contraDesbloqueo" class="form-control desbloqueoDescuentos" name="contraDescuentos[]">
                                        </div>
                                    </div>

                                    <div class="col-xl-2 col-xs-12" hidden>
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
                            }
                        ?>
                        

                    <?php

                        if($cliente["tipo_cliente"] == "00" || $cliente["tipo_cliente"] == "01"){ // Normal o contribuyente
                            echo '<div class="row">

                            <div class="col-xl-2 col-xs-12 ml-auto" hidden>
                                <p>Total factura sin IVA:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                    </div>
                                    <input type="number" class="form-control" name="nuevoTotalFacturaSin" readonly>
                                </div>
                            </div>

                            <div class="col-xl-2 col-xs-12 ml-auto">
                                <p>Total factura con IVA:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                    </div>
                                    <input type="number" class="form-control" name="nuevoTotalFactura" readonly>
                                </div>
                            </div>

                            <div class="col-xl-2 col-xs-12" hidden>
                                <p>Total descuento sin IVA:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                    </div>
                                    <input type="number" class="form-control" name="totalDescuentoSin" id="" readonly>
                                </div>
                            </div>

                            <div class="col-xl-2 col-xs-12">
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
                        if($cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03"){ // Beneficios o Diplomático
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
                <button type="submit" class="btn btn-dark">Crear factura localmente</button>
            </div>

        </form>

        <?php

          $crearFactura = new ControladorFacturas();
          $crearFactura -> ctrCrearFactura();

        ?> 

        </div>

    </div>


  </section>

</div>

<!--=====================================
MODAL VER COTIZACIONES AUTORIZADAS
======================================-->

<div id="modalVerCotizacionesAutorizadas" class="modal fade bd-example-modal-lg" role="dialog" style="width: 100% !important; font-size:80%">
  
  <div class="modal-dialog modal-lg" style="max-width: 90%;">

    <div class="modal-content">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:grey; color:white">
          <h4 class="modal-title">Cotizaciones desde bodega aprobadas</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">
             <p id="resumenCotizacionesModal" class="text-muted">Abra el modal para cargar las cotizaciones.</p>
             <!-- Añadir el contenedor responsivo -->
             <div class="table-responsive">
             <table class="table table-bordered table-striped dt-responsive tablas tabla-servidor" width="100%" style="font-size: 80%">
         
                <thead>
                
                <tr>
                    
                    <th style="width:10px">#</th>
                    <th style="width:200px">Cliente</th>
                    <th style="width:200px">Creado por</th>
                    <th style="width:300px">Cotización</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
        
                </tr> 
        
                </thead>
        
                <tbody id="tablaCotizacionesModal">
        
                    <?php
        
                    $item = null;
                    $valor = null;
                    $orden = "fecEmi";
                    $optimizacion = "no";
                    
        
                    $cotizaciones = array();
                    foreach ($cotizaciones as $key => $value){
                        if($value["estado"] == "Facturacion"){
                            $item = "id";
                            $valor = $value["id_cliente"];
                            $orden = "id";
            
                            $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);
            
                            $item = "id";
                            $valor = $value["id_usuario"];
                    
                            $usuario = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
                            // Suponiendo que $value["fecha"] tiene el valor '2024-10-19 22:36:44'
                            $fechaOriginal = new DateTime($value["fecEmi"]);
                            $fechaFormateada = $fechaOriginal->format('d \d\e F \d\e Y'); // Formato deseado
            
                            echo ' <tr>
                                    <td>'.($key+1).'</td>
                                    <td>'.$cliente["nombre"].'</td>
                                    <td>'.$usuario["nombre"].'</td>
                                    <td>'.$value["codigo"].'</td>
                                    <td>'.$value["estado"].'</td>
                                    <td>'.$fechaFormateada.'</td>
            
                                    <td>
            
                                        <div class="btn-group">
                                            <button class="btn btn-success btnUsarCotizacionAutorizada" url="'.($_SERVER["REQUEST_URI"]).'" idCotizacionAutorizada="'.$value["id"].'" ><i class="fa fa-pencil-square-o"></i></button>
                                            <button class="btn btn-danger btnRegresarBodega" idCotizacionAutorizada="'.$value["id"].'"><i class="fa fa-times"></i></button>
                                        </div>  
            
                                    </td>
            
                                </tr>';
                        }
                        
                    }
                        
        
        
                    ?> 
        
                </tbody>
        
                </table>
            </div>
            <nav id="paginacionCotizacionesModal" aria-label="Paginaci&oacute;n de cotizaciones"></nav>
            
            

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-dark pull-left" data-dismiss="modal">Salir</button>

        </div>

      </div>
      
    </div>

    </div>

  </div>

</div>

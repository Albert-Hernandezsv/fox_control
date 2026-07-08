<?php

  if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Facturación" || $_SESSION["rol"] == "Contabilidad" || $_SESSION["rol"] == "Bodega"){
  } else {
      echo '<script>
      window.location = "inicio";
      </script>';
    return;
  }
// Configurar la zona horaria de El Salvador
date_default_timezone_set('America/El_Salvador');
?>

<div class="main-content content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Crear orden de compra
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio </a></li>
      
      <li class="active">&nbsp;Sistema de facturación</li>
    
    </ol>

  </section>

  <section class="content">
    <button class="btn btn-success" onclick="location.href='ordenes-compra'">Regresar</button>
    <br><br>
    <div class="box">

      <div class="box-body">
        
            <form role="form" method="post" enctype="multipart/form-data">

                <!--=====================================
                CABEZA DEL MODAL
                ======================================-->

                <div class="modal-header" style="background:grey; color:white">
                <h4 class="modal-title">Generar orden de compra</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>

                <!--=====================================
                CUERPO DEL MODAL
                ======================================-->

                <div class="modal-body">

                <div class="box-body">
                    <input type="text" id="productosOrden" name="productosOrden" hidden>
                    <!-- ENTRADA PARA EL PROVEEDOR -->
                    
                    <div class="form-group">
                        Seleccionar proveedor:
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                            </div>
                            <select class="form-control" name="nuevoIdProveedor" id="" required>
                            <option value="" selected disabled>Seleccione un proveedor</option>
                            <?php
                                $item = null;
                                $valor = null;
                                $orden = "id";
                        
                                $proveedores = ControladorClientes::ctrMostrarProveedores($item, $valor, $orden);

                                foreach($proveedores as $proveedor){
                                    echo '<option value="'.$proveedor["id"].'">'.$proveedor["nombre"].'</option>';
                                }
                            ?>
                            </select>
                        </div>
                    </div>

                    <!-- ENTRADA PARA LA FECHA -->
                    <div class="form-group">
                        Seleccionar fecha de creación:
                        <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                </div>
                                <input type="date" class="form-control" name="nuevaFechaOrden" required>
                        </div>
                    </div>

                    <!-- Contenedor donde se agregarán los productos -->
                    <div id="productosOrdenContainer"></div>

                    <div class="form-group">
                        <div class="input-group mb-3">
                            <button type="button" class="btn btn-success btn-block btnAgregarProductoOrden">Agregar producto</button>
                        </div>
                    </div>

                    <!-- Plantilla del producto -->
                    <div class="producto-orden-template" style="display:none;">
                        <!-- Contenedor del producto -->
                        <div class="producto-orden-item">
                            <div class="row">

                                <!-- PRODUCTO NUEVO? -->
                                <div class="col-xl-2 col-xs-12">
                                    <div class="form-group">
                                        ¿Producto existente en inventario?
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <span><i class="fa fa-question-circle"></i></span>
                                                </span>
                                            </div>
                                            <input class="form-control productoViejo" type="checkbox" value="">
                                        </div>
                                    </div>
                                </div>

                                <!-- PRODUCTO NUEVO NOMBRE -->
                                <div class="col-xl-3 col-xs-12">
                                    <div class="form-group">
                                        Nombre de producto nuevo:
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <span><i class="fa fa-text-width"></i></span>
                                                </span>
                                            </div>
                                            <input class="form-control" type="text" value="" name="nuevoNombreProductoNuevo[]" id="nuevoNombreProductoNuevo">
                                        </div>
                                    </div>
                                </div>

                                <!-- PRODUCTO NUEVO PRECIO SIN INVA -->
                                <div class="col-xl-3 col-xs-12">
                                    <div class="form-group">
                                        Precio sin IVA de producto nuevo:
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <span><i class="fa fa-usd"></i></span>
                                                </span>
                                            </div>
                                            <input class="form-control" type="text" value="" name="nuevoPrecioProductoNuevo[]" id="nuevoPrecioProductoNuevo">
                                        </div>
                                    </div>
                                </div>

                                <!-- PRODUCTO NUEVO CANTIDAD -->
                                <div class="col-xl-3 col-xs-12">
                                    <div class="form-group">
                                        Cantidad de producto nuevo:
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <span><i class="fa fa-sort-numeric-desc"></i></span>
                                                </span>
                                            </div>
                                            <input class="form-control" type="number" min="1" value="1" name="nuevoCantidadProductoNuevo[]" id="nuevoCantidadProductoNuevo" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- PRODUCTO VIEJO -->
                                <div class="col-xl-6 col-xs-12">
                                    <div class="form-group">
                                        Seleccionar producto en inventario:
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <span><i class="fa fa-text-width"></i></span>
                                                </span>
                                            </div>

                                            <select name="nuevoIdProductoViejo[]" id="nuevoIdProductoViejo" class="form-control nuevoIdProductoViejo" disabled>
                                                <option value="" selected disabled>Seleccione un producto</option>
                                                <?php
                                                    $item = null;
                                                    $valor = null;
                                                    $orden = "id";

                                                    $productos = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);

                                                    foreach ($productos as $key => $value){
                                                        if($value["stock"] > 0){
                                                            echo '<option data-value="'.$value["id"].'">'.$value["nombre"].' '.$value["codigo"].'</option>';
                                                        }
                                                    }
                                                ?>
                                            </select>


                                        </div>
                                    </div>
                                </div>

                                <!-- PRODUCTO VIEJO CANTIDAD -->
                                <div class="col-xl-3 col-xs-12">
                                    <div class="form-group">
                                        Cantidad de producto en inventario:
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <span><i class="fa fa-sort-numeric-desc"></i></span>
                                                </span>
                                            </div>
                                            <input class="form-control" type="number" min="1" value="1" name="nuevoCantidadProductoViejo[]" id="nuevoCantidadProductoViejo" readonly>
                                        </div>
                                    </div>
                                </div>
                                <!-- Botón de eliminación -->
                                <div class="col-xl-12 col-xs-12 text-right">
                                    <button type="button" class="btn btn-danger btnEliminarProductoOrden">Eliminar producto</button>
                                </div>
                                <br><br><hr style="border: 1px solid black; width: 80%; margin: 20px auto;">
                            </div>
                        </div>
                    </div>

                <!--=====================================
                PIE DEL MODAL
                ======================================-->

                <div class="modal-footer">

                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

                <button type="submit" class="btn btn-dark">Guardar orden de compra</button>

                </div>

                <?php

                    $crearOrden = new ControladorOrdenes();
                    $crearOrden -> ctrCrearOrden();

                ?>

            </form>

      </div>

    </div>

  </section>

</div>
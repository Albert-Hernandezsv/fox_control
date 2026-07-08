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
      
      Sistema de ordenes de compra
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio </a></li>
      
      <li class="active">&nbsp;Sistema de facturación</li>
    
    </ol>

  </section>

  <section class="content">
    <button class="btn btn-primary" onclick="location.href='crear-orden-compra'">Crear orden de compras</button>

    <div class="box">

      <div class="box-body">
        
        <table class="table table-bordered table-striped dt-responsive tablas" width="100%">
         
            <thead>
            
            <tr>
                
                <th style="width:10px">#</th>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Productos</th>
                <th>Acciones</th>
    
            </tr> 
    
            </thead>
    
            <tbody>
    
                    <?php
            
                            $item = null;
                            $valor = null;
                            $orden = "id";
                    
                            $ordenes = ControladorOrdenes::ctrMostrarOrdenes($item, $valor, $orden);
                    
                            foreach ($ordenes as $key => $ordens){
                                $item = "id";
                                $valor = $ordens["id_proveedor"];
                                $orden = "id";
                        
                                $proveedor = ControladorClientes::ctrMostrarProveedores($item, $valor, $orden);

                                // Decodificar el campo JSON 'productos' para convertirlo en un array de PHP
                                $productos = json_decode($ordens['productos'], true);  // 'true' convierte el JSON en un array asociativo

                                echo ' <tr>
                                        <td>'.($key+1).'</td>
                                        <td>'.$ordens["fecha"].'</td>
                                        <td>'.$proveedor["nombre"].'</td>
                                        <td>';
                                        
                                        if (!empty($productos)) {
                                          foreach ($productos as $producto) {
                                              // Mostrar los detalles del producto de manera ordenada
                                              if (!empty($producto['nombreProductoNuevo'])) {
                                                  echo $producto['nombreProductoNuevo'] . '<br>';
                                              }
                                          }
                                      } else {
                                          echo 'No hay productos disponibles.';
                                      }
                                        
                        
                                                    
                                        echo '</td>
                                        <td>
                        
                                            <div class="btn-group">
                                                
                                            <button class="btn btn-info btnVerOrden" idOrden="'.$ordens["id"].'"><i class="fa fa-eye"></i></button>
                        
                                            <button class="btn btn-danger btnEliminarOrden" idOrden="'.$ordens["id"].'"><i class="fa fa-times"></i></button>';
                        
                                            echo '</div>  
                        
                                        </td>
                        
                                        </tr>';
                            }
            
            
                    ?> 
    
            </tbody>
 
        </table>
      </div>

    </div>

  </section>

</div>
<?php

  $borrarOrden = new ControladorOrdenes();
  $borrarOrden -> ctrBorrarOrden();

?>


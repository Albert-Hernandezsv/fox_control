<?php

  if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Facturación" || $_SESSION["rol"] == "Contabilidad" || $_SESSION["rol"] == "Vendedor" || $_SESSION["rol"] == "Bodega"){
  } else {
      echo '<script>
      window.location = "inicio";
      </script>';
    return;
  }

?>

<div class="main-content content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Crear cotización autorizada
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio </a></li>
      
      <li class="active">&nbsp;Sistema de facturación</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
        <button class="btn btn-success" onclick="location.href='inventario'">
            Cancelar
        </button>
        <button class="btn btn-secondary" data-toggle="modal" data-target="#modalVerClientes">
          
          Ver clientes registrados - crear cotización autorizada

        </button>

<br><br>
      </div>
      <div class="box-body">

          <?php
            $paginaActual = isset($_GET["pagina"]) ? max(1, (int) $_GET["pagina"]) : 1;
            $registrosPorPagina = 10;
            $totalRegistros = ControladorFacturas::ctrContarCotizacionesAutorizadas();
            $totalPaginas = max(1, (int) ceil($totalRegistros / $registrosPorPagina));
            $paginaActual = min($paginaActual, $totalPaginas);
            $offset = ($paginaActual - 1) * $registrosPorPagina;
            $cotizaciones = ControladorFacturas::ctrMostrarCotizacionesAutorizadasPaginadas($registrosPorPagina, $offset);
          ?>

       <p>
         Mostrando <?php echo count($cotizaciones); ?> de <?php echo $totalRegistros; ?> cotizaciones autorizadas.
         P&aacute;gina <?php echo $paginaActual; ?> de <?php echo $totalPaginas; ?>.
       </p>

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

        <tbody>

            <?php

              foreach ($cotizaciones as $key => $value){
                // Suponiendo que $value["fecha"] tiene el valor '2024-10-19 22:36:44'
                $fechaOriginal = new DateTime($value["fecEmi"]);
                $fechaFormateada = $fechaOriginal->format('d \d\e F \d\e Y'); // Formato deseado

                echo ' <tr>
                        <td>'.($offset+$key+1).'</td>
                        <td>'.($value["cliente_nombre"] ?: "Cliente no disponible").'</td>
                        <td>'.($value["usuario_nombre"] ?: "Usuario no disponible").'</td>
                        <td>'.$value["codigo"].'</td>
                        <td>'.$value["estado"].'</td>
                        <td>'.$fechaFormateada.'</td>

                        <td>

                            <div class="btn-group">';
                            if($value["estado"] == "Bodega"){
                                echo '<button class="btn btn-warning btnVerCotizacionAutorizada" idCotizacionAutorizada="'.$value["id"].'"><i class="fa fa-eye"></i></button>
                                <button class="btn btn-info btnEnviarFacturacion" idCotizacionAutorizada="'.$value["id"].'" ><i class="fa fa-pencil-square-o"></i></button>
                                <button class="btn btn-danger btnEliminarCotizacionAutorizada" idCotizacionAutorizada="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                            } else {
                                echo '<button class="btn btn-warning btnVerCotizacionAutorizada" idCotizacionAutorizada="'.$value["id"].'"><i class="fa fa-eye"></i></button>';
                            }
                                
                            echo '</div>  

                        </td>

                    </tr>';
              }
                


            ?> 

        </tbody>

       </table>
       </div>

       <?php if($totalPaginas > 1): ?>
        <nav aria-label="Paginaci&oacute;n de cotizaciones autorizadas">
          <ul class="pagination justify-content-center flex-wrap">
            <li class="page-item <?php echo $paginaActual <= 1 ? "disabled" : ""; ?>">
              <a class="page-link" href="<?php echo $paginaActual <= 1 ? "#" : "index.php?ruta=cotizaciones-autorizadas&pagina=".($paginaActual - 1); ?>" <?php echo $paginaActual <= 1 ? 'tabindex="-1" aria-disabled="true"' : 'rel="prev"'; ?>>Anterior</a>
            </li>

            <?php
              $inicioPagina = max(1, $paginaActual - 2);
              $finPagina = min($totalPaginas, $paginaActual + 2);
              for($numeroPagina = $inicioPagina; $numeroPagina <= $finPagina; $numeroPagina++):
            ?>
              <li class="page-item <?php echo $numeroPagina === $paginaActual ? "active" : ""; ?>">
                <a class="page-link" href="index.php?ruta=cotizaciones-autorizadas&pagina=<?php echo $numeroPagina; ?>" <?php echo $numeroPagina === $paginaActual ? 'aria-current="page"' : ""; ?>><?php echo $numeroPagina; ?></a>
              </li>
            <?php endfor; ?>

            <li class="page-item <?php echo $paginaActual >= $totalPaginas ? "disabled" : ""; ?>">
              <a class="page-link" href="<?php echo $paginaActual >= $totalPaginas ? "#" : "index.php?ruta=cotizaciones-autorizadas&pagina=".($paginaActual + 1); ?>" <?php echo $paginaActual >= $totalPaginas ? 'tabindex="-1" aria-disabled="true"' : 'rel="next"'; ?>>Siguiente</a>
            </li>
          </ul>
        </nav>
       <?php endif; ?>

      </div>

    </div>

  </section>

</div>

<!--=====================================
MODAL VER CLIENTES
======================================-->

<div id="modalVerClientes" class="modal fade bd-example-modal-lg" role="dialog" data-contexto="cotizacion-autorizada" style="width: 100% !important; font-size:80%">
  
  <div class="modal-dialog modal-lg" style="max-width: 90%;">

    <div class="modal-content">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:grey; color:white">
          <h4 class="modal-title">Clientes registrados</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">
             <div class="row" style="margin-bottom: 15px;">
               <div class="col-md-8 col-xs-12">
                 <label for="buscarClienteFacturacion">Buscar cliente por nombre</label>
                 <input type="text" class="form-control" id="buscarClienteFacturacion" placeholder="Escriba el nombre del cliente">
               </div>
               <div class="col-md-4 col-xs-12">
                 <label>&nbsp;</label>
                 <button type="button" class="btn btn-secondary btn-block" id="btnLimpiarBusquedaClientes">Limpiar b&uacute;squeda</button>
               </div>
             </div>

             <p id="resumenClientesFacturacion" class="text-muted">Abra el modal para cargar los clientes.</p>
             <!-- Añadir el contenedor responsivo -->
             <div class="table-responsive">
                <table class="table table-bordered table-striped dt-responsive tablas tabla-servidor" width="100%">
              
                    <thead>
                      
                      <tr>
                        
                        <th style="width:10px">#</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
            
                      </tr> 
            
                    </thead>
            
                    <tbody id="tablaClientesFacturacion">
            
                    <?php
            
                    $item = null;
                    $valor = null;
                    $orden = "id";
            
                    $clientes = array();
            
                    foreach ($clientes as $key => $value){
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
                      echo ' <tr>
                              <td>'.($key+1).'</td>
                              <td>'.$value["nombre"].'</td>';
            
                                        
                              echo '
                              <td>
            
                                <div class="btn-group">
                                    <button class="btn btn-info btnCrearCotizacionAutorizada" idCliente="'.$value["id"].'"><i class="fa fa-file-text"> Seleccionar cliente</i></button>
                                </div>  
            
                              </td>
            
                            </tr>';
                    }
            
            
                    ?>
                    <tr><td colspan="3" class="text-center">Los clientes se cargar&aacute;n al abrir este modal.</td></tr>
            
                    </tbody>

              </table>
            </div>

            <nav id="paginacionClientesFacturacion" aria-label="Paginaci&oacute;n de clientes"></nav>
            
            

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

<?php

  $borrarCotizacionAutorizada = new ControladorFacturas();
  $borrarCotizacionAutorizada -> ctrBorrarCotizacionAutorizada();

?> 

<?php

  $pasarCotizacionAutorizada = new ControladorFacturas();
  $pasarCotizacionAutorizada -> ctrPasarCotizacionAutorizada();

?> 

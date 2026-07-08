<?php

  if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Facturación" || $_SESSION["rol"] == "Contabilidad"){
  } else {
      echo '<script>
      window.location = "inicio";
      </script>';
    return;
  }
// Configurar la zona horaria de El Salvador
date_default_timezone_set('America/El_Salvador');
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
    
    <h1>
      
      Sistema de compras - F07
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio </a></li>
      
      <li class="active">&nbsp;Sistema de facturación</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">
    
      <div class="box-header with-border">
        <h3>Compras ingresadas</h3>
        <button class="btn btn-success" onclick="comprasExcel()">Descargar reporte como Excel</button>
        <button class="btn btn-info" onclick="comprasCsv()">Descargar reporte como CSV</button>
        <button class="btn btn-warning" data-toggle="modal" data-target="#modalCrearFolioCompras">Generar folios</button>
        <p style="color: red; font-size: 15px">¡Importante! Siempre revisa tu archivo CSV antes de subirlo al Ministerio de Hacienda - Qwerty Systems no es responsable de un mal manejo de las herramientas</p>
      </div>
<br>
      <div class="box-body">        

        <?php
            // Verificar si los parámetros existen y asignarlos a variables
            $filtroFechaInicio = isset($_GET['filtroFechaInicio']) ? $_GET['filtroFechaInicio'] : '00-00-0000';
            $filtroFechaFin = isset($_GET['filtroFechaFin']) ? $_GET['filtroFechaFin'] : '00-00-0000';

            echo '
                <h4>Filtros aplicados actualmente</h4>
                <div class="row">
                  <div class="col-xl-2 col-xs-12">
                    Fecha inicio: '.$filtroFechaInicio.'
                  </div>
                  <div class="col-xl-2 col-xs-12">
                    Fecha fin: '.$filtroFechaFin.'
                  </div>
                </div>
            ';
        ?>
        <hr>
        <br>
        <h5>Filtrar facturas:</h5>
        <form role="form" method="get" action="index.php?ruta=ver-compras" enctype="multipart/form-data">
          <input type="hidden" name="ruta" value="ver-compras">

          <div class="row">

            <div class="col-xl-2 col-xs-12">

                <!-- ENTRADA PARA FILTRO DE FECHA DE INICIO -->
                <div class="form-group">
                <p>Filtrar por fecha inicio:</p>
                  <div class="input-group mb-3">
                        <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                          <input type="date" class="form-control" id="filtroFechaInicio" name="filtroFechaInicio">
                  </div>

                </div>

            </div>

            <div class="col-xl-2 col-xs-12">

                <!-- ENTRADA PARA FILTRO DE FECHA FINALIZACION -->
                <div class="form-group">
                <p>Filtrar por fecha fin:</p>
                  <div class="input-group mb-3">
                        <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="date" class="form-control" id="filtroFechaFin" name="filtroFechaFin" value="<?php echo $_GET["filtroFechaFin"]; ?>">
                  </div>

                </div>

            </div>



          </div>
          <button type="submit" class="btn btn-dark">Aplicar filtros</button>
        </form>

        
        <br>
        <style>
            .tablas {
                table-layout: fixed;
                width: 100%;
            }
    

    

            td {
                word-wrap: break-word; /* El texto en las celdas se puede dividir en líneas */
            }
        </style>
          <?php
              $optimizacion;
              if(!isset($_GET["optimizar"])){
                $optimizacion = "si";
                echo "<div class='form-check form-switch'>
                  <input class='form-check-input' type='checkbox' role='switch' id='flexSwitchCheckChecked' onclick=\"location.href='index.php?ruta=ver-compras&filtroFechaInicio=".$filtroFechaInicio."&filtroFechaFin=".$filtroFechaFin."&optimizar=no'\" checked>
                  <label class='form-check-label' for='flexSwitchCheckChecked'>Optimizar tablas</label>
                </div>";
              } else {
                $optimizacion = "no";
                echo "<div class='form-check form-switch'>
                  <input class='form-check-input' type='checkbox' role='switch' id='flexSwitchCheckChecked' onclick=\"location.href='index.php?ruta=ver-compras&filtroFechaInicio=".$filtroFechaInicio."&filtroFechaFin=".$filtroFechaFin."'\">
                  <label class='form-check-label' for='flexSwitchCheckChecked'>Optimizar tablas</label>
                </div>";
              }
          ?>

       <table class="table table-bordered table-striped dt-responsive tablas" id="anexoCompras" width="100%" style="font-size: 60%">
         
        <thead>
         
         <tr>
           
         <th>Fecha</th>
            <th>Clase de documento</th>
            <th>Tipo de documento</th>
            <th>Número de documento (para DTE código de generación)</th>
            <th>NIT o NRC del proveedor (sin guiones)</th>
            <th>Nombre del proveedor</th>
            <th>Compras internas exentas</th>
            <th>Internaciones exentas y/o no sujetas</th>
            <th>Importaciones exentas y/o no sujetas</th>
            <th>Compras internas gravadas</th>
            <th>Internaciones gravadas de bienes</th>
            <th>Importaciones gravadas de bienes</th>
            <th>Importaciones gravadas de servicios</th>
            <th>Crédito fiscal</th>
            <th>Total de compras</th>
            <th>Dui del proveedor (solo si es persona natural)</th>
            <th>Tipo de operación (renta)</th>
            <th>Clasificación (renta)</th>
            <th>Sector (renta)</th>
            <th>Tipo de costo/gasto (renta)</th>
            <th>Anexo</th>
            <th>Acciones</th>

         </tr> 

        </thead>
                            
        <tbody>

            <?php
              
              $item = null;
              $valor = null;
              $orden = "fecha";

              $compras = ControladorFacturas::ctrMostrarCompras($item, $valor, $orden, $optimizacion);

              foreach ($compras as $key => $value){

                    if (
                      (
                          ($filtroFechaInicio == "todos" && $filtroFechaFin == "todos") || 
                          ($filtroFechaInicio != "todos" && $filtroFechaFin == "todos" && $value["fecha"] >= $filtroFechaInicio) ||
                          ($filtroFechaInicio == "todos" && $filtroFechaFin != "todos" && $value["fecha"] <= $filtroFechaFin) ||
                          ($filtroFechaInicio != "todos" && $filtroFechaFin != "todos" && $value["fecha"] >= $filtroFechaInicio && $value["fecha"] <= $filtroFechaFin)
                      )
                  ){
                          
                          // Suponiendo que $value["fecha"] tiene el valor '2024-10-19 22:36:44'
                          $fechaOriginal = new DateTime($value["fecha"]);
                          $fechaFormateada = $fechaOriginal->format('d/m/Y'); // Formato deseado: 25/12/2024
                        
                          echo ' <tr>
                                    <td>'.$fechaFormateada.'</td>
                                    <td>'.$value["clase_documento"].'</td>
                                    <td>'.$value["tipo_documento"].'</td>
                                    <td>'.$value["numero_documento"].'</td>
                                    <td>'.$value["nit_nrc"].'</td>
                                    <td>'.$value["nombre_proveedor"].'</td>
                                    <td>'.$value["compras_internas_exentas"].'</td>
                                    <td>'.$value["internaciones_exentas_y_no_sujetas"].'</td>
                                    <td>'.$value["importaciones_exentas_y_no_sujetas"].'</td>
                                    <td>'.$value["compras_internas_gravadas"].'</td>
                                    <td>'.$value["internaciones_gravadas_de_bienes"].'</td>
                                    <td>'.$value["importaciones_gravadas_de_bienes"].'</td>
                                    <td>'.$value["importaciones_gravadas_de_servicios"].'</td>
                                    <td>'.$value["credito_fiscal"].'</td>
                                    <td>'.$value["total_de_compras"].'</td>
                                    <td>'.$value["dui_del_proveedor"].'</td>
                                    <td>'.$value["tipo_de_operacion"].'</td>
                                    <td>'.$value["clasificacion"].'</td>
                                    <td>'.$value["sector"].'</td>
                                    <td>'.$value["tipo"].'</td>
                                    <td>'.$value["anexo"].'</td>
                                    <td>
                                        <button class="btn btn-warning btnEditarCompra" idCompra="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarCompra"><i class="fa fa-pencil"></i></button>
                                        <button class="btn btn-danger btnEliminarCompra" idCompra="'.$value["id"].'"><i class="fa fa-times"></i></button>
                                    </td>
                            </tr>';
                    }
                
                          
              }


            ?> 

        </tbody>

       </table>

      </div>

    </div>

  </section>

</div>
<!--=====================================
MODAL EDITAR COMPRAS
======================================-->

<div id="modalEditarCompra" class="modal" role="dialog">
  
  <div class="modal-dialog modal-lg">

    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="tipoFolio" value="todos">
        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:grey; color:white">
          <h4 class="modal-title">Editar compra</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">
          <div class="row">

              <div class="col-xl-6 col-xs-12">
                <input type="hidden" id="editarIdCompra" name="editarIdCompra">

                <div class="form-group">
                  <label>Fecha</label>
                  <input type="date" id="editarFechaCompra" name="editarFechaCompra" class="form-control">
                </div>
              </div>

              <div class="col-xl-6 col-xs-12">
                <div class="form-group">
                  <label>Clase de Documento</label>
                  <select id="editarclase_documentoCompra" name="editarclase_documentoCompra" class="form-control" required>
                    <option id="editarclase_documentoComprat" value=""></option>
                    <option value="1">IMPRESO POR IMPRENTA O TICKET</option>
                    <option value="2">FORMULARIO ÚNICO</option>
                    <option value="3">OTROS</option>
                    <option value="4">DTE</option>
                  </select>
                </div>
              </div>

          </div>

          <div class="row">

              <div class="col-xl-6 col-xs-12">
                <div class="form-group">
                  <label>Tipo de Documento</label>
                  <select id="editartipo_documentoCompra" name="editartipo_documentoCompra" class="form-control" required>
                    <option id="editartipo_documentoComprat" value=""></option>
                    <option value="03">CCF</option>
                    <option value="05">NOTA DE CRÉDITO</option>
                    <option value="06">NOTA DE DÉBITO</option>
                    <option value="11">FACTURA DE EXPORTACIÓN</option>
                    <option value="12">DECLARACIÓN DE MERCANCÍA</option>
                    <option value="13">MANDAMIENTO DE INGRESO</option>
                  </select>
                </div>
              </div>

              <div class="col-xl-6 col-xs-12">
                <div class="form-group">
                  <label>Número de Documento</label>
                  <input type="text" id="editarnumero_documentoCompra" name="editarnumero_documentoCompra" class="form-control" required>
                </div>
              </div>

          </div>

          <div class="row">

              <div class="col-xl-6 col-xs-12">
                <div class="form-group">
                  <label>NIT o NRC</label>
                  <input type="text" id="editarnit_nrcCompra" name="editarnit_nrcCompra" class="form-control" required>
                </div>
              </div>

              <div class="col-xl-6 col-xs-12">
                <div class="form-group">
                  <label>Nombre del Proveedor</label>
                  <input type="text" id="editarnombre_proveedorCompra" name="editarnombre_proveedorCompra" class="form-control" required>
                </div>
              </div>

          </div>


          <!-- Campos monetarios -->
          <div class="form-row">
            <div class="form-group col">
              <label>Compras Internas Exentas</label><br><br>
              <input type="text" id="editarcompras_internas_exentasCompra" name="editarcompras_internas_exentasCompra" class="form-control" value="0.00">
            </div>
            <div class="form-group col">
              <label>Internaciones Exentas y No Sujetas</label>
              <input type="text" id="editarinternaciones_exentas_y_no_sujetasCompra" name="editarinternaciones_exentas_y_no_sujetasCompra" class="form-control" value="0.00">
            </div>
            <div class="form-group col">
              <label>Importaciones Exentas y No Sujetas</label>
              <input type="text" id="editarimportaciones_exentas_y_no_sujetasCompra" name="editarimportaciones_exentas_y_no_sujetasCompra" class="form-control" value="0.00">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col">
              <label>Compras Internas Gravadas</label>
              <input type="text" id="editarcompras_internas_gravadasCompra" name="editarcompras_internas_gravadasCompra" class="form-control" value="0.00">
            </div>
            <div class="form-group col">
              <label>Internaciones Gravadas de Bienes</label>
              <input type="text" id="editarinternaciones_gravadas_de_bienesCompra" name="editarinternaciones_gravadas_de_bienesCompra" class="form-control" value="0.00">
            </div>
            <div class="form-group col">
              <label>Importaciones Gravadas de Bienes</label>
              <input type="text" id="editarimportaciones_gravadas_de_bienesCompra" name="editarimportaciones_gravadas_de_bienesCompra" class="form-control" value="0.00">
            </div>
            <div class="form-group col">
              <label>Importaciones Gravadas de Servicios</label>
              <input type="text" id="editarimportaciones_gravadas_de_serviciosCompra" name="editarimportaciones_gravadas_de_serviciosCompra" class="form-control" value="0.00">
            </div>
          </div>

          <div class="row">

              <div class="col-xl-6 col-xs-12">
                <div class="form-row">
                  <div class="form-group col">
                    <label>Crédito Fiscal</label>
                    <input type="text" id="editarcredito_fiscalCompra" name="editarcredito_fiscalCompra" class="form-control" readonly value="0.00">
                  </div>
                </div>
              </div>

              <div class="col-xl-6 col-xs-12">
                <div class="form-row">
                  <div class="form-group col">
                    <label>Total de Compras</label>
                    <input type="text" id="editartotal_de_comprasCompra" name="editartotal_de_comprasCompra" class="form-control" readonly value="0.00">
                  </div>
                </div>
              </div>

          </div>

          <div class="row">

              <div class="col-xl-6 col-xs-12">
                <div class="form-group">
                  <label>DUI del Proveedor</label>
                  <input type="text" id="editardui_del_proveedorCompra" name="editardui_del_proveedorCompra" class="form-control">
                </div>
              </div>

              <div class="col-xl-6 col-xs-12">
                <div class="form-group">
                  <label>Tipo de Operación</label>
                  <select id="editartipo_de_operacionCompra" name="editartipo_de_operacionCompra" class="form-control" required>
                    <option id="editartipo_de_operacionComprat" value=""></option>
                    <option value="1">GRAVADA</option>
                    <option value="2">NO GRAVADA O EXENTA</option>
                    <option value="3">EXCLUIDO O NO CONSTITUYE RENTA</option>
                    <option value="4">MIXTA</option>
                    <option value="9">EXCEPCIONES</option>
                    <option value="0">ANTES DEL PRESENTE AÑO FISCAL</option>
                  </select>
                </div>
              </div>

          </div>

          <div class="row">

              <div class="col-xl-6 col-xs-12">
                <div class="form-group">
                  <label>Clasificación</label>
                  <select id="editarclasificacionCompra" name="editarclasificacionCompra" class="form-control" required>
                    <option id="editarclasificacionComprat" value=""></option>
                    <option value="1">COSTO</option>
                    <option value="2">GASTO</option>
                    <option value="9">EXCEPCIONES</option>
                    <option value="0">ANTES DEL PRESENTE AÑO FISCAL</option>
                  </select>
                </div>
              </div>

              <div class="col-xl-6 col-xs-12">
                <div class="form-group">
                  <label>Sector</label>
                  <select id="editarsectorCompra" name="editarsectorCompra" class="form-control" required>
                    <option id="editarsectorComprat" value=""></option>
                    <option value="1">INDUSTRIA</option>
                    <option value="2">COMERCIO</option>
                    <option value="3">AGROPECUARIA</option>
                    <option value="4">SERVICIOS PROFESIONALES</option>
                    <option value="9">EXCEPCIONES</option>
                    <option value="0">ANTES DEL PRESENTE AÑO FISCAL</option>
                  </select>
                </div>
              </div>

          </div>

              <div class="row">

                  <div class="col-xl-6 col-xs-12">
                    <div class="form-group">
                      <label>Tipo</label>
                      <select id="editartipoCompra" name="editartipoCompra" class="form-control" required>
                        <option id="editartipoComprat" value=""></option>
                        <option value="1">Gasto de Venta sin Donación</option>
                        <option value="2">GASTOS DE ADMINISTRACIÓN SIN DONACIÓN</option>
                        <option value="3">GASTOS FINANCIEROS SIN DONACIÓN</option>
                        <option value="4">Costo Artículos Producidos/Comprados Importaciones/Internaciones</option>
                        <option value="5">Costo Artículos Producidos/Comprados Interno</option>
                        <option value="6">Costo Indirectos de Fabricación</option>
                        <option value="7">Mano de obra</option>
                        <option value="9">EXCEPCIONES</option>
                        <option value="0">ANTES DEL PRESENTE AÑO FISCAL</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-xl-6 col-xs-12">
                    <div class="form-group">
                      <label>Anexo</label>
                      <input type="text" id="editaranexoCompra" name="editaranexoCompra" class="form-control" readonly value="3">
                    </div>
                  </div>

              </div>
  
        </div>



        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-dark pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar compra</button>
          <?php
            $editarCompra = new ControladorFacturas();
            $editarCompra -> ctrEditarCompra();
          ?> 
        </div>

      </form>
    </div>
      
    </div>

    </div>

  </div>

</div>

<!--=====================================
MODAL CREAR FOLO DE COMPRAS
======================================-->

<div id="modalCrearFolioCompras" class="modal" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">
      <form role="form" method="get" action="extensiones/TCPDF-main/examples/imprimir-folio-compras.php?" enctype="multipart/form-data">
        <input type="hidden" name="tipoFolio" value="todos">
        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:grey; color:white">
          <h4 class="modal-title">Filtros de folios</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">
            
            
          <!-- ENTRADA PARA EL CORREO-->

          <div class="form-group">
            Seleccionar mes y año:
            <div class="input-group mb-3">
                  <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                  </div>
                  <input type="month" class="form-control" id="nuevaFechaFolio" name="nuevaFechaFolio" required>
            </div>
          </div>  


        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-dark pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Generar folio</button>
        </div>

      </form>
    </div>
      
    </div>

    </div>

  </div>

</div>
<?php

  $borrarCompra = new ControladorFacturas();
  $borrarCompra -> ctrBorrarCompra();

?>
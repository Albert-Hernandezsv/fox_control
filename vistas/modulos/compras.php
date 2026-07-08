<?php

  if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Facturación" || $_SESSION["rol"] == "Contabilidad" || $_SESSION["rol"] == "Bodega" ){
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
      
      Sistema de compras - F07
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio </a></li>
      
      <li class="active">&nbsp;Sistema de facturación</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-body">
        <h3>Ingresar facturas de compras y gastos</h3>
        <button class="btn btn-primary" onclick="location.href='ordenes-compra'">Ordenes de compras</button>
        <button class="btn btn-success" onclick="location.href='index.php?ruta=ver-compras&filtroFactura=todos&filtroFechaInicio=&filtroFechaFin=<?php echo date('Y-m-d'); ?>'"> Ver compras registradas</button>


        <p style="color: red; font-size: 15px">¡Importante! Siempre revisa tus documentos registrados - Qwerty Systems no es responsable de un mal manejo de las herramientas</p>
        <h1 class="text-center">Registro de Compras</h1>
        <button id="addRow" class="btn btn-warning mb-3">Agregar Registro</button>
        <form id="registroComprasForm" method="POST">
          <style>
              /* Reducir el tamaño de la fuente para la tabla */
              #registroComprasTable {
                  font-size: 9px;
              }

              /* Scroll horizontal para dispositivos muy pequeños */
              .table-responsive {
                  overflow-x: auto;
                  -webkit-overflow-scrolling: touch;
              }

              .form-control {
                  font-size: 9px; /* Tamaño de fuente más pequeño */
              }

              .select {
                  font-size: 9px; /* Tamaño de fuente más pequeño */
                  max-width: 500px !important; /* Ajusta según tu necesidad */
                  width: auto !important;
              }
          </style>

            <div class="table-responsive">
              <table class="table table-bordered" id="registroComprasTable">
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
                  </tbody>
              </table>
            </div>
            <br>
            <button type="submit" class="btn btn-success">Guardar Registros</button>
        </form>
        <?php

          $IngresarCompra = new ControladorFacturas();
          $IngresarCompra -> ctrCrearCompra();

        ?> 

        <script>
            $(document).ready(function() {
                // Función para agregar una nueva fila
                $('#addRow').click(function() {
                  const newRow = `
                      <tr>
                          <td><input type="date" name="fecha[]" class="form-control w-75"></td>
                          <td><select name="clase_documento[]" class="form-control select" required>
                                  <option value="" disabled selected>Seleccione una opción</option>
                                  <option value="1">IMPRESO POR IMPRENTA O TICKET</option>
                                  <option value="2">FORMULARIO ÚNICO</option>
                                  <option value="3">OTROS</option>
                                  <option value="4">DTE</option>
                              </select>
                          <td><select name="tipo_documento[]" class="form-control select" required>
                                  <option value="" disabled selected>Seleccione una opción</option>
                                  <option value="03">CCF</option>
                                  <option value="05">NOTA DE CRÉDITO</option>
                                  <option value="06">NOTA DE DÉBITO</option>
                                  <option value="11">FACTURA DE EXPORTACIÓN</option>
                                  <option value="12">DECLARACIÓN DE MERCANCÍA</option>
                                  <option value="13">MANDAMIENTO DE INGRESO</option>
                              </select>
                          </td>
                          <td><input type="text" name="numero_documento[]" class="form-control select" required></td>
                          <td><input type="text" name="nit_nrc[]" class="form-control select" required oninput="fetchProviderName(this)"></td>
                          <td><input type="text" name="nombre_proveedor[]" class="form-control select" required></td>

                          <td><input type="text" name="compras_internas_exentas[]" class="form-control select" value="0.00" oninput="calculateTotal()"></td>
                          <td><input type="text" name="internaciones_exentas_y_no_sujetas[]" class="form-control select" value="0.00" oninput="calculateTotal()"></td>
                          <td><input type="text" name="importaciones_exentas_y_no_sujetas[]" class="form-control select" value="0.00" oninput="calculateTotal()"></td>
                          <td><input type="text" name="compras_internas_gravadas[]" class="form-control select" value="0.00" oninput="calculateFiscalCredit(); calculateTotal()"></td>
                          <td><input type="text" name="internaciones_gravadas_de_bienes[]" class="form-control select" value="0.00" oninput="calculateFiscalCredit(); calculateTotal()"></td>
                          <td><input type="text" name="importaciones_gravadas_de_bienes[]" class="form-control select" value="0.00" oninput="calculateFiscalCredit(); calculateTotal()"></td>
                          <td><input type="text" name="importaciones_gravadas_de_servicios[]" class="form-control select" value="0.00" oninput="calculateFiscalCredit(); calculateTotal()"></td>

                          <td><input type="text" name="credito_fiscal[]" class="form-control select" value="0.00" readonly></td>

                          <td><input type="text" name="total_de_compras[]" class="form-control select" value="0.00" readonly></td>

                          <td><input type="text" name="dui_del_proveedor[]" class="form-control select"></td>
                          <td><select name="tipo_de_operacion[]" class="form-control select" required>
                                  <option value="" disabled selected>Seleccione una opción</option>
                                  <option value="1">GRAVADA</option>
                                  <option value="2">NO GRAVADA O EXENTA</option>
                                  <option value="3">EXCLUIDO O NO CONSTITUYE RENTA</option>
                                  <option value="4">MIXTA</option>
                                  <option value="9">EXCEPCIONES</option>
                                  <option value="0">ANTES DEL PRESENTE AÑO FISCAL</option>
                              </select>
                          </td>
                          <td><select name="clasificacion[]" class="form-control select" required>
                                  <option value="" disabled selected>Seleccione una opción</option>
                                  <option value="1">COSTO</option>
                                  <option value="2">GASTO</option>
                                  <option value="9">EXCEPCIONES</option>
                                  <option value="0">ANTES DEL PRESENTE AÑO FISCAL</option>
                              </select>
                          </td>
                          <td><select name="sector[]" class="form-control select" required>
                                  <option value="" disabled selected>Seleccione una opción</option>
                                  <option value="1">INDUSTRIA</option>
                                  <option value="2">COMERCIO</option>
                                  <option value="3">AGROPECUARIA</option>
                                  <option value="4">SERVICIOS PROFESIONALES</option>
                                  <option value="9">EXCEPCIONES</option>
                                  <option value="0">ANTES DEL PRESENTE AÑO FISCAL</option>
                              </select>
                          <td><select name="tipo[]" class="form-control select" required>
                                  <option value="" disabled selected>Seleccione una opción</option>
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
                          </td>
                          <td><input type="text" name="anexo[]" class="form-control select" value="3" readonly></td>
                          <td><button type="button" class="btn btn-danger removeRow">Eliminar</button></td>
                      </tr>`;
                  $('#registroComprasTable tbody').append(newRow);
                });


                // Función para eliminar una fila
                $(document).on('click', '.removeRow', function() {
                    $(this).closest('tr').remove();
                });
            });

            function fetchProviderName(input) {
              
                var idProveedorM = input.value.trim(); // Obtener el NIT ingresado
      
                var datos = new FormData();
                datos.append("idProveedorM", idProveedorM);
                

                // Realizar la solicitud AJAX
                $.ajax({
                  url:"ajax/clientes.ajax.php",
                  method: "POST",
                  data: datos,
                  cache: false,
                  contentType: false,
                  processData: false,
                  dataType: "json",
                  success: function(response) {
                    // Asignar el nombre del proveedor al campo correspondiente
                    $(input).closest('tr').find('input[name="nombre_proveedor[]"]').val(response["nombre"]);
                  },
                  error: function() {
                    
                  }
                });
            }

            function calculateFiscalCredit() {
                // Iterar sobre todas las filas de la tabla
                $('#registroComprasTable tbody tr').each(function() {
                    // Obtener los valores de los campos de compras e importaciones de la fila actual
                    var comprasInternas = parseFloat($(this).find('input[name="compras_internas_gravadas[]"]').val()) || 0;
                    var internacionesBienes = parseFloat($(this).find('input[name="internaciones_gravadas_de_bienes[]"]').val()) || 0;
                    var importacionesBienes = parseFloat($(this).find('input[name="importaciones_gravadas_de_bienes[]"]').val()) || 0;
                    var importacionesServicios = parseFloat($(this).find('input[name="importaciones_gravadas_de_servicios[]"]').val()) || 0;

                    // Sumar todos los valores
                    var total = comprasInternas + internacionesBienes + importacionesBienes + importacionesServicios;

                    // Multiplicar el total por 0.13 (13%)
                    var creditoFiscal = total * 0.13;

                    // Actualizar el campo "credito_fiscal" de la fila actual
                    $(this).find('input[name="credito_fiscal[]"]').val(creditoFiscal.toFixed(2)); // Mostrar con 2 decimales
                });
            }


            function calculateTotal() {
                // Recorrer cada fila de la tabla
                $('#registroComprasTable tbody tr').each(function() {
                    // Obtener los valores de los campos dentro de la fila actual
                    var comprasInternasExentas = parseFloat($(this).find('input[name="compras_internas_exentas[]"]').val()) || 0;
                    var internacionesExentas = parseFloat($(this).find('input[name="internaciones_exentas_y_no_sujetas[]"]').val()) || 0;
                    var importacionesExentas = parseFloat($(this).find('input[name="importaciones_exentas_y_no_sujetas[]"]').val()) || 0;
                    var comprasInternasGravadas = parseFloat($(this).find('input[name="compras_internas_gravadas[]"]').val()) || 0;
                    var internacionesGravadasBienes = parseFloat($(this).find('input[name="internaciones_gravadas_de_bienes[]"]').val()) || 0;
                    var importacionesGravadasBienes = parseFloat($(this).find('input[name="importaciones_gravadas_de_bienes[]"]').val()) || 0;
                    var importacionesGravadasServicios = parseFloat($(this).find('input[name="importaciones_gravadas_de_servicios[]"]').val()) || 0;

                    // Sumar todos los valores de la fila
                    var total = comprasInternasExentas + internacionesExentas + importacionesExentas + comprasInternasGravadas + internacionesGravadasBienes + importacionesGravadasBienes + importacionesGravadasServicios;

                    // Mostrar el resultado en el campo "total_de_compras" de la fila actual
                    $(this).find('input[name="total_de_compras[]"]').val(total.toFixed(2)); // Mostrar con 2 decimales
                });
            }



        </script>

      </div>

    </div>

  </section>

</div>
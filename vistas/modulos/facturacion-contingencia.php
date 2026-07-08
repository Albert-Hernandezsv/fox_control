<?php

  if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Facturación" || $_SESSION["rol"] == "Contabilidad"){
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
    
    <h1>
      
      Sistema de facturación - modo contingencia
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio </a></li>
      
      <li class="active">&nbsp;Sistema de facturación</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">

        <button class="btn btn-warning" onclick="location.href='facturacion'">
          
          Regresar a facturación normal

        </button>
        <button class="btn btn-secondary" data-toggle="modal" data-target="#modalVerClientes">
          
          Ver clientes registrados - crear factura

        </button>

        <button class="btn btn-info" data-toggle="modal" data-target="#modalInstruccionesContingencia">
          
          Pasos de una facturación en contingencia

        </button>

        <?php

            if($_SESSION["tokenInicioSesionMh"] == ""){
                echo '<button class="btn btn-danger btnIniciarSesionMh">
          
                          Iniciar sesión en Ministerio de Hacienda

                    </button>';
            } else {
              echo '<button class="btn btn-success">
          
                    Inicio de sesión MH correcto

                  </button>';
            }

        ?>
<br><br>
      </div>

      <div class="box-body">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrearEventoContingencia">
          Crear evento de contingencia
        </button>
        <br><br>
        
       <table class="table table-bordered table-striped dt-responsive tablas" width="100%" style="font-size: 80%">
         
        <thead>
         
         <tr>
           
           <th style="width:10px">#</th>
           <th style="width:200px">Cliente</th>
           <th style="width:200px">Número de control</th>
           <th style="width:200px">Código de generación</th>
           <th style="width:20px !important">Firma digital</th>
           <th style="width:50px !important">Sellado por hacienda</th>
           <th>Tipo de factura</th>
           <th>Modo</th>
           <th>Monto total</th>
           <th>Estado</th>
           <th>Fecha</th>
           <th>Acciones</th>

         </tr> 

        </thead>

        <tbody>

        <?php

        $item = null;
        $valor = null;
        $orden = "fecEmi";
        $optimizacion = "no";

        $facturas = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        foreach ($facturas as $key => $value){
          if($value["modo"] == "Contingencia"){
            if($value["tipoDte"] == "05" || $value["tipoDte"] == "06"){
            
            } else {
              $item = "id";
                      $valor = $value["id_cliente"];
                      $orden = "id";
  
                      $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);
                      // Suponiendo que $value["fecha"] tiene el valor '2024-10-19 22:36:44'
                      $fechaOriginal = new DateTime($value["fecEmi"]);
                      $fechaFormateada = $fechaOriginal->format('d \d\e F \d\e Y'); // Formato deseado
                      $firmaDigi = "";
                      $sello = "";
                      if($value["firmaDigital"] == ""){
                        $firmaDigi = "No";
                      } else {
                        $firmaDigi = "Si";
                      }
  
                      if($value["sello"] ===  ""){
                        $sello = "No";
                      } else {
                        $sello = "Si";
                      }
  
                      $tipoFacturaTexto = "";
                      switch ($value["tipoDte"]) {
                        case "01":
                            $tipoFacturaTexto = "Factura";
                            break;
                        case "03":
                            $tipoFacturaTexto = "Comprobante de crédito fiscal";
                            break;
                        case "04":
                            $tipoFacturaTexto = "Nota de remisión";
                            break;
                        case "05":
                            $tipoFacturaTexto = "Nota de crédito";
                            break;
                        case "06":
                            $tipoFacturaTexto = "Nota de débito";
                            break;
                        case "07":
                            $tipoFacturaTexto = "Comprobante de retención";
                            break;
                        case "08":
                            $tipoFacturaTexto = "Comprobante de liquidación";
                            break;
                        case "09":
                            $tipoFacturaTexto = "Documento contable de liquidación";
                            break;
                        case "11":
                            $tipoFacturaTexto = "Factura de exportación";
                            break;
                        case "14":
                            $tipoFacturaTexto = "Factura de sujeto excluido";
                            break;
                        case "15":
                            $tipoFacturaTexto = "Comprobante de donación";
                            break;
            
                        default:
                            $tipoFacturaTexto = "Factura no válida";
                            break;
                    }
  
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
  
                    $totalF = "";
                    if($cliente["tipo_cliente"] ==  "00" && $value["tipoDte"] == "01"){
                      $totalF = $value["total"];
                    }
                    if($cliente["tipo_cliente"] ==  "00" && $value["tipoDte"] == "03"){
                      $totalF = $value["total"];
                    }
                    if($cliente["tipo_cliente"] ==  "00" && $value["tipoDte"] == "11"){
                      $totalF = $value["total"];
                    }
                    if($cliente["tipo_cliente"] ==  "00" && $value["tipoDte"] == "14"){
                      $totalF = $value["total"];
                    }
  
                    if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "01"){
                      $totalF = $value["total"];
                    }
                    if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "03"){
                      $totalF = $value["total"];
                    }
                    if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "11"){
                      $totalF = $value["totalSinIva"];
                    }
  
                    if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "01"){
                      $totalF = $value["totalSinIva"];
                    }
                    if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "03"){
                      $totalF = $value["totalSinIva"];
                    }
                    if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "11"){
                      $totalF = $value["totalSinIva"];
                    }
  
                    if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "01"){
                      $totalF = $value["totalSinIva"];
                    }
                    if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "03"){
                      $totalF = $value["totalSinIva"];
                    }
                    if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "11"){
                      $totalF = $value["totalSinIva"];
                    }
  
                    if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "05"){
                      $totalF = $value["total"];
                    }
                    if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "05"){
                      $totalF = $value["totalSinIva"];
                    }
                    if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "05"){
                      $totalF = $value["totalSinIva"];
                    }
  
                    if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "04"){
                      $totalF = $value["total"];
                    }
                    if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "04"){
                      $totalF = $value["totalSinIva"];
                    }
                    if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "04"){
                      $totalF = $value["totalSinIva"];
                    }
  
                    if($_SESSION["rol"] == "Admin"){
                          echo ' <tr>
                          <td>'.($key+1).'</td>
                          <td>'.$cliente["nombre"].' - '.$tipo.'</td>
                          <td>'.$value["numeroControl"].'</td>
                          <td>'.$value["codigoGeneracion"].'</td>
                          <td>'.$firmaDigi.'</td>
                          <td>'.$sello.'</td>
                          <td>'.$tipoFacturaTexto.'</td>
                          <td>'.$value["modo"].'</td>
                          <td>$'.$totalF.'</td>
                          <td>'.$value["estado"].'</td>
                          <td>'.$fechaFormateada.'</td>
          
                          <td>
      
                            <div class="btn-group">
                              
                              <button class="btn btn-warning btnVerFactura" idFactura="'.$value["id"].'"><i class="fa fa-eye"></i></button>';
                              date_default_timezone_set('America/El_Salvador');

                              // Fecha y hora de emisión
                              $fecEmi = $value["fecEmi"] . ' ' . $value["horEmi"];
                              
                              // Obtener la fecha y hora actual
                              $fechaActual = new DateTime(); // Fecha y hora actual
                              
                              // Crear un objeto DateTime con la fecha de emisión
                              $fechaEmision = new DateTime($fecEmi);
                              
                              // Clonar la fecha de emisión y sumar 3 meses (para la primera validación)
                              $fechaLimiteTresMeses = clone $fechaEmision;
                              $fechaLimiteTresMeses->modify('+3 months')->setTime(23, 59, 59); // 3 meses después a las 23:59:59
                              
                              // Clonar la fecha de emisión y agregar 1 día (para la segunda validación)
                              $fechaLimiteUnDia = clone $fechaEmision;
                              $fechaLimiteUnDia->modify('+1 day')->setTime(23, 59, 59); // Día siguiente a las 23:59:59
                              
                              // Verificar si la fecha actual es anterior a 3 meses
                              if ($fechaActual <= $fechaLimiteTresMeses) {
                                  // Aún no han pasado 3 meses
                                  // Verificar si la fecha actual coincide con el día que mh da
                                  if ($fechaActual <= $fechaLimiteUnDia) {
                                  // Ejecutar la acción si la fecha coincide
                                  
                                  if($value["firmaDigital"] === ""){
                                    echo '<button class="btn btn-info btnFirmarDteContingencia" idFactura="'.$value["id"].'" ><i class="fa fa-pencil-square-o"></i></button>';
                                    echo '<button class="btn btn-danger btnEliminarFactura" idFactura="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                                  } else {
                                      if($value["sello"] === ""){
                                          echo '<button class="btn btn-success btnSellarDteContingencia" idFactura="'.$value["id"].'" ><i class="fa fa-arrow-circle-right"></i></button>';
                                          echo '<button class="btn btn-danger btnEliminarFactura" idFactura="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                                      } else {
                                        if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
                                          if($value["estado"] != "Anulada"){
                                            echo '<button class="btn btn-danger btnEliminarFacturaHacienda" idFactura="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                                          }
                                        }
                                      }
                                      
                                  }
                                  if($value["sello"] != "" && $value["estado"] != "Anulada"){
      
                                    if($value["tipoDte"] == "03") {
                                      if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
                                        echo '<button class="btn btn-info btnNotaCredito" idFactura="'.$value["id"].'">NC</button>';
                                        echo '<button class="btn btn-success btnNotaDebito" idFactura="'.$value["id"].'">ND</button>';
                                      }
                                    }
                                    
                                  }
                                } else {
                                    if($value["sello"] != "" && $value["estado"] != "Anulada"){
      
                                      if($value["tipoDte"] == "03") {
                                        if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
                                          echo '<button class="btn btn-info btnNotaCredito" idFactura="'.$value["id"].'">NC</button>';
                                          echo '<button class="btn btn-success btnNotaDebito" idFactura="'.$value["id"].'">ND</button>';
                                          echo '<button class="btn btn-dark btnNotaRemision" idFactura="'.$value["id"].'">NR</button>';
                                        }
                                      }
                                        if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
                                          if($value["estado"] != "Anulada" && ($value["tipoDte"] == "01" || $value["tipoDte"] == "11")){
                                            echo '<button class="btn btn-danger btnEliminarFacturaHacienda" idFactura="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                                          }
                                        }                   
                                      
                                    } else {
                                      if($value["estado"] != "Anulada"){
                                        echo '<button class="btn btn-danger btnEliminarFactura" idFactura="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                                      }
                                    }
                              }
                              } else {
                                  // Han pasado más de 3 meses
                                  
                              }
      
                      echo '</div>  
      
                          </td>
      
                        </tr>';
                    } else if($value["id_usuario"] == $_SESSION["id"]){
                        echo ' <tr>
                        <td>'.($key+1).'</td>
                        <td>'.$cliente["nombre"].' - '.$tipo.'</td>
                        <td>'.$value["numeroControl"].'</td>
                        <td>'.$value["codigoGeneracion"].'</td>
                        <td>'.$firmaDigi.'</td>
                        <td>'.$sello.'</td>
                        <td>'.$tipoFacturaTexto.'</td>
                        <td>'.$value["modo"].'</td>
                        <td>$'.$totalF.'</td>
                        <td>'.$value["estado"].'</td>
                        <td>'.$fechaFormateada.'</td>
        
                        <td>
    
                          <div class="btn-group">
                            
                            <button class="btn btn-warning btnVerFactura" idFactura="'.$value["id"].'"><i class="fa fa-eye"></i></button>';
                            date_default_timezone_set('America/El_Salvador');

                            // Fecha y hora de emisión
                            $fecEmi = $value["fecEmi"] . ' ' . $value["horEmi"];
                            
                            // Obtener la fecha y hora actual
                            $fechaActual = new DateTime(); // Fecha y hora actual
                            
                            // Crear un objeto DateTime con la fecha de emisión
                            $fechaEmision = new DateTime($fecEmi);
                            
                            // Clonar la fecha de emisión y sumar 3 meses (para la primera validación)
                            $fechaLimiteTresMeses = clone $fechaEmision;
                            $fechaLimiteTresMeses->modify('+3 months')->setTime(23, 59, 59); // 3 meses después a las 23:59:59
                            
                            // Clonar la fecha de emisión y agregar 1 día (para la segunda validación)
                            $fechaLimiteUnDia = clone $fechaEmision;
                            $fechaLimiteUnDia->modify('+1 day')->setTime(23, 59, 59); // Día siguiente a las 23:59:59
                            
                            // Verificar si la fecha actual es anterior a 3 meses
                            if ($fechaActual <= $fechaLimiteTresMeses) {
                                // Aún no han pasado 3 meses
                                // Verificar si la fecha actual coincide con el día que mh da
                                if ($fechaActual <= $fechaLimiteUnDia) {
                                // Ejecutar la acción si la fecha coincide
                                
                                if($value["firmaDigital"] === ""){
                                  echo '<button class="btn btn-info btnFirmarDteContingencia" idFactura="'.$value["id"].'" ><i class="fa fa-pencil-square-o"></i></button>';
                                  echo '<button class="btn btn-danger btnEliminarFactura" idFactura="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                                } else {
                                    if($value["sello"] === ""){
                                        echo '<button class="btn btn-success btnSellarDteContingencia" idFactura="'.$value["id"].'" ><i class="fa fa-arrow-circle-right"></i></button>';
                                        echo '<button class="btn btn-danger btnEliminarFactura" idFactura="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                                    } else {
                                      if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
                                        if($value["estado"] != "Anulada"){
                                          echo '<button class="btn btn-danger btnEliminarFacturaHacienda" idFactura="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                                        }
                                      }
                                    }
                                    
                                }
                                if($value["sello"] != "" && $value["estado"] != "Anulada"){
    
                                  if($value["tipoDte"] == "03") {
                                    if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
                                      echo '<button class="btn btn-info btnNotaCredito" idFactura="'.$value["id"].'">NC</button>';
                                      echo '<button class="btn btn-success btnNotaDebito" idFactura="'.$value["id"].'">ND</button>';
                                    }
                                  }
                                  
                                }
                              } else {
                                  if($value["sello"] != "" && $value["estado"] != "Anulada"){
    
                                    if($value["tipoDte"] == "03") {
                                      if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
                                        echo '<button class="btn btn-info btnNotaCredito" idFactura="'.$value["id"].'">NC</button>';
                                        echo '<button class="btn btn-success btnNotaDebito" idFactura="'.$value["id"].'">ND</button>';
                                        echo '<button class="btn btn-dark btnNotaRemision" idFactura="'.$value["id"].'">NR</button>';
                                      }
                                    }
                                      if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
                                        if($value["estado"] != "Anulada" && ($value["tipoDte"] == "01" || $value["tipoDte"] == "11")){
                                          echo '<button class="btn btn-danger btnEliminarFacturaHacienda" idFactura="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                                        }
                                      }                   
                                    
                                  } else {
                                    if($value["estado"] != "Anulada"){
                                      echo '<button class="btn btn-danger btnEliminarFactura" idFactura="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                                    }
                                  }
                            }
                            } else {
                                // Han pasado más de 3 meses
                                
                            }
    
                    echo '</div>  
    
                        </td>
    
                      </tr>';
                    }
                    
  
              
            }
          }
        }


        ?> 

        </tbody>

       </table>




       <table class="table table-bordered table-striped dt-responsive tablas" width="100%" style="font-size: 80%">
         
                <thead>
                
                <tr>
                  
                  <th style="width:10px">#</th>
                  <th style="width:100px">Código de generación</th>
                  <th style="width:80px">Fecha de inicio</th>
                  <th style="width:80px">Fecha de finalización</th>
                  <th>Hora de inicio</th>
                  <th style="width:80px">Hora de finalización</th>
                  <th style="width:100px">Tipo contingencia</th>
                  <th>Motivo</th>
                  <th>Facturas</th>
                  <th>Firma digital</th>
                  <th>Sello de hacienda</th>
                  <th>Acciones</th>

                </tr> 

                </thead>

                <tbody>

                <?php

                  $item = null;
                  $valor = null;
                  $orden = "fecEmi";

                  $facturas = ControladorFacturas::ctrMostrarEventosContingencias($item, $valor, $orden);
                  
                  foreach ($facturas as $key => $value){
                    $ids = json_decode($value["ids_facturas"], true);
                    $codigosFacturas = "";

                    foreach ($ids as $item) {
                      
                      $itemf = "id";
                      $valor = $item;
                      $orden = "fecEmi";
                      $optimizacion = "no";

                      $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($itemf, $valor, $orden, $optimizacion);
                      $codigosFacturas .= $facturaOriginal["numeroControl"]."<br>";
                      
                    }

                                
                    $firmaDigi = "";
                    $sello = "";
                    $tipoCon = "";

                    if($value["firmaDigital"] == ""){
                      $firmaDigi = "No";
                    } else {
                      $firmaDigi = "Si";
                    }

                    if($value["sello"] ===  ""){
                      $sello = "No";
                    } else {
                      $sello = "Si";
                    }

                    if($value["tipo_contingencia"] ===  "1"){
                      $tipoCon = "No disponibilidad de sistema del MH";
                    }
                    if($value["tipo_contingencia"] ===  "2"){
                      $tipoCon = "No disponibilidad del sistema emisor";
                    }
                    if($value["tipo_contingencia"] ===  "3"){
                      $tipoCon = "Falla en el suministro de servicio de internet del emisor";
                    }
                    if($value["tipo_contingencia"] ===  "4"){
                      $tipoCon = "Falla en el suministro de servicio de energía eléctrica del emisor";
                    }

                  $tipo = "";
                                          
            
                        echo ' <tr>
                                <td>'.($key+1).'</td>
                                <td>'.$value["codigoGeneracion"].'</td>
                                <td>'.$value["fecha_inicio"].'</td>
                                <td>'.$value["fecha_fin"].'</td>
                                <td>'.$value["hora_inicio"].'</td>
                                <td>'.$value["hora_fin"].'</td>
                                <td>'.$tipoCon.'</td>
                                <td>'.$value["motivo_contingencia"].'</td>
                                <td>'.$codigosFacturas.'</td>
                                <td>'.$firmaDigi.'</td>
                                <td>'.$sello.'</td>
                                <td>

                                    <div class="btn-group">';
                                      // Ejecutar la acción si la fecha coincide
                          
                                      if($value["firmaDigital"] === ""){
                                        echo '<button class="btn btn-info btnFirmarEventoContingencia" idEvento="'.$value["id"].'" ><i class="fa fa-pencil-square-o"></i></button>';
                                        echo '<button class="btn btn-danger btnEliminarEventoContingencia" idEvento="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                                      } else {
                                          if($value["sello"] === ""){
                                              echo '<button class="btn btn-success btnSellarEventoContingencia" idEvento="'.$value["id"].'" ><i class="fa fa-arrow-circle-right"></i></button>';
                                              echo '<button class="btn btn-danger btnEliminarEventoContingencia" idEvento="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                                          } else {
                                            echo "Firma y envía las facturas a hacienda ahora";
                                          }
                                          
                                      }                                          

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



<!--=====================================
MODAL VER CLIENTES
======================================-->

<div id="modalVerClientes" class="modal fade bd-example-modal-lg" role="dialog" style="width: 100% !important; font-size:80%">
  
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
             <!-- Añadir el contenedor responsivo -->
             <div class="table-responsive">
                <table class="table table-bordered table-striped dt-responsive tablas" width="100%">
              
                    <thead>
                      
                      <tr>
                        
                        <th style="width:10px">#</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>NIT</th>
                        <th>NRC</th>
                        <th>Tipo</th>
                        <th>País de envío</th>
                        <th>Tipo de persona</th>
                        <th>Acciones</th>
            
                      </tr> 
            
                    </thead>
            
                    <tbody>
            
                    <?php
            
                    $item = null;
                    $valor = null;
                    $orden = "id";
            
                    $clientes = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);
            
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
                              <td>'.$value["nombre"].'</td>
                              <td>'.$value["correo"].'</td>
                              <td>'.$value["telefono"].'</td>
                              <td>'.$value["departamento"].', '.$value["municipio"].', '.$value["direccion"].'</td>
                              <td>'.$value["NIT"].'</td>
                              <td>'.$value["NRC"].'</td>
                              <td>'.$tipo.'</td>
                              <td>'.$value["nombrePais"].'</td>
                              <td>'.$tipoPersona.'</td>';
                              
                                        
                              echo '
                              <td>
            
                                <div class="btn-group">
                                    
                                  <button class="btn btn-warning btnEditarCliente" idCliente="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarCliente"><i class="fa fa-pencil"></i></button>
            
                                  <button class="btn btn-danger btnEliminarCliente" idCliente="'.$value["id"].'"><i class="fa fa-times"></i></button>';

                                  $conectado = @fsockopen("www.google.com", 80); 
                                    if ($conectado) {
                                        if($_SESSION["tokenInicioSesionMh"] == ""){
                                          echo '<button class="btn btn-info btnEscogerFacturaContingencia" idCliente="'.$value["id"].'"><i class="fa fa-file-text"></i></button>';
                                        } else {
                                          echo '<button class="btn btn-info btnEscogerFacturaContingencia" idCliente="'.$value["id"].'"><i class="fa fa-file-text"></i></button>';
                                        }
                                    } else {
                                        echo '<button class="btn btn-info btnEscogerFacturaContingencia" idCliente="'.$value["id"].'"><i class="fa fa-file-text"></i></button>';
                                    }
            
                                echo '</div>  
            
                              </td>
            
                            </tr>';
                    }
            
            
                    ?> 
            
                    </tbody>

              </table>
            </div>
            
            

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

<!--=====================================
MODAL VER INSTRUCCIONES CONTINGENCIA
======================================-->

<div id="modalInstruccionesContingencia" class="modal fade bd-example-modal-lg" role="dialog" style="width: 100% !important; font-size:80%">
  
  <div class="modal-dialog">

    <div class="modal-content">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:grey; color:white">
          <h4 class="modal-title">Pasos para una facturacion en contingencia</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">
             <h5>- Crea las facturas como siempre, pero indicando el motivo y la descripción de la contingencia SIN FIRMAR NI SELLAR.</h5>
             <h5>- Cuando termine el periodo de contingencia (o sea ya puedas facturar normalmente) crea el evento de contingencia, específica los datos ahí solicitados, firma y sella el evento.</h5>
             <h5>- Cuando el evento de contingencia ya esté sellado sella y firma cada una de las facturas realizadas en el periodo de contingencia.</h5>
            
            

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

<!--=====================================
MODAL CREAR EVENTO DE CONTINGENCIA
======================================-->

<div id="modalCrearEventoContingencia" class="modal fade" role="dialog">
  
  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:grey; color:white">
          <h4 class="modal-title">Crear evento de contingencia</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <div class="row">

              <div class="col-xl-6 col-xs-12">
                    <!-- ENTRADA PARA LA FECHA DE INICIO DEL EVENTO DE CONTINGENCIA -->            
                    <div class="form-group">
                        <p>Fecha de inicio del evento de contingencia</p>
                      <div class="input-group mb-3">
                        
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="date" class="form-control" name="nuevaFechaInicio" id="nuevaFechaInicio" required>
                      </div>
                    </div>
              </div>

              <div class="col-xl-6 col-xs-12">
                    <!-- ENTRADA PARA LA FECHA DE INICIO DEL EVENTO DE CONTINGENCIA -->            
                    <div class="form-group">
                        <p>Fecha de finalización del evento de contingencia</p>
                      <div class="input-group mb-3">
                        
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="date" class="form-control" name="nuevaFechaFin" id="nuevaFechaFin" required>
                      </div>
                    </div>
              </div>

            </div>

            <div class="row">

              <div class="col-xl-6 col-xs-12">
                    <!-- ENTRADA PARA LA HORA DE FIN DEL EVENTO DE CONTINGENCIA -->            
                    <div class="form-group">
                        <p>Hora de inicio del evento de contingencia</p>
                      <div class="input-group mb-3">
                        
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="time" class="form-control" name="nuevaHoraInicio" id="nuevaHoraInicio" required>
                      </div>
                    </div>
              </div>

              <div class="col-xl-6 col-xs-12">
                    <!-- ENTRADA PARA LA HORA DE INICIO DEL EVENTO DE CONTINGENCIA -->            
                    <div class="form-group">
                        <p>Hora de finalización del evento de contingencia</p>
                      <div class="input-group mb-3">
                        
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="time" class="form-control" name="nuevaHoraFin" id="nuevaHoraFin" required>
                      </div>
                    </div>
              </div>

            </div>
            
            <div class="row">

              <div class="col-xl-6 col-xs-12">
                    <!-- ENTRADA PARA LA CONDICIÓN DE CONTINGENCIA -->
                    <div class="form-group">
                        <p>Tipo de contingencia:</p>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                            </div>
                            <select name="tipoContingencia" id="tipoContingenciaEvento" class="form-control" required>
                                <option value="" disabled selected>Seleccione una opción</option>
                                <option value="1">No disponibilidad de sistema del MH</option>
                                <option value="2">No disponibilidad de sistema del emisor</option>
                                <option value="3">Falla en el suministro de servicio de Internet del Emisor</option>
                                <option value="4">Falla en el suministro de servicio de energía eléctrica del emisor que impida la transmisión de los DTE</option>
                            </select>
                        </div>
                    </div>

              </div>

              <div class="col-xl-6 col-xs-12">

                    <!-- ENTRADA PARA EL MOTIVO DE CONTINGENCIA -->
                    <div class="form-group">
                        <p>Motivo de la contingencia:</p>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                            </div>
                            <input type="text" name="motivoContingencia" id="motivoContingenciaEvento" class="form-control" placeholder="Motivo de la contingencia" required>                       
                        </div>
                    </div>

              </div>

            </div>
                    <br>
            <h5>Facturas a ingresar en el lote:</h5>
            <h6>Nota: se agregará automáticamente todas las facturas del modo contingencia que no tengan sello de Hacienda</h6>
            <br>
            
            <table class="table table-bordered table-striped dt-responsive tablas" width="100%" style="font-size: 80%">
         
                <thead>
                
                <tr>
                  
                  <th style="width:10px">#</th>
                  <th style="width:200px">Número de control</th>
                  <th style="width:200px">Código de generación</th>
                  <th>Monto total</th>
                  <th>Fecha</th>

                </tr> 

                </thead>

                <tbody>

                <?php

                  $item = "modo";
                  $valor = "contingencia";
                  $orden = "fecEmi";
                  $optimizacion = "no";

                  $facturas = ControladorFacturas::ctrMostrarFacturasVarias($item, $valor, $orden, $optimizacion);
                    if($facturas){
                      foreach ($facturas as $key => $value){
                    
                        if($value["modo"] == "Contingencia" && $value["sello"] == ""){
    
                          echo '<input type="text" name="ids_facturas[]" value="' . $value['id'] . '" hidden>';
    
                          if($value["tipoDte"] == "05" || $value["tipoDte"] == "06"){
                          
                          } else {
                            $item = "id";
                                    $valor = $value["id_cliente"];
                                    $orden = "id";
                
                                    $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);
                                    // Suponiendo que $value["fecha"] tiene el valor '2024-10-19 22:36:44'
                                    $fechaOriginal = new DateTime($value["fecEmi"]);
                                    $fechaFormateada = $fechaOriginal->format('d \d\e F \d\e Y'); // Formato deseado
                                    $firmaDigi = "";
                                    $sello = "";
                                    if($value["firmaDigital"] == ""){
                                      $firmaDigi = "No";
                                    } else {
                                      $firmaDigi = "Si";
                                    }
                
                                    if($value["sello"] ===  ""){
                                      $sello = "No";
                                    } else {
                                      $sello = "Si";
                                    }
                
                                    $tipoFacturaTexto = "";
                                    switch ($value["tipoDte"]) {
                                      case "01":
                                          $tipoFacturaTexto = "Factura";
                                          break;
                                      case "03":
                                          $tipoFacturaTexto = "Comprobante de crédito fiscal";
                                          break;
                                      case "04":
                                          $tipoFacturaTexto = "Nota de remisión";
                                          break;
                                      case "05":
                                          $tipoFacturaTexto = "Nota de crédito";
                                          break;
                                      case "06":
                                          $tipoFacturaTexto = "Nota de débito";
                                          break;
                                      case "07":
                                          $tipoFacturaTexto = "Comprobante de retención";
                                          break;
                                      case "08":
                                          $tipoFacturaTexto = "Comprobante de liquidación";
                                          break;
                                      case "09":
                                          $tipoFacturaTexto = "Documento contable de liquidación";
                                          break;
                                      case "11":
                                          $tipoFacturaTexto = "Factura de exportación";
                                          break;
                                      case "14":
                                          $tipoFacturaTexto = "Factura de sujeto excluido";
                                          break;
                                      case "15":
                                          $tipoFacturaTexto = "Comprobante de donación";
                                          break;
                          
                                      default:
                                          $tipoFacturaTexto = "Factura no válida";
                                          break;
                                  }
                
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
                
                                  $totalF = "";
                                  if($cliente["tipo_cliente"] ==  "00" && $value["tipoDte"] == "01"){
                                    $totalF = $value["total"];
                                  }
                                  if($cliente["tipo_cliente"] ==  "00" && $value["tipoDte"] == "03"){
                                    $totalF = $value["total"];
                                  }
                                  if($cliente["tipo_cliente"] ==  "00" && $value["tipoDte"] == "11"){
                                    $totalF = $value["total"];
                                  }
                                  if($cliente["tipo_cliente"] ==  "00" && $value["tipoDte"] == "14"){
                                    $totalF = $value["total"];
                                  }
                
                                  if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "01"){
                                    $totalF = $value["total"];
                                  }
                                  if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "03"){
                                    $totalF = $value["total"];
                                  }
                                  if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "11"){
                                    $totalF = $value["totalSinIva"];
                                  }
                
                                  if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "01"){
                                    $totalF = $value["totalSinIva"];
                                  }
                                  if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "03"){
                                    $totalF = $value["totalSinIva"];
                                  }
                                  if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "11"){
                                    $totalF = $value["totalSinIva"];
                                  }
                
                                  if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "01"){
                                    $totalF = $value["totalSinIva"];
                                  }
                                  if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "03"){
                                    $totalF = $value["totalSinIva"];
                                  }
                                  if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "11"){
                                    $totalF = $value["totalSinIva"];
                                  }
                
                                  if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "05"){
                                    $totalF = $value["total"];
                                  }
                                  if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "05"){
                                    $totalF = $value["totalSinIva"];
                                  }
                                  if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "05"){
                                    $totalF = $value["totalSinIva"];
                                  }
                
                                  if($cliente["tipo_cliente"] ==  "01" && $value["tipoDte"] == "04"){
                                    $totalF = $value["total"];
                                  }
                                  if($cliente["tipo_cliente"] ==  "02" && $value["tipoDte"] == "04"){
                                    $totalF = $value["totalSinIva"];
                                  }
                                  if($cliente["tipo_cliente"] ==  "03" && $value["tipoDte"] == "04"){
                                    $totalF = $value["totalSinIva"];
                                  }
                
                
                                  
                
                            echo ' <tr>
                                    <td>'.($key+1).'</td>
                                    <td>'.$value["numeroControl"].'</td>
                                    <td>'.$value["codigoGeneracion"].'</td>
                                    <td>$'.$totalF.'</td>
                                    <td>'.$fechaFormateada.'</td>
                                    </tr>';
                    
                          }
                        }
                      }
                    }
                  


                ?> 

                </tbody>

            </table>
        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-dark">Crear evento</button>

        </div>

        <?php

          $crearEventoContingencia = new ControladorFacturas();
          $crearEventoContingencia -> ctrCrearEventoContingencia();

        ?>

      </form>

      </div>

    </div>

    </div>

  </div>

</div>

<!--=====================================
MODAL EDITAR CLIENTE
======================================-->

<div id="modalEditarCliente" class="modal fade" role="dialog">
  
  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:grey; color:white">
          <h4 class="modal-title">Editar cliente</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <div class="row">

                <div class="col-xl-6 col-xs-12">
                    <!-- ENTRADA PARA EL NOMBRE -->

                  <div class="form-group">

                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                    </div>
                    <input type="text" class="form-control" id="editarNombreCliente" name="editarNombreCliente" value="" required>
                    <input type="text" class="form-control" id="editarIdCliente" name="editarIdCliente" hidden>
                  </div>

                  </div>

                  <!-- ENTRADA PARA EL DEPARTAMENTO-->

                  <div class="form-group">
                  <p>Seleccionar departamento</p>
                  <div class="input-group mb-3">
                        <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <select class="form-control" name="editarDepartamentoCliente" value="" required>
                        <option value="" id="editarDepartamentoCliente"></option>
                          <option value="00">EXTRANJERO</option>
                          <option value="01">AHUACHAPAN</option>
                          <option value="02">SANTA ANA</option>
                          <option value="03">SONSONATE</option>
                          <option value="04">CHALATENANGO</option>
                          <option value="05">LA LIBERTAD</option>
                          <option value="06">SAN SALVADOR</option>
                          <option value="07">CUSCATLAN</option>
                          <option value="08">LA PAZ</option>
                          <option value="09">CABAÑAS</option>
                          <option value="10">SAN VICENTE</option>
                          <option value="11">USULUTAN</option>>
                          <option value="12">SAN MIGUEL</option>
                          <option value="13">MORAZAN</option>
                          <option value="14">LA UNION</option>
                        </select>
                  </div>

                  </div>

                  <!-- ENTRADA PARA EL MUNICIPIO-->

                  <div class="form-group">
                  <p>Seleccionar municipio</p>
                  <div class="input-group mb-3">
                        <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <select class="form-control" name="editarMunicipioCliente" value="" required>
                        <option value="" id="editarMunicipioCliente"></option>
                          <option value="00">EXTRANJERO</option>
                          <option value="13">AHUACHAPAN NORTE</option>
                          <option value="14">AHUACHAPAN CENTRO</option>
                          <option value="15">AHUACHAPAN SUR</option>
                          <option value="14">SANTA ANA NORTE</option>
                          <option value="15">SANTA ANA CENTRO</option>
                          <option value="16">SANTA ANA ESTE</option>
                          <option value="17">SANTA ANA OESTE</option>
                          <option value="17">SONSONATE NORTE</option>
                          <option value="18">SONSONATE CENTRO</option>
                          <option value="19">SONSONATE ESTE</option>
                          <option value="20">SONSONATE OESTE</option>
                          <option value="34">CHALATENANGO NORTE</option>
                          <option value="35">CHALATENANGO CENTRO</option>
                          <option value="36">CHALATENANGO SUR</option>
                          <option value="23">LA LIBERTAD NORTE</option>
                          <option value="24">LA LIBERTAD CENTRO</option>
                          <option value="25">LA LIBERTAD OESTE</option>
                          <option value="26">LA LIBERTAD ESTE</option>
                          <option value="27">LA LIBERTAD COSTA</option>
                          <option value="28">LA LIBERTAD SUR</option>
                          <option value="20">SAN SALVADOR NORTE</option>
                          <option value="21">SAN SALVADOR OESTE</option>
                          <option value="22">SAN SALVADOR ESTE</option>
                          <option value="23">SAN SALVADOR CENTRO</option>
                          <option value="24">SAN SALVADOR SUR</option>
                          <option value="17">CUSCATLAN NORTE</option>
                          <option value="18">CUSCATLAN SUR</option>
                          <option value="23">LA PAZ OESTE</option>
                          <option value="24">LA PAZ CENTRO</option>
                          <option value="25">LA PAZ ESTE</option>
                          <option value="10">CABAÑAS OESTE</option>
                          <option value="11">CABAÑAS ESTE</option>
                          <option value="14">SAN VICENTE NORTE</option>
                          <option value="15">SAN VICENTE SUR</option>
                          <option value="24">USULUTAN NORTE</option>
                          <option value="25">USULUTAN ESTE</option>
                          <option value="26">USULUTAN OESTE</option>
                          <option value="21">SAN MIGUEL NORTE</option>
                          <option value="22">SAN MIGUEL CENTRO</option>
                          <option value="23">SAN MIGUEL OESTE</option>
                          <option value="27">MORAZAN NORTE</option>
                          <option value="28">MORAZAN SUR</option>
                          <option value="19">LA UNION NORTE</option>
                          <option value="20">LA UNION SUR</option>
                        </select>
                  </div>

                  </div>

                  <!-- ENTRADA PARA LA DIRECCION -->

                  <div class="form-group">

                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                    </div>
                    <input type="text" class="form-control" id="editarDireccionCliente" name="editarDireccionCliente" value="" required>
                  </div>

                  </div>

                  <!-- ENTRADA PARA EL CORREO-->

                  <div class="form-group">

                  <div class="input-group mb-3">
                        <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-envelope"></i></span>
                        </div>
                        <input type="email" class="form-control" id="editarCorreoCliente" name="editarCorreoCliente" placeholder="Ingresar correo electrónico" required>
                  </div>

                  </div>

                  <!-- ENTRADA PARA EL NUMERO -->

                  <div class="form-group">

                      <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-phone"></i></span>
                            </div>
                            <input type="text" class="form-control" name="editarNumeroCliente" id="editarNumeroCliente" placeholder="Ingresar número telefónico" required>
                      </div>

                  </div>

                  <!-- ENTRADA PARA EL PAIS A RECIBIR EL PRODUCTO -->

                  <div class="form-group">
                    <p>Seleccionar país a recibir producto:</p>
                    <div class="input-group mb-3">
                          <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-globe"></i></span>
                          </div>
                          <select class="form-control" name="editarPaisRecibir" value="" required>
                            <option id="editarPaisRecibir" value=""></option>
                            <option value="AF">Afganistán</option>
                            <option value="AX">Aland</option>
                            <option value="AL">Albania</option>
                            <option value="DE">Alemania</option>
                            <option value="AD">Andorra</option>
                            <option value="AO">Angola</option>
                            <option value="AI">Anguila</option>
                            <option value="AQ">Antártica</option>
                            <option value="AG">Antigua y Barbuda</option>
                            <option value="AW">Aruba</option>
                            <option value="SA">Arabia Saudita</option>
                            <option value="DZ">Argelia</option>
                            <option value="AR">Argentina</option>
                            <option value="AM">Armenia</option>
                            <option value="AU">Australia</option>
                            <option value="AT">Austria</option>
                            <option value="AZ">Azerbaiyán</option>
                            <option value="BS">Bahamas</option>
                            <option value="BH">Bahrein</option>
                            <option value="BD">Bangladesh</option>
                            <option value="BB">Barbados</option>
                            <option value="BE">Bélgica</option>
                            <option value="BZ">Belice</option>
                            <option value="BJ">Benin</option>
                            <option value="BM">Bermudas</option>
                            <option value="BY">Bielorrusia</option>
                            <option value="BO">Bolivia</option>
                            <option value="BQ">Bonaire, Sint Eustatius and Saba</option>
                            <option value="BA">Bosnia-Herzegovina</option>
                            <option value="BW">Botswana</option>
                            <option value="BR">Brasil</option>
                            <option value="BN">Brunei</option>
                            <option value="BG">Bulgaria</option>
                            <option value="BF">Burkina Faso</option>
                            <option value="BI">Burundi</option>
                            <option value="BT">Bután</option>
                            <option value="CV">Cabo Verde</option>
                            <option value="KY">Caimán, Islas</option>
                            <option value="KH">Camboya</option>
                            <option value="CM">Camerún</option>
                            <option value="CA">Canadá</option>
                            <option value="CF">Centroafricana, República</option>
                            <option value="TD">Chad</option>
                            <option value="CL">Chile</option>
                            <option value="CN">China</option>
                            <option value="CY">Chipre</option>
                            <option value="VA">Ciudad del Vaticano</option>
                            <option value="CO">Colombia</option>
                            <option value="KM">Comoras</option>
                            <option value="CG">Congo</option>
                            <option value="CI">Costa de Marfil</option>
                            <option value="CR">Costa Rica</option>
                            <option value="HR">Croacia</option>
                            <option value="CU">Cuba</option>
                            <option value="CW">Curazao</option>
                            <option value="DK">Dinamarca</option>
                            <option value="DM">Dominica</option>
                            <option value="DJ">Djiboutí</option>
                            <option value="EC">Ecuador</option>
                            <option value="EG">Egipto</option>
                            <option value="SV">El Salvador</option>
                            <option value="AE">Emiratos Árabes Unidos</option>
                            <option value="ER">Eritrea</option>
                            <option value="SK">Eslovaquia</option>
                            <option value="SI">Eslovenia</option>
                            <option value="ES">España</option>
                            <option value="US">Estados Unidos</option>
                            <option value="EE">Estonia</option>
                            <option value="ET">Etiopía</option>
                            <option value="FJ">Fiji</option>
                            <option value="PH">Filipinas</option>
                            <option value="FI">Finlandia</option>
                            <option value="FR">Francia</option>
                            <option value="GA">Gabón</option>
                            <option value="GM">Gambia</option>
                            <option value="GE">Georgia</option>
                            <option value="GH">Ghana</option>
                            <option value="GI">Gibraltar</option>
                            <option value="GD">Granada</option>
                            <option value="GR">Grecia</option>
                            <option value="GL">Groenlandia</option>
                            <option value="GP">Guadalupe</option>
                            <option value="GU">Guam</option>
                            <option value="GT">Guatemala</option>
                            <option value="GF">Guayana Francesa</option>
                            <option value="GG">Guernsey</option>
                            <option value="GN">Guinea</option>
                            <option value="GQ">Guinea Ecuatorial</option>
                            <option value="GW">Guinea-Bissau</option>
                            <option value="GY">Guyana</option>
                            <option value="HT">Haití</option>
                            <option value="HN">Honduras</option>
                            <option value="HK">Hong Kong</option>
                            <option value="HU">Hungría</option>
                            <option value="IN">India</option>
                            <option value="ID">Indonesia</option>
                            <option value="IQ">Irak</option>
                            <option value="IE">Irlanda</option>
                            <option value="BV">Isla Bouvet</option>
                            <option value="IM">Isla de Man</option>
                            <option value="NF">Isla Norfolk</option>
                            <option value="IS">Islandia</option>
                            <option value="CX">Islas Navidad</option>
                            <option value="CC">Islas Cocos</option>
                            <option value="CK">Islas Cook</option>
                            <option value="FO">Islas Faroe</option>
                            <option value="GS">Islas Georgias del Sur y Sandwich del Sur</option>
                            <option value="HM">Islas Heard y McDonald</option>
                            <option value="FK">Islas Malvinas</option>
                            <option value="MP">Islas Marianas del Norte</option>
                            <option value="MH">Islas Marshall</option>
                            <option value="PN">Islas Pitcairn</option>
                            <option value="TC">Islas Turcas y Caicos</option>
                            <option value="UM">Islas Ultramarinas de E.E.U.U</option>
                            <option value="VI">Islas Vírgenes</option>
                            <option value="IL">Israel</option>
                            <option value="IT">Italia</option>
                            <option value="JM">Jamaica</option>
                            <option value="JP">Japón</option>
                            <option value="JE">Jersey</option>
                            <option value="JO">Jordania</option>
                            <option value="KZ">Kazajistán</option>
                            <option value="KE">Kenia</option>
                            <option value="KG">Kirguistán</option>
                            <option value="KI">Kiribati</option>
                            <option value="KW">Kuwait</option>
                            <option value="LA">Laos, República Democrática</option>
                            <option value="LS">Lesotho</option>
                            <option value="LV">Letonia</option>
                            <option value="LB">Líbano</option>
                            <option value="LR">Liberia</option>
                            <option value="LY">Libia</option>
                            <option value="LI">Liechtenstein</option>
                            <option value="LT">Lituania</option>
                            <option value="LU">Luxemburgo</option>
                            <option value="MO">Macao</option>
                            <option value="MK">Macedonia</option>
                            <option value="MG">Madagascar</option>
                            <option value="MY">Malasia</option>
                            <option value="MW">Malawi</option>
                            <option value="MV">Maldivas</option>
                            <option value="ML">Malí</option>
                            <option value="MT">Malta</option>
                            <option value="MA">Marruecos</option>
                            <option value="MQ">Martinica</option>
                            <option value="MU">Mauricio</option>
                            <option value="MR">Mauritania</option>
                            <option value="YT">Mayotte</option>
                            <option value="MX">México</option>
                            <option value="FM">Micronesia</option>
                            <option value="MD">Moldavia</option>
                            <option value="MC">Mónaco</option>
                            <option value="MN">Mongolia</option>
                            <option value="ME">Montenegro</option>
                            <option value="MS">Montserrat</option>
                            <option value="MZ">Mozambique</option>
                            <option value="MM">Myanmar</option>
                            <option value="NA">Namibia</option>
                            <option value="NR">Nauru</option>
                            <option value="NP">Nepal</option>
                            <option value="NI">Nicaragua</option>
                            <option value="NE">Níger</option>
                            <option value="NG">Nigeria</option>
                            <option value="NU">Niue</option>
                            <option value="NO">Noruega</option>
                            <option value="NC">Nueva Caledonia</option>
                            <option value="NZ">Nueva Zelanda</option>
                            <option value="OM">Omán</option>
                            <option value="NL">Países Bajos</option>
                            <option value="PK">Pakistán</option>
                            <option value="PW">Palaos</option>
                            <option value="PS">Palestina</option>
                            <option value="PA">Panamá</option>
                            <option value="PG">Papúa Nueva Guinea</option>
                            <option value="PY">Paraguay</option>
                            <option value="PE">Perú</option>
                            <option value="PF">Polinesia Francesa</option>
                            <option value="PL">Polonia</option>
                            <option value="PT">Portugal</option>
                            <option value="PR">Puerto Rico</option>
                            <option value="QA">Qatar</option>
                            <option value="GB">Reino Unido</option>
                            <option value="KR">República de Corea</option>
                            <option value="CZ">República Checa</option>
                            <option value="DO">República Dominicana</option>
                            <option value="IR">República Islámica de Irán</option>
                            <option value="RE">Reunión</option>
                            <option value="RW">Ruanda</option>
                            <option value="RO">Rumania</option>
                            <option value="RU">Rusia</option>
                            <option value="EH">Sahara Occidental</option>
                            <option value="BL">Saint Barthélemy</option>
                            <option value="MF">Saint Martin (French part)</option>
                            <option value="SB">Salomón, Islas</option>
                            <option value="WS">Samoa</option>
                            <option value="AS">Samoa Americana</option>
                            <option value="KN">San Cristóbal y Nieves</option>
                            <option value="SM">San Marino</option>
                            <option value="PM">San Pedro y Miquelón</option>
                            <option value="VC">San Vicente y las Granadinas</option>
                            <option value="SH">Santa Elena</option>
                            <option value="LC">Santa Lucía</option>
                            <option value="ST">Santo Tomé y Príncipe</option>
                            <option value="SN">Senegal</option>
                            <option value="RS">Serbia</option>
                            <option value="SC">Seychelles</option>
                            <option value="SL">Sierra Leona</option>
                            <option value="SG">Singapur</option>
                            <option value="SX">Sint Maarten (Dutch part)</option>
                            <option value="SY">Siria</option>
                            <option value="SO">Somalía</option>
                            <option value="SS">South Sudan</option>
                            <option value="LK">Sri Lanka</option>
                            <option value="ZA">Sudáfrica</option>
                            <option value="SD">Sudán</option>
                            <option value="SE">Suecia</option>
                            <option value="CH">Suiza</option>
                            <option value="SR">Surinam</option>
                            <option value="SJ">Svalbard y Jan Mayen</option>
                            <option value="SZ">Swazilandia</option>
                            <option value="TH">Tailandia</option>
                            <option value="TW">Taiwán</option>
                            <option value="TZ">Tanzania</option>
                            <option value="TJ">Tayikistán</option>
                            <option value="IO">Territorio Británico Océano Índico</option>
                            <option value="TF">Territorios Australes Franceses</option>
                            <option value="TL">Timor Oriental</option>
                            <option value="TG">Togo</option>
                            <option value="TK">Tokelau</option>
                            <option value="TO">Tonga</option>
                            <option value="TT">Trinidad y Tobago</option>
                            <option value="TN">Túnez</option>
                            <option value="TM">Turkmenistán</option>
                            <option value="TR">Turquía</option>
                            <option value="TV">Tuvalu</option>
                            <option value="UA">Ucrania</option>
                            <option value="UG">Uganda</option>
                            <option value="UY">Uruguay</option>
                            <option value="UZ">Uzbekistán</option>
                            <option value="VU">Vanuatu</option>
                            <option value="VE">Venezuela</option>
                            <option value="VN">Vietnam</option>
                            <option value="VG">Islas Vírgenes Británicas</option>
                            <option value="WF">Wallis y Fortuna</option>
                            <option value="YE">Yemen</option>
                            <option value="ZM">Zambia</option>
                            <option value="ZW">Zimbabue</option>

                          </select>
                    </div>
                  </div>

                  <!-- ENTRADA PARA EL TIPO DE PERSONA-->

                  <div class="form-group">
                    <p>Tipo de persona:</p>
                    <div class="input-group mb-3">
                          <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                          </div>
                          <select class="form-control" name="editarTipoPersona" value="" required>
                            <option id="editarTipoPersona" value=""></option>
                            <option value="1">Natural</option>
                            <option value="2">Juridica</option>
                          </select>
                    </div>
                  </div>

                </div>

                <div class="col-xl-6 col-xs-12">
                  <!-- ENTRADA PARA EL TIPO DE CONTRIBUYENTE -->

                    <div class="form-group">
                    <p>Tipo de contribuyente:</p>
                      <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                            </div>
                            <select class="form-control" id="editarTipoContribuyentes" name="editarTipoContribuyentes" value="" required>
                              <option id="editarContribu" value=""></option>
                              <option value="00">Persona natural</option>
                              <option value="01">Persona natural /juridica declarante de Iva - empresa sin beneficios fiscales</option>
                              <option value="02">Empresa con beneficios fiscales</option>
                              <option value="03">Diplomática o institución pública</option>
                            </select>
                      </div>

                    </div>


                    <!-- ENTRADA PARA EL NIT -->

                    <div class="form-group">
                      <P>Colocarlo sin guiones (si NO es contribuyente colocar 0000  y si es extranjero colocar aquí su número de identificación tributaria)</P>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="editarNITCliente" name="editarNITCliente" value="" required>
                      </div>

                    </div>

                    <!-- ENTRADA PARA EL DUI -->

                    <div class="form-group">
                      <P>DUI Colocarlo sin guiones</P>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="editarDUICliente" name="editarDUICliente" value="">
                      </div>

                    </div>

                    <!-- ENTRADA PARA EL NRC -->

                    <div class="form-group">
                      <P>Colocarlo sin guiones y si es extranjero colocar aquí su número de identificación tributaria</P>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="editarNRCCliente" name="editarNRCCliente" value="">
                      </div>

                    </div>

                    <!-- ENTRADA PARA EL CÓDIGO DE ACT-->

                    <div class="form-group">
                          <p>Código de actividad económica, si no es contribuyente o es extranjero colocar 0000</p>
                            <div class="input-group mb-3">
                                  <div class="input-group-prepend">
                                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                  </div>
                                  <input type="text" class="form-control" id="editarCodActividad" name="editarCodActividad" placeholder="Ingresar código de actividad" required>
                            </div>

                          </div>
                          
                          <!-- ENTRADA PARA LA ACT-->

                          <div class="form-group">
                          <p>Actividad económica, si no es contribuyente colocar 0000</p>
                            <div class="input-group mb-3">
                                  <div class="input-group-prepend">
                                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                  </div>
                                  <input type="text" class="form-control" id="editarDescActividad" name="editarDescActividad" placeholder="Ingresar nombre de actividad económica" required>
                            </div>

                          </div>

                </div>

            </div>
            

            

            
              </div>
            </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Modificar cliente</button>

        </div>

     <?php
          $editarCliente = new ControladorClientes();
          $editarCliente -> ctrEditarCliente();
        ?> 

      </form>

    </div>
    </div>
    </div>

  </div>

</div>

<!--=====================================
MODAL CREAR MOTORISTA
======================================-->

<div id="modalCrearMotorista" class="modal fade" role="dialog">
  
  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:grey; color:white">
          <h4 class="modal-title">Crear motorista</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">
      
            <!-- ENTRADA PARA EL NOMBRE -->            
            <div class="form-group">
              <div class="input-group mb-3">
                
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                </div>
                <input type="text" class="form-control" name="nuevoNombreMotorista" id="nuevoNombreMotorista" placeholder="Ingresar nombre del motorista" required>
              </div>
            </div>

            <!-- ENTRADA PARA EL DUI -->            
            <div class="form-group">
              Dui sin guiones:
              <div class="input-group mb-3">
                
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                </div>
                <input type="text" class="form-control" name="nuevoDuiMotorista" id="nuevoDuiMotorista" placeholder="Ingresar DUI del motorista" required>
              </div>
            </div>

            <!-- ENTRADA PARA LA PLACA -->            
            <div class="form-group">
              <div class="input-group mb-3">
                
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                </div>
                <input type="text" class="form-control" name="nuevoPlacaMotorista" id="nuevoPlacaMotorista" placeholder="Ingresar placa del motorista" required>
              </div>
            </div>
        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-dark">Guardar motorista</button>

        </div>

        <?php

          $crearMotorista = new ControladorClientes();
          $crearMotorista -> ctrCrearMotorista();

        ?>

      </form>

      </div>

    </div>

    </div>

  </div>

</div>

<!--=====================================
MODAL VER MOTORISTAS
======================================-->

<div id="modalVerMotoristas" class="modal fade bd-example-modal-lg" role="dialog" style="width: 100% !important">
  
  <div class="modal-dialog modal-lg" style="max-width: 70%;">

    <div class="modal-content">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:grey; color:white">
          <h4 class="modal-title">Motoristas registrados</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">
          <table class="table table-bordered table-striped dt-responsive tablas" width="100%">
         
         <thead>
          
          <tr>
            
            <th style="width:10px">#</th>
            <th>Nombre</th>
            <th>DUI</th>
            <th>Placa</th>
            <th>Acciones</th>
 
          </tr> 
 
         </thead>
 
         <tbody>
 
         <?php
 
         $item = null;
         $valor = null;
         $orden = "id";
 
         $motorista = ControladorClientes::ctrMostrarMotoristas($item, $valor, $orden);
 
        foreach ($motorista as $key => $value){
            
           echo ' <tr>
                   <td>'.($key+1).'</td>
                   <td>'.$value["nombre"].'</td>
                   <td>'.$value["duiMotorista"].'</td>
                   <td>'.$value["placaMotorista"].'</td>';
 
                             
                   echo '
                   <td>
 
                     <div class="btn-group">
                         
                       <button class="btn btn-warning btnEditarMotorista" idMotorista="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarMotorista"><i class="fa fa-pencil"></i></button>
 
                       <button class="btn btn-danger btnEliminarMotorista" idMotorista="'.$value["id"].'"><i class="fa fa-times"></i></button>';
 
                     echo '</div>  
 
                   </td>
 
                 </tr>';
         }
 
 
         ?> 
 
         </tbody>
 
        </table>
            

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

<!--=====================================
MODAL EDITAR MOTORISTA
======================================-->

<div id="modalEditarMotorista" class="modal fade" role="dialog">
  
  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:grey; color:white">
          <h4 class="modal-title">Editar motorista</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">
      
            <!-- ENTRADA PARA EL NOMBRE -->            
            <div class="form-group">
              <div class="input-group mb-3">
                
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                </div>
                <input type="text" name="editarIdMotorista" id="editarIdMotorista" hidden>
                <input type="text" class="form-control" name="editarnombreMotorista" id="editarNombreMotorista" placeholder="Ingresar nombre del motorista" required>
              </div>
            </div>

            <!-- ENTRADA PARA EL DUI -->            
            <div class="form-group">
              Dui sin guiones:
              <div class="input-group mb-3">
                
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                </div>
                <input type="text" class="form-control" name="editarDuiMotorista" id="editarDuiMotorista" placeholder="Ingresar DUI del motorista" required>
              </div>
            </div>

            <!-- ENTRADA PARA LA PLACA -->            
            <div class="form-group">
              <div class="input-group mb-3">
                
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                </div>
                <input type="text" class="form-control" name="editarPlacaMotorista" id="editarPlacaMotorista" placeholder="Ingresar placa del motorista" required>
              </div>
            </div>
        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-dark">Guardar motorista</button>

        </div>

        <?php

          $editarMotorista = new ControladorClientes();
          $editarMotorista -> ctrEditarMotorista();

        ?>

      </form>

      </div>

    </div>

    </div>

  </div>

</div>

<?php

  $borrarEvento = new ControladorFacturas();
  $borrarEvento -> ctrBorrarEvento();

?>

<?php

  $borrarFactura = new ControladorFacturas();
  $borrarFactura -> ctrBorrarFactura();

?>
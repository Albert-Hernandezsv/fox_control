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
    <button class="btn btn-primary" onclick="location.href='facturacion-contingencia'">
          
          Regresar

    </button>
    <br><br>
    <h1>
      
      Factura de exportación - en modo de contingencia
    
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
                        <input type="text" name="tipoDte" id="tipoDte" value="11" hidden>
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

                    <!-- ENTRADA PARA LA CONDICIÓN DE CONTINGENCIA -->
                    <div class="form-group">
                        <p>Tipo de contingencia:</p>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                            </div>
                            <select name="tipoContingencia" id="tipoContingencia" class="form-control" required>
                                <option value="" disabled selected>Seleccione una opción</option>
                                <option value="1">No disponibilidad de sistema del MH</option>
                                <option value="2">No disponibilidad de sistema del emisor</option>
                                <option value="3">Falla en el suministro de servicio de Internet del Emisor</option>
                                <option value="4">Falla en el suministro de servicio de energía eléctrica del emisor que impida la transmisión de los DTE</option>
                            </select>
                        </div>
                    </div>

                    <!-- ENTRADA PARA EL MOTIVO DE CONTINGENCIA -->
                    <div class="form-group">
                        <p>Motivo de la contingencia:</p>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                            </div>
                            <input type="text" name="motivoContingencia" id="motivoContingencia" class="form-control" placeholder="Motivo de la contingencia" required>                       
                        </div>
                    </div>

                    <!-- ENTRADA PARA LA CONDICIÓN DE LA OPERACIÓN -->
                    <div class="form-group">
                        <p>Condición de la operación:</p>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                            </div>
                            <select name="condicionOperacion" id="condicionOperacion" class="form-control" required>
                                <option value="" disabled selected>Seleccione una opción</option>
                                <option value="1">Contado</option>
                                <option value="2">A crédito</option>
                                <option value="3">Otro</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-xl-6 col-xs-12">

                            <!-- ENTRADA PARA EL RECINTO FISCAL -->
                            <div class="form-group">
                                <p>Recinto fiscal:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                    </div>
                                    <select name="recintoFiscal" class="form-control" required>
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="01">01 Terrestre San Bartola</option>
                                        <option value="02">02 Marítima de Acajutla</option>
                                        <option value="03">03 Aérea De Comalapa</option>
                                        <option value="04">04 Terrestre Las Chinamas</option>
                                        <option value="05">05 Terrestre La Hachadura</option>
                                        <option value="06">06 Terrestre Santa Ana</option>
                                        <option value="07">07 Terrestre San Cristóbal</option>
                                        <option value="08">08 Terrestre Anguiatú</option>
                                        <option value="09">09 Terrestre El Amatillo</option>
                                        <option value="10">10 Marítima La Unión</option>
                                        <option value="11">11 Terrestre El Poy</option>
                                        <option value="12">12 Terrestre Metalio</option>
                                        <option value="15">15 Fardos Postales</option>
                                        <option value="16">16 Z.F. San Marcos</option>
                                        <option value="17">17 Z.F. El Pedregal</option>
                                        <option value="18">18 Z.F. San Bartola</option>
                                        <option value="20">20 Z.F. Exportsalva</option>
                                        <option value="21">21 Z.F. American Park</option>
                                        <option value="23">23 Z.F. Internacional</option>
                                        <option value="24">24 Z.F. Diez</option>
                                        <option value="26">26 Z.F. Miramar</option>
                                        <option value="27">27 Z.F. Santo Tomas</option>
                                        <option value="28">28 Z.F. Santa Tecla</option>
                                        <option value="29">29 Z.F. Santa Ana</option>
                                        <option value="30">30 Z.F. La Concordia</option>
                                        <option value="31">31 Aérea llopango</option>
                                        <option value="32">32 Z.F. Pipil</option>
                                        <option value="33">33 Puerto Sarillas</option>
                                        <option value="34">34 Z.F. Calvo Conservas</option>
                                        <option value="35">35 Feria Internacional</option>
                                        <option value="36">36 Aduana El Papalón</option>
                                        <option value="37">37 Z.F. Sam-Li</option>
                                        <option value="38">38 Z.F. San José</option>
                                        <option value="39">39 Z.F. Las Mercedes</option>
                                        <option value="71">71 Aldesa</option>
                                        <option value="72">72 Agdosa Merliot</option>
                                        <option value="73">73 Bodesa</option>
                                        <option value="76">76 Delegacion DHL</option>
                                        <option value="77">77 Transauto</option>
                                        <option value="80">80 Nejapa</option>
                                        <option value="81">81 Almaconsa</option>
                                        <option value="83">83 Agdosa Apopa</option>
                                        <option value="85">85 Gutiérrez Courier Y Cargo</option>
                                        <option value="99">99 San Bartola Envío Hn/Gt</option>
                                    </select>

                                </div>

                            </div>

                        </div>

                        <div class="col-xl-6 col-xs-12">
                            
                            <!-- ENTRADA PARA EL REGIMEN -->
                            <div class="form-group">
                                <p>Tipo de regimen:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                    </div>
                                    <select name="regimen" class="form-control" required>
                                        <option value="1000.000">Exportación Definitiva, Régimen Común</option>
                                        <option value="1040.000">Exportación Definitiva Sustitución de Mercancías, Régimen Común</option>
                                        <option value="1041.020">Exportación Definitiva Proveniente de Franquicia Provisional, Franq. Presidenciales exento de DAI</option>
                                        <option value="1041.021">Exportación Definitiva Proveniente de Franquicia Provisional, Franq. Presidenciales exento de DAI e IVA</option>
                                        <option value="1048.025">Exportación Definitiva Proveniente de Franquicia Definitiva, Maquinaria y Equipo LZF. DPA</option>
                                        <option value="1048.031">Exportación Definitiva Proveniente de Franquicia Definitiva, Distribución Internacional</option>
                                        <option value="1048.032">Exportación Definitiva Proveniente de Franquicia Definitiva, Operaciones Internacionales de Logística</option>
                                        <option value="1048.033">Exportación Definitiva Proveniente de Franquicia Definitiva, Centro Internacional de llamadas (Call Center)</option>
                                        <option value="1048.034">Exportación Definitiva Proveniente de Franquicia Definitiva, Tecnologías de Información LSI</option>
                                        <option value="1048.035">Exportación Definitiva Proveniente de Franquicia Definitiva, Investigación y Desarrollo LSI</option>
                                        <option value="1048.036">Exportación Definitiva Proveniente de Franquicia Definitiva, Reparación y Mantenimiento de Embarcaciones Marítimas LSI</option>
                                        <option value="1048.037">Exportación Definitiva Proveniente de Franquicia Definitiva, Reparación y Mantenimiento de Aeronaves LSI</option>
                                        <option value="1048.038">Exportación Definitiva Proveniente de Franquicia Definitiva, Procesos Empresariales LSI</option>
                                        <option value="1048.039">Exportación Definitiva Proveniente de Franquicia Definitiva, Servicios Médico-Hospitalarios LSI</option>
                                        <option value="1048.040">Exportación Definitiva Proveniente de Franquicia Definitiva, Servicios Financieros Internacionales LSI</option>
                                        <option value="1048.043">Exportación Definitiva Proveniente de Franquicia Definitiva, Reparación y Mantenimiento de Contenedores LSI</option>
                                        <option value="1048.044">Exportación Definitiva Proveniente de Franquicia Definitiva, Reparación de Equipos Tecnológicos LSI</option>
                                        <option value="1048.054">Exportación Definitiva Proveniente de Franquicia Definitiva, Atención Ancianos y Convalecientes LSI</option>
                                        <option value="1048.055">Exportación Definitiva Proveniente de Franquicia Definitiva, Telemedicina LSI</option>
                                        <option value="1048.056">Exportación Definitiva Proveniente de Franquicia Definitiva, Cinematografía LSI</option>
                                        <option value="1052.000">Exportación Definitiva de DPA con origen en Compras Locales, Régimen Común</option>
                                        <option value="1054.000">Exportación Definitiva de Zona Franca con origen en Compras Locales, Régimen Común</option>
                                        <option value="1100.000">Exportación Definitiva de Envíos de Socorro, Régimen Común</option>
                                        <option value="1200.000">Exportación Definitiva de Envíos Postales, Régimen Común</option>
                                        <option value="1300.000">Exportación Definitiva Envíos que requieren despacho urgente, Régimen Común</option>
                                        <option value="1400.000">Exportación Definitiva Courier, Régimen Común</option>
                                        <option value="1400.011">Exportación Definitiva Courier, Muestras Sin Valor Comercial</option>
                                        <option value="1400.012">Exportación Definitiva Courier, Material Publicitario</option>
                                        <option value="1400.017">Exportación Definitiva Courier, Declaración de Documentos</option>
                                        <option value="1500.000">Exportación Definitiva Menaje de casa, Régimen Común</option>
                                        <option value="2100.000">Exportación Temporal para Perfeccionamiento Pasivo, Régimen Común</option>
                                        <option value="2100.065">Exportación Temporal para Perfeccionamiento Pasivo, De Mercado Nacional a ZF o DPA</option>
                                        <option value="2200.000">Exportación Temporal con Reimportación en el mismo estado, Régimen Común</option>
                                        <option value="2200.065">Exportación Temporal con Reimportación en el mismo estado, De Mercado Nacional a ZF o DPA</option>
                                        <option value="2400.000">Traslados Definitivos</option>
                                        <option value="3050.000">Reexportación Proveniente de Importación Temporal, Régimen Común</option>
                                        <option value="3051.000">Reexportación Proveniente de Tiendas Libres, Régimen Común</option>
                                        <option value="3052.000">Reexportación Proveniente de Admisión Temporal para Perfeccionamiento Activo, Régimen Común</option>
                                        <option value="3053.000">Reexportación Proveniente de Admisión Temporal, Régimen Común</option>
                                        <option value="3054.000">Reexportación Proveniente de Régimen de Zona Franca, Régimen Común</option>
                                        <option value="3055.000">Reexportación Proveniente de Admisión Temporal para Perfeccionamiento Activo con Garantía, Régimen Común</option>
                                        <option value="3056.000">Reexportación Proveniente de Admisión Temporal Distribución Internacional Parque de Servicios, Régimen Común</option>
                                        <option value="3056.047">Reexportación Proveniente de Admisión Temporal Distribución Internacional Parque de Servicios, Remisión a Departamento de Subastas</option>
                                        <option value="3056.057">Reexportación Proveniente de Admisión Temporal Distribución Internacional Parque de Servicios, Remisión entre Usuarios Directos del Mismo Parque de Servicios</option>
                                        <option value="3056.058">Reexportación Proveniente de Admisión Temporal Distribución Internacional Parque de Servicios, Remisión entre Usuarios Directos de Diferente Parque de Servicios</option>
                                        <option value="3056.072">Reexportación Proveniente de Admisión Temporal Distribución Internacional Parque de Servicios, Decreto 738 Eléctricos e Híbridos</option>
                                        <option value="3056.081">Reexportación Proveniente de Admisión Temporal Distribución Internacional Parque de Servicios, Remisión entre Usuarios Directos LSI</option>
                                        <option value="3056.084">Reexportación Proveniente de Admisión Temporal Distribución Internacional Parque de Servicios, De una LSI para un DPA</option>
                                        <option value="3056.085">Reexportación Proveniente de Admisión Temporal Distribución Internacional Parque de Servicios, De una LSI para una ZF</option>
                                        <option value="3057.000">Reexportación Proveniente de Admisión Temporal Operaciones Internacional de Logística Parque de Servicios, Régimen Común</option>
                                        <option value="3057.047">Reexportación Proveniente de Admisión Temporal Operaciones Internacional de Logística Parque de Servicios, Remisión a Departamento de Subastas</option>
                                        <option value="3057.057">Reexportación Proveniente de Admisión Temporal Operaciones Internacional de Logística Parque de Servicios, Remisión entre Usuarios Directos del Mismo Parque de Servicios</option>
                                        <option value="3057.058">Reexportación Proveniente de Admisión Temporal Operaciones Internacional de Logística Parque de Servicios, Remisión entre Usuarios Directos de Diferente Parque de Servicios</option>
                                        <option value="3057.081">Reexportación Proveniente de Admisión Temporal Operaciones Internacional de Logística Parque de Servicios, Remisión entre Usuarios Directos LSI</option>
                                        <option value="3057.084">Reexportación Proveniente de Admisión Temporal Operaciones Internacional de Logística Parque de Servicios, De una LSI para un DPA</option>
                                        <option value="3057.085">Reexportación Proveniente de Admisión Temporal Operaciones Internacional de Logística Parque de Servicios, De una LSI para una ZF</option>
                                        <option value="3058.033">Reexportación Proveniente de Admisión Temporal Centro Servicio LSI, Centro Internacional de llamadas (Call Center)</option>
                                        <option value="3058.034">Reexportación Proveniente de Admisión Temporal Centro Servicio LSI, Tecnologías de Información LSI</option>
                                        <option value="3058.035">Reexportación Proveniente de Admisión Temporal Centro Servicio LSI, Investigación y Desarrollo LSI</option>
                                        <option value="3058.036">Reexportación Proveniente de Admisión Temporal Centro Servicio LSI, Reparación y Mantenimiento de Embarcaciones Marítimas LSI</option>
                                        <option value="3058.037">Reexportación Proveniente de Admisión Temporal Centro Servicio LSI, Reparación y Mantenimiento de Aeronaves LSI</option>
                                        <option value="3058.038">Reexportación Proveniente de Admisión Temporal Centro Servicio LSI, Procesos Empresariales LSI</option>
                                        <option value="3058.039">Reexportación Proveniente de Admisión Temporal Centro Servicio LSI, Servicios Médico-Hospitalarios LSI</option>
                                        <option value="3058.040">Reexportación Proveniente de Admisión Temporal Centro Servicio LSI, Servicios Financieros Internacionales LSI</option>
                                        <option value="3058.043">Reexportación Proveniente de Admisión Temporal Centro Servicio LSI, Reparación y Mantenimiento de Contenedores LSI</option>
                                        <option value="3058.044">Reexportación Proveniente de Admisión Temporal Centro Servicio LSI, Reparación de Equipos Tecnológicos LSI</option>
                                        <option value="3058.054">Reexportación Proveniente de Admisión Temporal Centro Servicio LSI, Atención Ancianos y Convalecientes LSI</option>
                                        <option value="3058.055">Reexportación Proveniente de Admisión Temporal Centro Servicio LSI, Telemedicina LSI</option>
                                        <option value="3058.056">Reexportación Proveniente de Admisión Temporal Centro Servicio LSI, Cinematografía LSI</option>
                                        <option value="3058.082">Reexportación Proveniente de Admisión Temporal Centro Servicio LSI, Remisión entre Centros de Servicios LSI</option>
                                        <option value="3059.000">Reexportación Proveniente de Admisión Temporal Reparación de Equipo Tecnológico Parque de Servicios, Régimen Común</option>
                                        <option value="3059.057">Reexportación Proveniente de Admisión Temporal Reparación de Equipo Tecnológico Parque de Servicios, Remisión entre Usuarios Directos del Mismo Parque de Servicios</option>
                                        <option value="3059.058">Reexportación Proveniente de Admisión Temporal Reparación de Equipo Tecnológico Parque de Servicios, Remisión entre Usuarios Directos de Diferente Parque de Servicios</option>
                                        <option value="3059.033">Reexportación Proveniente de Servicios Internacionales en Parques de Servicios, Centro Internacional de llamadas (Call Center)</option>
                                        <option value="3059.034">Reexportación Proveniente de Servicios Internacionales en Parques de Servicios, Tecnologías de Información LSI</option>
                                        <option value="3059.035">Reexportación Proveniente de Servicios Internacionales en Parques de Servicios, Investigación y Desarrollo LSI</option>
                                        <option value="3059.038">Reexportación Proveniente de Servicios Internacionales en Parques de Servicios, Procesos Empresariales LSI</option>
                                        <option value="3059.039">Reexportación Proveniente de Servicios Internacionales en Parques de Servicios, Servicios Médico-Hospitalarios LSI</option>
                                        <option value="3059.040">Reexportación Proveniente de Servicios Internacionales en Parques de Servicios, Servicios Financieros Internacionales LSI</option>
                                        <option value="3059.044">Reexportación Proveniente de Servicios Internacionales en Parques de Servicios, Reparación de Equipos Tecnológicos LSI</option>
                                        <option value="3059.047">Reexportación Proveniente de Servicios Internacionales en Parques de Servicios, Remisión a Departamento de Subastas</option>
                                        <option value="3059.054">Reexportación Proveniente de Servicios Internacionales en Parques de Servicios, Atención Ancianos y Convalecientes LSI</option>
                                        <option value="3059.055">Reexportación Proveniente de Servicios Internacionales en Parques de Servicios, Telemedicina LSI</option>
                                        <option value="3059.056">Reexportación Proveniente de Servicios Internacionales en Parques de Servicios, Cinematografía LSI</option>
                                        <option value="3059.081">Reexportación Proveniente de Servicios Internacionales en Parques de Servicios, Remisión entre Usuarios Directos</option>
                                        <option value="3070.000">Reexportación Proveniente de Depósito, Régimen Común</option>
                                        <option value="3070.047">Reexportación Proveniente de Depósito, Remisión a Departamento de Subastas</option>
                                        <option value="3070.072">Reexportación Proveniente de Depósito, Decreto 738 Eléctricos e Híbridos</option>
                                        <option value="3071.000">Reexp. Prov. de Depósito</option>
                                        <option value="0000.000">Tránsito Aduanero</option>
                                    </select>

                                </div>

                            </div>

                        </div>
                    
                    </div>

                    <div class="row">

                        <div class="col-xl-6 col-xs-12">

                            <!-- ENTRADA PARA EL MODO DE TRANSPORTE -->
                            <div class="form-group">
                                <p>Modo de transporte en la entrega:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                    </div>
                                    <select name="modoTransporte" class="form-control" required>
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="1">Terrestre</option>
                                        <option value="2">Aéreo</option>
                                        <option value="3">Marítimo</option>
                                        <option value="4">Ferreo</option>
                                        <option value="5">Multimodal</option>
                                        <option value="6">Correo</option>
                                    </select>

                                </div>

                            </div>

                        </div>

                        <div class="col-xl-6 col-xs-12">
                            
                            <!-- ENTRADA PARA EL COSTO DEL SEGURO -->
                            <div class="form-group">
                                <p>Valor del seguro:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                    </div>
                                    <input type="number" name="seguro" id="seguro" class="form-control" placeholder="Monto del seguro" required min="0" step="0.01">
                                </div>

                            </div>

                        </div>
                    
                    </div>

                    <div class="row">

                        <div class="col-xl-6 col-xs-12">

                            <!-- ENTRADA PARA EL MONTO DEL FLETE -->
                            <div class="form-group">
                                <p>Valor del flete</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-usd"></i></span>
                                    </div>
                                    <input type="number" name="flete" id="flete" class="form-control" placeholder="Monto del flete" required min="0" step="0.01">
                                </div>

                            </div>

                        </div>

                        <div class="col-xl-6 col-xs-12">
                            <!-- ENTRADA PARA EL MOTORISTA -->
                            <div class="form-group">
                                <p>Seleccionar motorista a entregar el producto:</p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                    </div>

                                            <select name="idMotorista" class="form-control">
                                                <option value="" selected disabled>Seleccione un motorista</option>
                                                <?php
                                                    $item = null;
                                                    $valor = null;
                                                    $orden = "id";

                                                    $motoristas = ControladorClientes::ctrMostrarMotoristas($item, $valor, $orden);

                                                    foreach ($motoristas as $key => $value){
                                                            echo '<option value="'.$value["id"].'" >'.$value["nombre"].'</option>';
                                                    }
                                                ?>
                                            </select>
                                </div>
                            </div>
                        </div>
                    
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
                                        <br><br>
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
                                                            echo '<option data-value="'.$value["id"].'" data-precio="'.$value["precio_venta"].'" data-codigo="'.$value["codigo"].'">'.$value["nombre"].' '.$value["codigo"].'</option>';
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
                                    <br><br>
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
                                        <input type="number" class="form-control" name="nuevoPrecioProductoFacturaOriginal[]" readonly>
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
                                        <input type="text" class="form-control porcentajeDescuentoItem" name="porcentajeDescuento[]" min="0" readonly>
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
                <button type="submit" class="btn btn-dark">Crear factura localmente</button>
            </div>

        </form>

        <?php

          $crearFactura = new ControladorFacturas();
          $crearFactura -> ctrCrearFacturaContingencia();

        ?> 

        </div>

    </div>


  </section>

</div>
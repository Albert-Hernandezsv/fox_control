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
        $valor = $_GET["idClienteEscogerFactura"];
        $orden = "id";

        $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);

    ?>
    <button class="btn btn-success" onclick="location.href='facturacion-contingencia'">
          Cancelar
    </button>
    <br><br>
    <h1>
      
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
        echo($tipo) ?>
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio </a></li>
      
      <li class="active">&nbsp;Sistema de facturación</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
        Tipos de facturas a las que puede aplicar en MODO CONTINGENCIA:
<br><br>
      </div>

      
        
      <div class="box-body">
        
            <?php
                if($cliente["tipo_cliente"] == "00"){ // Persona normal
                    echo '
                        <div class="row">

                            <div class="col-xl-3 col-xs-12">
                                <div class="card" style="width: 18rem;">
                                    <img class="card-img-top" src="vistas/img/consumidor_final.png" alt="Card image cap">
                                    <div class="card-body">
                                        <a href="index.php?ruta=crear-factura-contingencia&idCliente='.$cliente["id"].'" class="btn btn-dark">Seleccionar</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-xs-12">
                                <div class="card" style="width: 18rem;">
                                    <img class="card-img-top" src="vistas/img/sujeto_excluido.png" alt="Card image cap">
                                    <div class="card-body">
                                        <a href="index.php?ruta=crear-factura-sujeto-contingencia&idCliente='.$cliente["id"].'" class="btn btn-dark">Seleccionar</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    ';
                }

                if($cliente["tipo_cliente"] == "01"){ // Persona que declara IVA - empresa
                    echo '
                        <div class="row">

                            <div class="col-xl-3 col-xs-12">
                                <div class="card" style="width: 18rem;">
                                    <img class="card-img-top" src="vistas/img/consumidor_final.png" alt="Card image cap">
                                    <div class="card-body">
                                        <a href="index.php?ruta=crear-factura-contingencia&idCliente='.$cliente["id"].'" class="btn btn-dark">Seleccionar</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-xs-12">
                                <div class="card" style="width: 18rem;">
                                    <img class="card-img-top" src="vistas/img/ccf.png" alt="Card image cap">
                                    <div class="card-body">
                                        <a href="index.php?ruta=crear-ccf-contingencia&idCliente='.$cliente["id"].'" class="btn btn-dark">Seleccionar</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-xs-12">
                                <div class="card" style="width: 18rem;">
                                    <img class="card-img-top" src="vistas/img/exportacion.png" alt="Card image cap">
                                    <div class="card-body">
                                        <a href="index.php?ruta=crear-factura-exportacion-contingencia&idCliente='.$cliente["id"].'" class="btn btn-dark">Seleccionar</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    ';
                }

                if($cliente["tipo_cliente"] == "02"){ // Beneficios fiscales
                    echo '
                        <div class="row">

                            <div class="col-xl-3 col-xs-12">
                                <div class="card" style="width: 18rem;">
                                    <img class="card-img-top" src="vistas/img/consumidor_final.png" alt="Card image cap">
                                    <div class="card-body">
                                        <a href="index.php?ruta=crear-factura-contingencia&idCliente='.$cliente["id"].'" class="btn btn-dark">Seleccionar</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-xs-12">
                                <div class="card" style="width: 18rem;">
                                    <img class="card-img-top" src="vistas/img/ccf.png" alt="Card image cap">
                                    <div class="card-body">
                                        <a href="index.php?ruta=crear-ccf-contingencia&idCliente='.$cliente["id"].'" class="btn btn-dark">Seleccionar</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-xs-12">
                                <div class="card" style="width: 18rem;">
                                    <img class="card-img-top" src="vistas/img/exportacion.png" alt="Card image cap">
                                    <div class="card-body">
                                        <a href="index.php?ruta=crear-factura-exportacion-contingencia&idCliente='.$cliente["id"].'" class="btn btn-dark">Seleccionar</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    ';
                }

                if($cliente["tipo_cliente"] == "03"){
                    echo '
                        <div class="row">

                            <div class="col-xl-3 col-xs-12">
                                <div class="card" style="width: 18rem;">
                                    <img class="card-img-top" src="vistas/img/consumidor_final.png" alt="Card image cap">
                                    <div class="card-body">
                                        <a href="index.php?ruta=crear-factura-contingencia&idCliente='.$cliente["id"].'" class="btn btn-dark">Seleccionar</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-xs-12">
                                <div class="card" style="width: 18rem;">
                                    <img class="card-img-top" src="vistas/img/ccf.png" alt="Card image cap">
                                    <div class="card-body">
                                        <a href="index.php?ruta=crear-ccf-contingencia&idCliente='.$cliente["id"].'" class="btn btn-dark">Seleccionar</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-xs-12">
                                <div class="card" style="width: 18rem;">
                                    <img class="card-img-top" src="vistas/img/exportacion.png" alt="Card image cap">
                                    <div class="card-body">
                                        <a href="index.php?ruta=crear-factura-exportacion-contingencia&idCliente='.$cliente["id"].'" class="btn btn-dark">Seleccionar</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    ';
                }
            ?>

        </div>

    </div>


  </section>

</div>
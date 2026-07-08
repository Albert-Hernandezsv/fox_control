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
      
      Buscar datos o facturas
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio </a></li>
      
      <li class="active">&nbsp;Sistema de facturación</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

        <button class="btn btn-info mt-3" onclick="location.href='facturacion'">
            Regresar
        </button>

        <?php
            $pdo = Conexion::conectar();

            $numeroControl = trim($_POST["numeroControl"] ?? "");
            $cliente = trim($_POST["cliente"] ?? "");
            $codigoProducto = trim($_POST["codigoProducto"] ?? "");
            $nombreProducto = trim($_POST["nombreProducto"] ?? "");
            $fechaDesde = trim($_POST["fechaDesde"] ?? "");
            $fechaHasta = trim($_POST["fechaHasta"] ?? "");

            $hayBusqueda = (
                $numeroControl !== "" ||
                $cliente !== "" ||
                $codigoProducto !== "" ||
                $nombreProducto !== "" ||
                $fechaDesde !== "" ||
                $fechaHasta !== ""
            );

            $facturas = [];

            if ($hayBusqueda) {

                $sql = "SELECT f.*, c.nombre AS nombre_cliente
                    FROM facturas_locales f
                    LEFT JOIN clientes c ON f.id_cliente = c.id
                    WHERE 1=1";

                $params = [];

                if ($numeroControl !== "") {
                    $sql .= " AND f.numeroControl LIKE :numeroControl";
                    $params[":numeroControl"] = "%".$numeroControl."%";
                }

                if ($cliente !== "") {
                    $sql .= " AND c.nombre LIKE :cliente";
                    $params[":cliente"] = "%".$cliente."%";
                }

                if ($codigoProducto !== "") {
                    $sql .= " AND f.productos LIKE :codigoProducto";
                    $params[":codigoProducto"] = "%".$codigoProducto."%";
                }

                if ($nombreProducto !== "") {

                    $stmtProd = $pdo->prepare("
                        SELECT id 
                        FROM inventario 
                        WHERE nombre LIKE :nombreProducto
                        ORDER BY 
                            CASE 
                                WHEN nombre = :nombreExacto THEN 1
                                WHEN nombre LIKE :nombreInicio THEN 2
                                ELSE 3
                            END
                        LIMIT 20
                    ");

                    $stmtProd->execute([
                        ":nombreProducto" => "%".$nombreProducto."%",
                        ":nombreExacto" => $nombreProducto,
                        ":nombreInicio" => $nombreProducto."%"
                    ]);

                    $productosEncontrados = $stmtProd->fetchAll(PDO::FETCH_COLUMN);

                    if (!empty($productosEncontrados)) {
                        $condicionesProducto = [];

                        foreach ($productosEncontrados as $index => $idProducto) {
                            $key1 = ":idProductoA".$index;
                            $key2 = ":idProductoB".$index;

                            $condicionesProducto[] = "(f.productos LIKE $key1 OR f.productos LIKE $key2)";

                            $params[$key1] = '%"idProducto":'.$idProducto.'%';
                            $params[$key2] = '%"idProducto": '.$idProducto.'%';
                        }

                        $sql .= " AND (" . implode(" OR ", $condicionesProducto) . ")";
                    } else {
                        $sql .= " AND 1=0";
                    }
                }

                if ($fechaDesde !== "") {
                    $sql .= " AND f.fecEmi >= :fechaDesde";
                    $params[":fechaDesde"] = $fechaDesde;
                }

                if ($fechaHasta !== "") {
                    $sql .= " AND f.fecEmi <= :fechaHasta";
                    $params[":fechaHasta"] = $fechaHasta;
                }

                $sql .= " ORDER BY f.id DESC LIMIT 100";

               try {
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    echo "<pre>";
                    echo "Resultados encontrados: " . count($facturas);
                    echo "</pre>";

                } catch (PDOException $e) {
                    echo "<pre>";
                    echo "ERROR SQL: " . $e->getMessage();
                    echo "</pre>";
                }
            }
        ?>

        <form method="POST" class="row" style="margin-bottom:20px;">

            <div class="col-md-3">
                <label>Número de control</label>
                <input type="text" name="numeroControl" class="form-control" value="<?php echo htmlspecialchars($numeroControl); ?>">
            </div>

            <div class="col-md-3">
                <label>Cliente</label>
                <input type="text" name="cliente" class="form-control" value="<?php echo htmlspecialchars($cliente); ?>">
            </div>

            <div class="col-md-3">
                <label>Código de producto</label>
                <input type="text" name="codigoProducto" class="form-control" value="<?php echo htmlspecialchars($codigoProducto); ?>">
            </div>

            <div class="col-md-3">
                <label>Nombre de producto</label>
                <input type="text" name="nombreProducto" class="form-control" value="<?php echo htmlspecialchars($nombreProducto); ?>">
            </div>

            <div class="col-md-3 mt-3">
                <label>Fecha desde</label>
                <input type="date" name="fechaDesde" class="form-control" value="<?php echo htmlspecialchars($fechaDesde); ?>">
            </div>

            <div class="col-md-3 mt-3">
                <label>Fecha hasta</label>
                <input type="date" name="fechaHasta" class="form-control" value="<?php echo htmlspecialchars($fechaHasta); ?>">
            </div>

            <div class="col-md-3 mt-3">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">
                    Buscar facturas
                </button>
            </div>

        </form>

        <?php if ($hayBusqueda): ?>
            <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Número control</th>
                    <th>Cliente</th>
                    <th>Tipo DTE</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Productos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facturas as $key => $factura): ?>
                    <tr>
                        <td><?php echo $key + 1; ?></td>
                        <td><?php echo $factura["numeroControl"]; ?></td>
                        <td><?php echo $factura["nombre_cliente"]; ?></td>
                        <td><?php echo $factura["tipoDte"]; ?></td>
                        <td><?php echo $factura["fecEmi"]; ?></td>
                        <td>$<?php echo number_format($factura["total"], 2); ?></td>
                        <td>
                            <?php
                            $productosFactura = json_decode($factura["productos"], true);

                            if (is_array($productosFactura)) {
                                foreach ($productosFactura as $producto) {
                                    echo "Código: " . $producto["codigo"] . 
                                        " | Cantidad: " . $producto["cantidad"] . "<br>";
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <a href="index.php?ruta=ver-factura&idFacturaEditar=<?php echo $factura['id']; ?>" 
                            class="btn btn-primary btn-sm">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
        <?php endif; ?>
    </div>

  </section>

</div>
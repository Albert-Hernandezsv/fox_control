<?php

$rolFacturacion = html_entity_decode("Facturaci&oacute;n", ENT_QUOTES, "UTF-8");
if(!in_array($_SESSION["rol"], array("Admin", $rolFacturacion, "Facturacion", "Contabilidad", "Vendedor"), true)){
    echo '<script>window.location = "inicio";</script>';
    return;
}

$idFactura = isset($_GET["idFactura"]) ? (int) $_GET["idFactura"] : 0;
$factura = ControladorFacturas::ctrMostrarFacturas("id", $idFactura, "id", "no");

if(!$factura || !in_array($factura["tipoDte"], array("01", "11", "14"), true)){
    echo '<script>window.location = "facturacion";</script>';
    return;
}

$fechaLimite = new DateTime($factura["fecEmi"] . " " . (isset($factura["horEmi"]) ? $factura["horEmi"] : "00:00:00"));
$fechaLimite->modify("+3 months")->setTime(23, 59, 59);
if(new DateTime() > $fechaLimite){
    echo '<script>window.location = "facturacion";</script>';
    return;
}

$cliente = ControladorClientes::ctrMostrarClientes("id", $factura["id_cliente"], "id");
$productos = json_decode($factura["productos"], true);
$productos = is_array($productos) ? $productos : array();
$retornadoPorProducto = array();
$eventosAnteriores = ControladorFacturas::ctrMostrarFacturasVarias("idFacturaRelacionada", $factura["id"], "id", "no");

foreach($eventosAnteriores as $eventoAnterior){
    if($eventoAnterior["tipoDte"] !== "18"){
        continue;
    }
    $productosAnteriores = json_decode($eventoAnterior["productos"], true);
    if(!is_array($productosAnteriores)){
        continue;
    }
    foreach($productosAnteriores as $productoAnterior){
        $idProductoAnterior = isset($productoAnterior["idProducto"]) ? (string) $productoAnterior["idProducto"] : "";
        $retornadoPorProducto[$idProductoAnterior] = isset($retornadoPorProducto[$idProductoAnterior])
            ? $retornadoPorProducto[$idProductoAnterior] + (int) $productoAnterior["cantidad"]
            : (int) $productoAnterior["cantidad"];
    }
}

?>

<div class="main-content content-wrapper">
  <section class="content-header">
    <h1>Crear evento de retorno</h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Evento de retorno</li>
    </ol>
  </section>

  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <a class="btn btn-primary" href="facturacion">Regresar</a>
      </div>

      <form method="post">
        <div class="box-body">
          <input type="hidden" name="crearEventoRetorno" value="1">
          <input type="hidden" name="idFacturaRelacionada" value="<?php echo (int) $factura["id"]; ?>">

          <div class="row">
            <div class="col-md-4">
              <p><strong>Cliente:</strong><br><?php echo htmlspecialchars($cliente["nombre"], ENT_QUOTES, "UTF-8"); ?></p>
            </div>
            <div class="col-md-4">
              <p><strong>N&uacute;mero de control:</strong><br><?php echo htmlspecialchars($factura["numeroControl"], ENT_QUOTES, "UTF-8"); ?></p>
            </div>
            <div class="col-md-4">
              <p><strong>C&oacute;digo de generaci&oacute;n:</strong><br><?php echo htmlspecialchars($factura["codigoGeneracion"], ENT_QUOTES, "UTF-8"); ?></p>
            </div>
          </div>

          <div class="alert alert-info">
            Indique solamente las cantidades que regresar&aacute;n al inventario. Los precios y descuentos se conservan desde la factura relacionada.
          </div>

          <?php if($factura["tipoDte"] === "11"): ?>
            <?php
              $opcionesRecintoFiscal = ControladorFacturas::ctrOpcionesExportacion("recintoFiscal");
              $opcionesTipoRegimen = ControladorFacturas::ctrOpcionesExportacion("tipo_regimen");
              $opcionesRegimen = ControladorFacturas::ctrOpcionesRegimenRetorno();
              $recintoFiscalSeleccionado = isset($_POST["recintoFiscal"]) ? $_POST["recintoFiscal"] : "";
              $tipoRegimenSeleccionado = isset($_POST["tipo_regimen"]) ? $_POST["tipo_regimen"] : "";
              $regimenSeleccionado = isset($_POST["regimen"]) ? $_POST["regimen"] : "";
            ?>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="recintoFiscalEventoRetorno">Recinto fiscal</label>
                  <select class="form-control" id="recintoFiscalEventoRetorno" name="recintoFiscal" required>
                    <option value="" disabled <?php echo $recintoFiscalSeleccionado === "" ? "selected" : ""; ?>>Seleccione una opci&oacute;n</option>
                    <?php foreach($opcionesRecintoFiscal as $codigo => $texto): ?>
                      <option value="<?php echo htmlspecialchars($codigo, ENT_QUOTES, "UTF-8"); ?>" <?php echo $recintoFiscalSeleccionado === $codigo ? "selected" : ""; ?>><?php echo htmlspecialchars($texto, ENT_QUOTES, "UTF-8"); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="tipoRegimenEventoRetorno">Tipo de r&eacute;gimen</label>
                  <select class="form-control" id="tipoRegimenEventoRetorno" name="tipo_regimen" required>
                    <option value="" disabled <?php echo $tipoRegimenSeleccionado === "" ? "selected" : ""; ?>>Seleccione una opci&oacute;n</option>
                    <?php foreach($opcionesTipoRegimen as $codigo => $texto): ?>
                      <option value="<?php echo htmlspecialchars($codigo, ENT_QUOTES, "UTF-8"); ?>" <?php echo $tipoRegimenSeleccionado === $codigo ? "selected" : ""; ?>><?php echo htmlspecialchars($texto, ENT_QUOTES, "UTF-8"); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="regimenEventoRetorno">R&eacute;gimen</label>
                  <select class="form-control" id="regimenEventoRetorno" name="regimen" required>
                    <option value="" disabled <?php echo $regimenSeleccionado === "" ? "selected" : ""; ?>>Seleccione una opci&oacute;n</option>
                    <?php foreach($opcionesRegimen as $codigo => $texto): ?>
                      <option value="<?php echo htmlspecialchars($codigo, ENT_QUOTES, "UTF-8"); ?>" <?php echo $regimenSeleccionado === $codigo ? "selected" : ""; ?>><?php echo htmlspecialchars($texto, ENT_QUOTES, "UTF-8"); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="fleteEventoRetorno">Valor del flete</label>
                  <input type="number"
                         class="form-control"
                         id="fleteEventoRetorno"
                         name="fleteRetorno"
                         value="<?php echo isset($_POST["fleteRetorno"]) ? htmlspecialchars($_POST["fleteRetorno"], ENT_QUOTES, "UTF-8") : "0.00"; ?>"
                         min="0"
                         step="0.01"
                         required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="seguroEventoRetorno">Valor del seguro</label>
                  <input type="number"
                         class="form-control"
                         id="seguroEventoRetorno"
                         name="seguroRetorno"
                         value="<?php echo isset($_POST["seguroRetorno"]) ? htmlspecialchars($_POST["seguroRetorno"], ENT_QUOTES, "UTF-8") : "0.00"; ?>"
                         min="0"
                         step="0.01"
                         required>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>C&oacute;digo</th>
                  <th>Facturado</th>
                  <th>Retornado</th>
                  <th>Disponible</th>
                  <th style="width:180px">Cantidad a devolver</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($productos as $indice => $producto): ?>
                  <?php
                    $idProducto = isset($producto["idProducto"]) ? (string) $producto["idProducto"] : "";
                    $productoInventario = ControladorProductos::ctrMostrarProductos("id", $idProducto);
                    $cantidadOriginal = isset($producto["cantidad"]) ? (int) $producto["cantidad"] : 0;
                    $cantidadRetornada = isset($retornadoPorProducto[$idProducto]) ? (int) $retornadoPorProducto[$idProducto] : 0;
                    $cantidadDisponible = max(0, $cantidadOriginal - $cantidadRetornada);
                    $nombreProducto = $productoInventario && isset($productoInventario["nombre"])
                        ? $productoInventario["nombre"]
                        : "Producto no disponible";
                  ?>
                  <tr>
                    <td><?php echo htmlspecialchars($nombreProducto, ENT_QUOTES, "UTF-8"); ?></td>
                    <td><?php echo htmlspecialchars(isset($producto["codigo"]) ? $producto["codigo"] : "", ENT_QUOTES, "UTF-8"); ?></td>
                    <td><?php echo $cantidadOriginal; ?></td>
                    <td><?php echo $cantidadRetornada; ?></td>
                    <td><?php echo $cantidadDisponible; ?></td>
                    <td>
                      <input type="number"
                             class="form-control cantidadEventoRetorno"
                             name="cantidadRetorno[<?php echo (int) $indice; ?>]"
                             value="0"
                             min="0"
                             max="<?php echo $cantidadDisponible; ?>"
                             step="1"
                             <?php echo $cantidadDisponible === 0 ? "readonly" : ""; ?>>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="box-footer">
          <button type="submit" class="btn btn-success">Crear evento de retorno</button>
        </div>
      </form>

      <?php ControladorFacturas::ctrCrearEventoRetorno(); ?>
    </div>
  </section>
</div>

<script>
document.addEventListener("input", function(event){
  if(!event.target.classList.contains("cantidadEventoRetorno")){
    return;
  }
  var maximo = parseInt(event.target.getAttribute("max"), 10) || 0;
  var cantidad = parseInt(event.target.value, 10);
  if(!Number.isInteger(cantidad) || cantidad < 0){
    event.target.value = 0;
  } else if(cantidad > maximo){
    event.target.value = maximo;
  }
});
</script>

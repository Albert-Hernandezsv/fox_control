<?php
date_default_timezone_set('America/El_Salvador');
?>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <ul>
		<?php
			if(isset($_SESSION)){
				echo '
				<li>
					<a href="dashboard"><i class="fa fa-home"></i> <span class="menu-text">Dashboard</span></a>
				</li>';
			}
			if($_SESSION["rol"] == "Admin"){
				echo '
				<li>
					<a href="usuarios"><i class="fa fa-users"></i> <span class="menu-text">Usuarios</span></a>
				</li>';
			}
			if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Bodega"){
				echo '
				<li>
					<a href="index.php?ruta=inventario&filtroNombre=todos&filtroTipo=todos&filtroCategoria=todos&filtroCodigo=todos&filtroStock=todos"><i class="fa fa-archive"></i> <span class="menu-text">Inventario</span></a>
				</li>';
			}
			if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Facturación" || $_SESSION["rol"] == "Contabilidad" || $_SESSION["rol"] == "Vendedor"){
				echo '
				<li>
					<a href="facturacion"><i class="fa fa-print"></i> <span class="menu-text">Facturación</span></a>
				</li>';
			}
			if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Facturación" || $_SESSION["rol"] == "Contabilidad" || $_SESSION["rol"] == "Vendedor"){
				echo '
				<li>
					<a href="compras"><i class="fa fa-shopping-cart"></i> <span class="menu-text">Compras</span></a>
				</li>';
			}
			if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Facturación" || $_SESSION["rol"] == "Contabilidad" || $_SESSION["rol"] == "Vendedor"){
				echo '
				<li>
					<a href="index.php?ruta=ventas&filtroFechaInicio=&filtroFechaFin='.date('Y-m-d').'"><i class="fa fa-university"></i> <span class="menu-text">Ventas</span></a>
				</li>';
			}
			if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
				echo '
				<li>
					<a href="contabilidad"><i class="fa fa-line-chart"></i> <span class="menu-text">Contabilidad</span></a>
				</li>';
			}
			if(isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok"){
				echo '
				<li>
					<a href="salir"><i class="fa fa-sign-out"></i> <span class="menu-text">Salir</span></a>
				</li>';
			}
		?>
    </ul>
</div>

<!-- Scripts adicionales -->
<script>
    // Aseguramos que el sidebar se colapse en móvil
    document.querySelector('.navbar-toggler').addEventListener('click', function () {
      document.getElementById('sidebar').classList.toggle('show');
    });
  </script>

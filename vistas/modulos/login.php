<?php
  $estadoLogin = ControladorUsuarios::ctrIngresoUsuarioInline();
  $usuarioIngresado = isset($_POST["ingUsuario"]) ? htmlspecialchars($_POST["ingUsuario"], ENT_QUOTES, "UTF-8") : "";
?>

<main class="login-shell">
  <section class="login-card">
    <div class="login-form-panel">
      <div class="login-form-content">
        <p class="login-eyebrow">Bienvenido</p>
        <h2>Iniciar sesi&oacute;n</h2>
        <p class="login-help">Ingresa tus credenciales para acceder al sistema.</p>

        <form method="post" role="form">
          <div class="form-group has-feedback">
            <label for="ingUsuario">Usuario</label>
            <input id="ingUsuario" type="text" class="form-control" placeholder="Usuario" name="ingUsuario" value="<?php echo $usuarioIngresado; ?>" autocomplete="username" autofocus required>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>

          <div class="form-group has-feedback">
            <label for="ingPassword">Contrase&ntilde;a</label>
            <input id="ingPassword" type="password" class="form-control" placeholder="Contrase&ntilde;a" name="ingPassword" autocomplete="current-password" required>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>

          <button type="submit" class="login-submit btn btn-block">Ingresar</button>

          <?php if($estadoLogin): ?>
            <p class="login-message login-message-<?php echo $estadoLogin["tipo"]; ?>" role="status">
              <?php echo $estadoLogin["mensaje"]; ?>
            </p>
          <?php endif; ?>

          <?php if($estadoLogin && $estadoLogin["tipo"] === "success"): ?>
            <script>
              window.setTimeout(function () {
                window.location.replace("dashboard");
              }, 700);
            </script>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <div class="login-brand-panel">
      <img src="vistas/img/logo.png" alt="Logo de FOX CONTROL" width="190" height="190">
      <h1>FOX CONTROL</h1>
      <p>El control de la administraci&oacute;n en tus manos</p>
      <span class="login-version">Versi&oacute;n 2.0</span>
    </div>
  </section>
</main>

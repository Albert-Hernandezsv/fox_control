<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top navfijo">
    <a class="navbar-brand" href="dashboard" style="display: flex; align-items: center;">
        <img src="vistas/img/logo.png" alt="logo" class="img-fluid"><h1 class="hlogo">FOX CONTROL</h1>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <p style="color: white; width: 500px; margin-top: 18px">
        <?php 
            if($_SESSION["foto"] == ""){
                echo '<img src="vistas/img/usuarios/default/anonimo.png" class="img-thumbnail" width="30px">'; echo("&nbsp&nbsp&nbsp&nbsp&nbsp");
            } else {
                echo '<img src="'.$_SESSION['foto'].'" width="40px">';
            }
            echo("&nbsp&nbsp&nbsp&nbsp&nbsp");
            echo($_SESSION["nombre"]);
            echo(" | "); echo($_SESSION["rol"])
        ?>
    </p>
</nav>
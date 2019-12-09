	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	  <a class="navbar-brand" href="#">
	  	<?php if (!isset($_GET["mensaje"])) {
	 	 	echo("Bienvenido <b>".$_SESSION["email"]."</b>");
	 	 } ?>
	  </a>

	  <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
	    <ul class="navbar-nav mr-auto">
	      <li class="nav-item ">
	      	<a class="nav-link" href="panel.php">Cliente</a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="panelProducto.php">Producto</a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="panelInventario.php">Inventario</a>
	      </li>
	      <li class="nav-item ">
	        <a class="nav-link" href="PanelVenta.php">Ventas</a>
	      </li>
	      <li class="nav-item ">
	        <a class="nav-link" href="panelDevolucion.php">Devoluciones</a>
	      </li>
	    </ul>
	    <div class="col-md-3" align="right">
 	 		<a class="nav-link" href='panel.php?close=ok'>Cerrar sesion</a>
 	 	</div>
	  </div>
	</nav>


 
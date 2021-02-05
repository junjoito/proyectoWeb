<?php
$error = false;
$config = include 'config.php';

try {
  $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
  $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

  if (isset($_POST['producto'])) {
    $consultaSQL = "SELECT * FROM productos WHERE producto LIKE '%" . $_POST['producto'] . "%'";
  } else {
    $consultaSQL = "SELECT productos.id, productos.producto, productos.cantidad, categorias.categoria FROM productos, categorias WHERE productos.fk_categoria = categorias.id";
  }

  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $alumnos = $sentencia->fetchAll();

} catch(PDOException $error) {
  $error= $error->getMessage();
}

$titulo = isset($_POST['producto']) ? 'Lista de productos (' . $_POST['producto'] . ')' : 'Lista de productos';
?>

<?php include "templates/header.php"; ?>

<?php
if ($error) {
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
          <?= $error ?>
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <a href="crear.php"  class="btn btn-success mt-4">Nuevo Producto</a>
      <hr>
      <form method="post" class="form-inline">
        <div class="form-group mr-3">
          <input type="text" id="producto" name="producto" placeholder="Buscar por producto" class="form-control">
        </div>
        
        <button type="submit" name="submit" class="btn btn-info">Buscar</button>
      </form>
    </div>
  </div>
</div>

<div class="container">
  <div class="row">
    <div class="col-xl-9">
      <h2 class="mt-3"><?= $titulo ?></h2>
      <table class="table table-bordered table-sm">
        <thead class="table-dark ">
          <tr>
            <th>#</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Categoria</th>      
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($alumnos && $sentencia->rowCount() > 0) {
            foreach ($alumnos as $fila) {
              ?>
              <tr>
                <td><?php echo $fila["id"]; ?></td>
                <td><?php echo $fila["producto"]; ?></td>
                <td><?php echo $fila["cantidad"]; ?></td>
                <td><?php echo $fila["categoria"]; ?></td>                
                <td>                  
                  <a href="<?= 'editar.php?id=' .$fila["id"] ?>"><button class="btn btn-info btn-sm">Actualizar</button></a>
                  <a href="<?= 'borrar.php?id=' .$fila["id"] ?>"><button class="btn btn-danger btn-sm">Borrar</button></a>
                </td>                
              </tr>
              <?php
            }
          }
          ?>
        <tbody>
      </table>
    </div>
  </div>
</div>

<?php include "templates/footer.php"; ?>
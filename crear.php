<?php
if (isset($_POST['submit'])) {
  $resultado = [
    'error' => false,
    'mensaje' => 'El producto ' .$_POST['nombre']. ' ha sido agregado con éxito'
  ];

  $config = include 'config.php';

  try {
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

    $producto = [
      "producto"   => $_POST['producto'],
      "cantidad" => $_POST['cantidad'],
      "fk_categoria"    => $_POST['fk_categoria'],
    ];

    $consultaSQL = "INSERT INTO productos (producto, cantidad, fk_categoria)";
    $consultaSQL .= "values (:" . implode(", :", array_keys($producto)) . ")";

    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute($producto);

  } catch(PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}
?>

<?php include 'templates/header.php'; ?>

<?php
if (isset($resultado)) {
  ?>
  <div class="container mt-3">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-<?= $resultado['error'] ? 'danger' : 'success' ?>" role="alert">
          <?= $resultado['mensaje'] ?>
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
      <h2 class="mt-4">Alta producto</h2>
      <hr>
      <form method="post">
        <div class="form-group">
          <label for="producto">Nombre del producto</label>
          <input type="text" name="producto" id="producto" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="cantidad">Cantidad</label>
          <input type="number" name="cantidad" id="cantidad" class="form-control" required>
        </div>
        <div class="form-group">   
          <label for="cantidad">Tipo de prudcto</label>       
          <select placeholder="Categoria" name="fk_categoria" required class="form-control" id="fk_categoria">
          <?php
            $config = include 'config.php';
          
            $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
            $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);                 
        
            $consultaSQL = "SELECT * FROM categorias";
            
            $sentencia = $conexion->prepare($consultaSQL);
            $sentencia->execute();

            $categorias = $sentencia->fetchAll();
            if ($categorias && $sentencia->rowCount() > 0) {
              foreach ($categorias as $fila) {
                ?>
                  <option value="<?php echo $fila["id"]; ?>"><?php echo $fila["categoria"]; ?></option>                
                <?php
              }
            }
          ?>
          </select>
        </div>
        <div class="form-group">
          
          <input type="submit" name="submit" class="btn btn-info" value="Enviar">
          <a class="btn btn-danger" href="index.php">Regresar al inicio</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>
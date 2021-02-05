<?php
include 'funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}

$config = include 'config.php';

$resultado = [
  'error' => false,
  'mensaje' => ''
];

if (!isset($_GET['id'])) {
  $resultado['error'] = true;
  $resultado['mensaje'] = 'El producto no existe';
}

if (isset($_POST['submit'])) {
  try {
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

    $producto = [
      "id"        => $_GET['id'],
      "producto"    => $_POST['producto'],
      "cantidad"  => $_POST['cantidad'],
      "fk_categoria"  => $_POST['fk_categoria']
    ];
    
    $consultaSQL = "UPDATE productos SET
        producto = :producto,
        cantidad = :cantidad,
        fk_categoria = :fk_categoria,        
        actualizado = NOW()
        WHERE id = :id";
    $consulta = $conexion->prepare($consultaSQL);
    $consulta->execute($producto);

  } catch(PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}

try {
  $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
  $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);
    
  $id = $_GET['id'];
  $consultaSQL = "SELECT * FROM productos WHERE id =" . $id;

  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $producto = $sentencia->fetch(PDO::FETCH_ASSOC);

  if (!$producto) {
    $resultado['error'] = true;
    $resultado['mensaje'] = 'No se ha encontrado el producto';
  }

} catch(PDOException $error) {
  $resultado['error'] = true;
  $resultado['mensaje'] = $error->getMessage();
}
?>

<?php require "templates/header.php"; ?>

<?php
if ($resultado['error']) {
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
          <?= $resultado['mensaje'] ?>
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php
if (isset($_POST['submit']) && !$resultado['error']) {
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-success" role="alert">
          El producto ha sido actualizado correctamente
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php
if (isset($producto) && $producto) {
  ?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2 class="mt-4">Editando el producto <?= escapar($producto['producto']) ?></h2>
        <hr>
        <form method="post">
          <div class="form-group">
            <label for="producto">Producto</label>
            <input type="text" name="producto" id="producto" value="<?= escapar($producto['producto']) ?>" class="form-control">
          </div>
          <div class="form-group">
            <label for="cantidad">Cantidad</label>
            <input type="text" name="cantidad" id="cantidad" value="<?= escapar($producto['cantidad']) ?>" class="form-control">
          </div>
          <div class="form-group">          
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
            <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
            <input type="submit" name="submit" class="btn btn-info" value="Actualizar">
            <a class="btn btn-danger" href="index.php">Regresar al inicio</a>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php require "templates/footer.php"; ?>
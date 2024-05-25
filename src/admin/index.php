<?php
require '../includes/funciones.php';
$auth = estaAutenticado();

if (!$auth) {
  header('Location: ../login.php');
  exit();
}



//CONSULTAR PROPIEDADES
require '../includes/config/database.php';
$db = conectarDB();

incluirTemplate('header', false);
$mensaje = $_GET['resultado'] ?? null;
// var_dump($mensaje);


//BORRAR PROPIEDAD

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = $_POST['id'];
  $id = filter_var($id, FILTER_VALIDATE_INT);
  if ($id) {
    $query = "SELECT imagen FROM propiedades WHERE id = $id";
    $resultado = mysqli_query($db, $query);
    $imagen = mysqli_fetch_assoc($resultado);
    $imagen = $imagen['imagen']; //Nombre del archivo actual.

    $carpetaImagenes = '../imagenes';
    if (!empty($imagen) && file_exists($carpetaImagenes . "/" . $imagen)) {
      unlink($carpetaImagenes . "/" . $imagen);
    }
    $query = "DELETE FROM propiedades WHERE id = $id";
    $resultado = mysqli_query($db, $query);
    if ($resultado) {
      $mensaje = 3;
    }
  }
}

$consulta = "SELECT * FROM propiedades";
$consultaPropiedades = mysqli_query($db, $consulta);
// var_dump($consultaPropiedades);


?>
<main class="alinear-elementos">
  <h1 class="text-4xl font-thin text-slate-600">Administrador</h1>
  <?php if ($mensaje == 1) {
    echo "<p class='text-2xl text-amber-500'>Propiedad creada correctamente!</p>";
  } elseif ($mensaje == 2) {
    echo "<p class='text-2xl text-amber-500'>Propiedad actualizada correctamente!</p>";
  } elseif ($mensaje == 3) {
    echo "<p class='text-2xl text-amber-500'>Propiedad eliminada correctamente!</p>";
  }
  ?>
  <nav class="flex gap-4">
    <a href="propiedades/crear.php" class="boton-naranja-inline">Crear Propiedad</a>

  </nav>
  <table class="min-w-full bg-white border border-gray-300">
    <thead>
      <tr>
        <th class="py-2 px-4 border-b">ID</th>
        <th class="py-2 px-4 border-b">Titulo</th>
        <th class="py-2 px-4 border-b">Imagen</th>
        <th class="py-2 px-4 border-b">Precio</th>
        <th class="py-2 px-4 border-b">Acciones</th>
      </tr>
    </thead>
    <tbody>

      <?php while ($row = mysqli_fetch_assoc($consultaPropiedades)) : ?>
        <tr>
          <td class="py-2 px-4 border-b"> <?php echo $row['id'] ?> </td>
          <td class="py-2 px-4 border-b"><?php echo $row['titulo'] ?></td>
          <td class="py-2 px-4 border-b"><img src="../imagenes/<?php echo $row['imagen'] ?> " alt="" class="h-48"></td>
          <td class="py-2 px-4 border-b">$<?php echo $row['precio'] ?></td>
          <td class="py-2 px-4 border-b">
            <a href="../admin/propiedades/actualizar.php?id=<?php echo $row['id'] ?>" class="text-blue-500 hover:text-blue-700">Actualizar</a>
            <form method="POST">
              <form method="POST">
                <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                <input type="submit" value="Eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar esta propiedad?')">
              </form>
          </td>
        </tr>
      <?php endwhile ?>


      </tr>
    </tbody>
  </table>

</main>

<?php
//cerrar bd
mysqli_close($db);
incluirTemplate('footer');
?>
<?php
require '../../includes/funciones.php';
$auth = estaAutenticado();

if (!$auth) {
  header('Location: ../../login.php');
  exit();
}
require '../../includes/config/database.php';
$db = conectarDB();
incluirTemplate('header');

$consulta = "SELECT * FROM vendedores";
$resultadoConsulta = mysqli_query($db, $consulta);

$errores = [];
$resultado = '';
$titulo = "";
$precio = "";
$descripcion = "";
$habitaciones = "";
$wc = "";
$plazas = "";
$vendedor = "";
$fechaCreacion = date('Y-m-d');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  /*   echo "<pre>";
  var_dump($_POST);
  echo "</pre>";

  echo "<pre>";
  var_dump($_FILES);
  echo "</pre>";
  exit;
 */
  /* SANITIZACIÓN */
  $titulo = mysqli_real_escape_string($db, $_POST['titulo']);
  $precio = mysqli_real_escape_string($db, $_POST['precio']);
  $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);
  $habitaciones = mysqli_real_escape_string($db, $_POST['habitaciones']);
  $wc = mysqli_real_escape_string($db, $_POST['wc']);
  $plazas = mysqli_real_escape_string($db, $_POST['plazas']);
  $vendedor = mysqli_real_escape_string($db, $_POST['vendedores_id']);

  /* validar */
  $imagenfile = $_FILES['imagen'];

  var_dump($imagenfile);


  if (!$titulo) {
    $errores[] = 'Debes añadir un titulo';
  }
  if (!$precio) {
    $errores[] = 'Debes añadir un precio';
  } elseif (!is_numeric($precio)) {
    $errores[] = 'El precio debe ser un número';
  } elseif ($precio <= 0) {
    $errores[] = 'El precio debe ser mayor que cero';
  }
  if (strlen($descripcion) < 50) {
    $errores[] = 'Debes añadir una descripción y debe contener al menos 50 caracteres';
  }
  if (!$habitaciones) {
    $errores[] = 'Debes añadir el número de habitaciones';
  }
  if (!$wc) {
    $errores[] = 'Debes añadir el número de baños';
  }
  if (!$plazas) {
    $errores[] = 'Debes añadir el número de plazas';
  }
  if (!$vendedor) {
    $errores[] = 'Debes seleccionar un vendedor';
  }
  if (!$imagenfile['name']) {
    $errores[] = 'Debes añadir una imagen';
  } elseif ($imagenfile['size'] > 1 * 1024 * 1000) {
    $errores[] = 'es muy grande';
  }


  if (empty($errores)) {

    // Generar un nombre único para la imagen
    $nombreImagen = md5(uniqid(rand(), true));

    // Crear carpeta si no existe
    $carpetaImagenes = '../../imagenes';
    if (!is_dir($carpetaImagenes)) {
      mkdir($carpetaImagenes, 0777, true);
    }

    // Obtener la extensión del archivo subido
    $extension = pathinfo($imagenfile['name'], PATHINFO_EXTENSION);

    // Mover el archivo subido a la carpeta especificada con el nombre generado y la extensión original
    move_uploaded_file($imagenfile['tmp_name'], $carpetaImagenes . "/" . $nombreImagen . "." . $extension);

    $nombreArchivo = $nombreImagen . "." . $extension;

    //insertar base de datos
    $query = "INSERT INTO propiedades (titulo, precio, descripcion, habitaciones, wc, estacionamiento, vendedores_id, creado, imagen) VALUES ('$titulo','$precio', '$descripcion', '$habitaciones', '$wc', '$plazas', '$vendedor', '$fechaCreacion', '$nombreArchivo')";
    /* echo $query; */
    $resultado = mysqli_query($db, $query);

    if ($resultado) {
      $host = $_SERVER['HTTP_HOST'];
      $extra = '/bienesraices/src/admin?resultado=1';
      header("Location: http://$host$extra");
      exit;
    }
  }
}

?>
<main class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
  <?php foreach ($errores as $error) : ?>
    <p class="text-red-500 font-bold text-base"><?php echo $error; ?></p>
  <?php endforeach; ?>
  <?php
  if ($resultado) {
    echo "<div class='bg-green-500 mt-8 p-4 rounded-lg text-white'><i class='bi bi-check-circle'><span class='ml-4 text-2xl'>Registro agregado correctamente a la base de datos</span></i></div>";
    $resultado = '';
    $titulo = "";
    $precio = "";
    $descripcion = "";
    $habitaciones = "";
    $wc = "";
    $plazas = "";
    $vendedor = "";
  }
  ?>

  <h1 class="text-4xl font-thin text-gray-800 mb-6">Crear Propiedad</h1>
  <a href="/bienesraices/src/admin/index.php" class="inline-block mb-6 px-6 py-2 bg-orange-500 text-white font-semibold rounded-md hover:bg-orange-600">Volver</a>
  <form action="" method="post" class="space-y-6" enctype="multipart/form-data">
    <fieldset class="border border-gray-300 p-4 rounded-md">
      <legend class="text-lg font-semibold text-gray-800">Información general</legend>
      <div class="mt-4">
        <label for="titulo" class="block text-gray-700">Titulo</label>
        <input type="text" id="titulo" name="titulo" value='<?php echo $titulo ?>' placeholder="Titulo de la propiedad" class="mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200 focus:border-blue-500 w-full">
      </div>
      <div class="mt-4">
        <label for="precio" class="block text-gray-700">Precio</label>
        <input type="text" id="precio" value='<?php echo $precio ?>' name="precio" placeholder="Precio de la propiedad" class="mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200 focus:border-blue-500 w-full">
      </div>
      <div class="mt-4">
        <label for="imagen" class="block text-gray-700">Imagen</label>
        <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png" class="mt-1 w-full">
      </div>
      <div class="mt-4">
        <label for="descripcion" class="block text-gray-700">Descripción</label>
        <textarea id="descripcion" name="descripcion" placeholder="Descripción de la propiedad" class="mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200 focus:border-blue-500 w-full"><?php echo $descripcion ?></textarea>
      </div>
    </fieldset>

    <fieldset class="border border-gray-300 p-4 rounded-md">
      <legend class="text-lg font-semibold text-gray-800">Información de la propiedad</legend>
      <div class="mt-4">
        <label for="habitaciones" class="block text-gray-700">Habitaciones</label>
        <input type="number" id="habitaciones" name="habitaciones" value="<?php echo $habitaciones ?>" placeholder="Número de habitaciones ej.3" min="1" class="mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200 focus:border-blue-500 w-full">
      </div>
      <div class="mt-4">
        <label for="wc" class="block text-gray-700">Baños</label>
        <input type="number" id="wc" name="wc" value="<?php echo $wc; ?>" placeholder="Número de baños ej.3" min="1" class="mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200 focus:border-blue-500 w-full">
      </div>
      <div class="mt-4">
        <label for="plazas" class="block text-gray-700">Plazas</label>
        <input type="number" id="plazas" name="plazas" value="<?php echo $plazas ?>" placeholder="Número de plazas ej.3" min="1" class="mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200 focus:border-blue-500 w-full">
      </div>
    </fieldset>

    <fieldset class="border border-gray-300 p-4 rounded-md">
      <legend class="text-lg font-semibold text-gray-800">Vendedor</legend>
      <select id="vendedores_id" name="vendedores_id" class="mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200 focus:border-blue-500 w-full">
        <option value="">Selecciona vendedor</option>
        <?php while ($row = mysqli_fetch_assoc($resultadoConsulta)) : ?>
          <option <?php echo $vendedor === $row['id'] ? 'selected' : ''; ?> value='<?php echo $row['id'] ?>'> <?php echo $row['nombre'] . " " . $row['apellido'] ?> </option>
        <?php endwhile ?>
      </select>
    </fieldset>

    <button type="submit" class="w-full mt-8 px-6 py-3 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600">Crear Propiedad</button>
  </form>
</main>
<?php incluirTemplate('footer'); ?>
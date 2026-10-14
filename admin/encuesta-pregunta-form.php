<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$esEdicion = false;
$pregunta = [
    'id'               => 0,
    'orden'            => 0,
    'texto'            => '',
    'tipo'             => 'texto',
    'escala_min'       => 1,
    'escala_max'       => 10,
    'escala_label_min' => '',
    'escala_label_max' => '',
    'opciones'         => '',
    'obligatoria'      => 1,
    'activa'           => 1,
];

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM encuesta_preguntas WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $fila = $stmt->fetch();
    if ($fila) {
        $pregunta  = $fila;
        $esEdicion = true;
    } else {
        header('Location: encuesta-preguntas.php?error=Pregunta no encontrada');
        exit;
    }
}

// Sugerir el siguiente orden si es nueva
if (!$esEdicion) {
    $maxOrden = (int) $pdo->query("SELECT COALESCE(MAX(orden), 0) FROM encuesta_preguntas")->fetchColumn();
    $pregunta['orden'] = $maxOrden + 1;
}

// Valores seguros para los campos numéricos
$escalaMinSafe = (int)($pregunta['escala_min'] ?? 1);
if ($escalaMinSafe < 0) $escalaMinSafe = 1;

$escalaMaxSafe = (int)($pregunta['escala_max'] ?? 10);
if ($escalaMaxSafe < 1) $escalaMaxSafe = 10;

$ordenSafe = (int)($pregunta['orden'] ?? 0);
if ($ordenSafe < 0) $ordenSafe = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $esEdicion ? 'Editar' : 'Nueva' ?> pregunta · Panel Transmidiesel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include 'partials/sidebar.php'; ?>

<main class="admin-main">
  <header class="admin-topbar">
    <h1><?= $esEdicion ? 'Editar pregunta' : 'Nueva pregunta' ?></h1>
    <a href="logout.php" class="btn-logout">Cerrar sesión</a>
  </header>

  <section class="admin-content">

    <div class="page-head">
      <div>
        <h2><?= $esEdicion ? 'Editar pregunta' : 'Crear pregunta' ?></h2>
        <p class="sub">Los cambios se reflejan automáticamente en el formulario público.</p>
      </div>
      <a href="encuesta-preguntas.php" class="btn btn-secondary">← Volver</a>
    </div>

    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form class="form-card" action="encuesta-pregunta-guardar.php" method="POST" novalidate>
      <input type="hidden" name="id" value="<?= (int)$pregunta['id'] ?>">

      <!-- Texto de la pregunta -->
      <div class="form-group">
        <label for="texto">Pregunta <span style="color:#d9383a">*</span></label>
        <textarea id="texto" name="texto" required maxlength="255" style="min-height:80px;"
                  placeholder="Ej: ¿Qué tanto recomendarías nuestro servicio?"><?= htmlspecialchars($pregunta['texto']) ?></textarea>
        <small class="help">Máximo 255 caracteres.</small>
      </div>

      <!-- Tipo de pregunta -->
      <div class="form-row-2">
        <div class="form-group">
          <label for="tipo">Tipo de pregunta <span style="color:#d9383a">*</span></label>
          <select id="tipo" name="tipo" required onchange="toggleTipoFields()">
            <option value="texto"  <?= $pregunta['tipo'] === 'texto'  ? 'selected' : '' ?>>📝 Texto libre</option>
            <option value="escala" <?= $pregunta['tipo'] === 'escala' ? 'selected' : '' ?>>⭐ Escala numérica</option>
            <option value="opcion" <?= $pregunta['tipo'] === 'opcion' ? 'selected' : '' ?>>📋 Opción múltiple</option>
          </select>
        </div>

        <div class="form-group">
          <label for="orden">Orden</label>
          <input type="number" id="orden" name="orden" min="0"
                 value="<?= $ordenSafe ?>">
          <small class="help">Números más bajos aparecen primero.</small>
        </div>
      </div>

      <!-- Campos SOLO para tipo "escala" -->
      <div id="camposEscala" style="display:none;">
        <div class="form-row-2">
          <div class="form-group">
            <label for="escala_min">Valor mínimo</label>
            <input type="number" id="escala_min" name="escala_min" min="0" max="100"
                   value="<?= $escalaMinSafe ?>">
          </div>
          <div class="form-group">
            <label for="escala_max">Valor máximo</label>
            <input type="number" id="escala_max" name="escala_max" min="1" max="100"
                   value="<?= $escalaMaxSafe ?>">
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="escala_label_min">Etiqueta del extremo inferior</label>
            <input type="text" id="escala_label_min" name="escala_label_min" maxlength="100"
                   value="<?= htmlspecialchars($pregunta['escala_label_min'] ?? '') ?>"
                   placeholder="Ej: No lo recomendaría">
          </div>
          <div class="form-group">
            <label for="escala_label_max">Etiqueta del extremo superior</label>
            <input type="text" id="escala_label_max" name="escala_label_max" maxlength="100"
                   value="<?= htmlspecialchars($pregunta['escala_label_max'] ?? '') ?>"
                   placeholder="Ej: Seguro lo recomendaría">
          </div>
        </div>
      </div>

      <!-- Campos SOLO para tipo "opcion" -->
      <div id="camposOpcion" style="display:none;">
        <div class="form-group">
          <label for="opciones">Opciones <span style="color:#d9383a">*</span></label>
          <textarea id="opciones" name="opciones" style="min-height:120px;"
                    placeholder="Una opción por línea:&#10;Sí&#10;No&#10;Tal vez"><?= htmlspecialchars($pregunta['opciones'] ?? '') ?></textarea>
          <small class="help">Escribe una opción por línea. Cada línea será un botón de selección.</small>
        </div>
      </div>

      <!-- Obligatoria + Activa -->
      <div class="form-row-2" style="margin-top:20px;">
        <div class="form-group">
          <div class="checkbox-row">
            <input type="checkbox" id="obligatoria" name="obligatoria" value="1"
                   <?= $pregunta['obligatoria'] ? 'checked' : '' ?>>
            <label for="obligatoria">Obligatoria (el cliente debe responderla)</label>
          </div>
        </div>
        <div class="form-group">
          <div class="checkbox-row">
            <input type="checkbox" id="activa" name="activa" value="1"
                   <?= $pregunta['activa'] ? 'checked' : '' ?>>
            <label for="activa">Activa (visible en el formulario público)</label>
          </div>
        </div>
      </div>

      <div class="form-actions">
        <a href="encuesta-preguntas.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">
          <?= $esEdicion ? 'Guardar cambios' : 'Crear pregunta' ?>
        </button>
      </div>

    </form>

  </section>
</main>

<script>
function toggleTipoFields() {
  const tipo = document.getElementById('tipo').value;
  document.getElementById('camposEscala').style.display = (tipo === 'escala') ? 'block' : 'none';
  document.getElementById('camposOpcion').style.display = (tipo === 'opcion') ? 'block' : 'none';
}
// Ejecutar al cargar
toggleTipoFields();
</script>

</body>
</html>
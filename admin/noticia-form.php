<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$esEdicion = false;
$noticia = [
    'id'          => 0,
    'titulo'      => '',
    'descripcion' => '',
    'imagen'      => '',
    'enlace'      => '',
    'orden'       => 999,
    'activa'      => 1,
];

// Cargar noticia si es edición
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $fila = $stmt->fetch();
    if ($fila) {
        $noticia   = $fila;
        $esEdicion = true;
    } else {
        header('Location: noticias.php?error=Noticia no encontrada');
        exit;
    }
}

// Cargar TODAS las noticias para el selector de posición
$stmt = $pdo->query("SELECT id, titulo, orden FROM noticias ORDER BY orden ASC, id ASC");
$todas = $stmt->fetchAll();
$total = count($todas);

// Si estamos editando, quitamos la noticia actual de la lista del selector
$otras = array_values(array_filter($todas, fn($n) => (int)$n['id'] !== (int)$noticia['id']));
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $esEdicion ? 'Editar' : 'Nueva' ?> noticia · Panel Transmidiesel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
<style>
  .posicion-info {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    background: rgba(15,62,104,0.06);
    border: 1px solid rgba(15,62,104,0.15);
    border-radius: 999px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--accent);
    margin-bottom: 10px;
  }
  .posicion-info .num {
    background: var(--accent);
    color: #fff;
    padding: 2px 9px;
    border-radius: 999px;
    font-family: 'Space Grotesk', sans-serif;
    font-size: 13px;
  }
  .posicion-lista {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-top: 10px;
    padding: 14px;
    background: #f9fafc;
    border: 1px solid var(--border);
    border-radius: 12px;
    max-height: 260px;
    overflow-y: auto;
  }
  .posicion-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    transition: border-color .2s, background .2s;
  }
  .posicion-item:hover {
    border-color: var(--accent-bright);
    background: #f0f5fa;
  }
  .posicion-item.selected {
    border-color: var(--accent);
    background: rgba(15,62,104,0.05);
    box-shadow: 0 0 0 2px rgba(15,62,104,0.12);
  }
  .posicion-item input[type="radio"] {
    accent-color: var(--accent);
    cursor: pointer;
  }
  .posicion-item .orden-num {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 26px;
    height: 22px;
    padding: 0 6px;
    background: var(--accent-dim);
    color: var(--accent);
    border-radius: 6px;
    font-family: 'Space Grotesk', sans-serif;
    font-size: 12px;
    font-weight: 700;
  }
  .posicion-item .titulo {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .posicion-item.al-final .titulo {
    font-style: italic;
    color: var(--text-muted);
  }
</style>
</head>
<body>

<?php include 'partials/sidebar.php'; ?>

<main class="admin-main">
  <header class="admin-topbar">
    <h1><?= $esEdicion ? 'Editar noticia' : 'Nueva noticia' ?></h1>
    <a href="logout.php" class="btn-logout">Cerrar sesión</a>
  </header>

  <section class="admin-content">

    <div class="page-head">
      <div>
        <h2><?= $esEdicion ? 'Editar noticia' : 'Crear noticia' ?></h2>
        <p class="sub">Los cambios se reflejan automáticamente en el sitio.</p>
      </div>
      <a href="noticias.php" class="btn btn-secondary">← Volver</a>
    </div>

    <form class="form-card" action="noticia-guardar.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= (int)$noticia['id'] ?>">

      <div class="form-group">
        <label for="titulo">Título <span style="color:#d9383a">*</span></label>
        <input type="text" id="titulo" name="titulo" required maxlength="255"
               value="<?= htmlspecialchars($noticia['titulo']) ?>"
               placeholder="Ej: Transmidiesel en OXE Suecia">
      </div>

      <div class="form-group">
        <label for="descripcion">Descripción <span style="color:#d9383a">*</span></label>
        <textarea id="descripcion" name="descripcion" required
                  placeholder="Puedes usar &lt;br&gt;&lt;br&gt; para separar párrafos."><?= htmlspecialchars($noticia['descripcion']) ?></textarea>
        <small class="help">Acepta HTML básico: &lt;br&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;a&gt;</small>
      </div>

      <div class="form-group">
        <label for="enlace">Enlace externo <span class="help">(opcional)</span></label>
        <input type="url" id="enlace" name="enlace" maxlength="500"
               value="<?= htmlspecialchars($noticia['enlace']) ?>"
               placeholder="https://www.linkedin.com/feed/update/...">
        <small class="help">Si lo dejas vacío, la tarjeta no será clickeable.</small>
      </div>

      <!-- ============================================================
           POSICIÓN EN LA LISTA
           El número se asigna solo, tú solo eliges antes de qué noticia va.
           ============================================================ -->
      <div class="form-group">
        <label>📌 Posición en la lista</label>

        <?php if ($esEdicion): ?>
          <div class="posicion-info">
            Actualmente está en la posición
            <span class="num">#<?= (int)$noticia['orden'] ?></span>
            de <?= $total ?>
          </div>
        <?php endif; ?>

        <?php if (empty($otras)): ?>
          <p class="help" style="margin:0;">Es la primera noticia del sitio. Se le asignará la posición <strong>#1</strong>.</p>
        <?php else: ?>
          <small class="help" style="display:block;margin-bottom:8px;">
            Elige antes de qué noticia quieres que aparezca. El sistema le asignará el número correcto automáticamente.
          </small>

          <div class="posicion-lista">
            <label class="posicion-item <?= ((int)$noticia['orden'] === 1) ? 'selected' : '' ?>">
              <input type="radio" name="posicion" value="1"
                     <?= ((int)$noticia['orden'] === 1) ? 'checked' : '' ?>>
              <span class="orden-num">#1</span>
              <span class="titulo"><em>Al principio (antes de todas)</em></span>
            </label>

            <?php foreach ($otras as $i => $n): ?>
              <?php
                // Posición "después de esta noticia"
                $posDespues = $i + 2; // porque #1 es "antes de la primera"
                // Seleccionada si su orden actual cae justo en esta posición
                $selected = ((int)$noticia['orden'] === $posDespues);
              ?>
              <label class="posicion-item <?= $selected ? 'selected' : '' ?>">
                <input type="radio" name="posicion" value="<?= $posDespues ?>"
                       <?= $selected ? 'checked' : '' ?>>
                <span class="orden-num">#<?= $posDespues ?></span>
                <span class="titulo"><?= htmlspecialchars($n['titulo']) ?></span>
              </label>
            <?php endforeach; ?>

            <label class="posicion-item al-final <?= ((int)$noticia['orden'] > $total) ? 'selected' : '' ?>">
              <input type="radio" name="posicion" value="<?= $total + 1 ?>"
                     <?= ((int)$noticia['orden'] > $total) ? 'checked' : '' ?>>
              <span class="orden-num">#<?= $total + 1 ?></span>
              <span class="titulo"><em>Al final (después de todas)</em></span>
            </label>
          </div>
        <?php endif; ?>

        <!-- Campo oculto que guarda la posición elegida -->
        <input type="hidden" name="orden" id="ordenHidden" value="<?= (int)$noticia['orden'] ?>">
      </div>

      <div class="form-group">
        <label>Estado</label>
        <div class="checkbox-row" style="width:100%;">
          <input type="checkbox" id="activa" name="activa" value="1"
                 <?= $noticia['activa'] ? 'checked' : '' ?>>
          <label for="activa">Noticia activa (visible en el sitio)</label>
        </div>
      </div>

      <div class="form-group">
        <label for="imagen">Imagen <span class="help">(jpg, png, webp — máx 3 MB)</span></label>
        <input type="file" id="imagen" name="imagen" accept="image/*"
               onchange="previewImage(event)">
        <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($noticia['imagen']) ?>">
        <div class="img-preview" id="imgPreview">
          <?php if ($noticia['imagen']): ?>
            <img src="../<?= htmlspecialchars($noticia['imagen']) ?>" alt="Preview"
                 onerror="this.parentNode.innerHTML='<div class=\'no-img\'>Imagen no encontrada</div>';">
          <?php else: ?>
            <div class="no-img">Sin imagen</div>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-actions">
        <a href="noticias.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">
          <?= $esEdicion ? 'Guardar cambios' : 'Crear noticia' ?>
        </button>
      </div>

    </form>

  </section>
</main>

<script>
function previewImage(e) {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = ev => {
    document.getElementById('imgPreview').innerHTML =
      '<img src="' + ev.target.result + '" alt="Preview">';
  };
  reader.readAsDataURL(file);
}

// Al cambiar la posición, actualizar el campo oculto "orden"
document.querySelectorAll('input[name="posicion"]').forEach(radio => {
  radio.addEventListener('change', () => {
    document.getElementById('ordenHidden').value = radio.value;

    // Marcar visualmente el item seleccionado
    document.querySelectorAll('.posicion-item').forEach(it => it.classList.remove('selected'));
    radio.closest('.posicion-item').classList.add('selected');
  });
});
</script>

</body>
</html>
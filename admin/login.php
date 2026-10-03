<?php
// =========================================================
// Login del panel de administración
// =========================================================

session_start();

// Si ya está logueado, va directo al panel
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usuario === '' || $password === '') {
        $error = 'Completa todos los campos.';
    } else {
        $stmt = $pdo->prepare("SELECT id, usuario, password_hash, nombre FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Login exitoso
            $_SESSION['admin_id']     = $user['id'];
            $_SESSION['admin_usuario'] = $user['usuario'];
            $_SESSION['admin_nombre']  = $user['nombre'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
            // Pequeña pausa para dificultar ataques de fuerza bruta
            usleep(400000);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin · Transmidiesel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  * { box-sizing: border-box; }
  body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Inter', sans-serif;
    background:
      radial-gradient(ellipse 60% 40% at 15% 10%, rgba(15,62,104,0.10), transparent 60%),
      radial-gradient(ellipse 50% 40% at 85% 20%, rgba(120,140,160,0.08), transparent 60%),
      linear-gradient(180deg, #ffffff 0%, #f5f7fa 100%);
    color: #1a1a1a;
  }
  .login-box {
    width: 100%;
    max-width: 400px;
    padding: 42px 38px;
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(18px);
    border: 1px solid rgba(15,62,104,0.10);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(15,62,104,0.12);
  }
  .login-box h1 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 26px;
    margin: 0 0 6px;
    color: #0f3e68;
  }
  .login-box .sub {
    color: #5a5f66;
    font-size: 14px;
    margin: 0 0 28px;
  }
  .form-group {
    margin-bottom: 18px;
  }
  .form-group label {
    display: block;
    font-size: 13.5px;
    font-weight: 600;
    margin-bottom: 7px;
    color: #1a1a1a;
  }
  .form-group input {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid rgba(15,62,104,0.18);
    border-radius: 12px;
    font-size: 14.5px;
    font-family: inherit;
    background: #fff;
    transition: border-color .25s, box-shadow .25s;
  }
  .form-group input:focus {
    outline: none;
    border-color: #2f6fa8;
    box-shadow: 0 0 0 4px rgba(47,111,168,0.15);
  }
  .btn-login {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(180deg, #2f6fa8, #0f3e68);
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    box-shadow: 0 10px 30px rgba(15,62,104,0.28);
    transition: transform .2s, box-shadow .2s;
  }
  .btn-login:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 36px rgba(15,62,104,0.38);
  }
  .btn-login:active { transform: translateY(0); }
  .error-msg {
    padding: 12px 16px;
    margin-bottom: 20px;
    border-radius: 10px;
    background: rgba(211,47,47,0.08);
    border: 1.5px solid rgba(211,47,47,0.35);
    color: #b71c1c;
    font-size: 13.5px;
  }
</style>
</head>
<body>
  <form class="login-box" method="POST" autocomplete="off">
    <h1>Panel Administrativo</h1>
    <p class="sub">Transmidiesel S.A.S</p>

    <?php if ($error): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="form-group">
      <label for="usuario">Usuario</label>
      <input type="text" id="usuario" name="usuario" required autofocus>
    </div>

    <div class="form-group">
      <label for="password">Contraseña</label>
      <input type="password" id="password" name="password" required>
    </div>

    <button type="submit" class="btn-login">Iniciar sesión</button>
  </form>
</body>
</html>
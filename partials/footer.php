<?php
// =========================================================
// partials/footer.php
// Footer común a todas las páginas.
// Los teléfonos se leen desde `configuracion`.
// Los certificados se cargan vía footer-certs.js.
// Requiere: $base (ruta relativa a la raíz del proyecto)
// =========================================================

require_once __DIR__ . '/../includes/config_loader.php';

$base              = $base ?? './';
$telefonoNacional  = cfg('telefono_nacional', '+57 316 877 5212');
$telefonoCartagena = '+57 316 877 5207';
?>
<!-- ================= FOOTER ================= -->
<footer>
  <div class="wrap">

    <div class="footer-grid">
      <div>
        <div class="logo-wrap" style="margin-bottom:16px;">
          <div class="logo-box">
            <img src="<?= $base ?>logos/Logo.png" alt="Transmidiesel" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="logo-fallback" style="display:none;">LOGO<br>.PNG</div>
          </div>
        </div>
        <p style="color:var(--metal-light);font-size:14px;max-width:320px;">
          Líder en importación y comercialización de equipos industriales y servicios de mantenimiento, con respaldo de marcas reconocidas globalmente.
        </p>
      </div>
      <div>
        <h4>Navegación</h4>
        <ul>
          <li><a href="<?= $base ?>">Inicio</a></li>
          <li><a href="<?= $base ?>quienes-somos/">Quiénes Somos</a></li>
          <li><a href="<?= $base ?>servicios/">Servicios</a></li>
          <li><a href="<?= $base ?>productos/">Productos</a></li>
          <li><a href="<?= $base ?>empleados/">Empleados</a></li>
          <li><a href="<?= $base ?>contactenos/">Contáctenos</a></li>
        </ul>
      </div>
      <div>
        <h4>Sede Cali</h4>
        <ul>
          <li>Carrera 1 # 50N-89</li>
          <li>Barrio Evaristo García</li>
          <li><a href="tel:+57<?= preg_replace('/\D/', '', $telefonoNacional) ?>"><?= htmlspecialchars($telefonoNacional) ?></a></li>
        </ul>
      </div>
      <div>
        <h4>Agencia Cartagena</h4>
        <ul>
          <li>Diagonal 22 # 37 – 54</li>
          <li>Av Crisanto Luque — Barrio El Bosque</li>
          <li><a href="tel:+57<?= preg_replace('/\D/', '', $telefonoCartagena) ?>"><?= htmlspecialchars($telefonoCartagena) ?></a></li>
        </ul>
      </div>
    </div>

    <!-- Fila de certificados — se llena dinámicamente desde footer-certs.js -->
    <div class="footer-certs-strip">
      <div class="footer-certs" id="footerCerts">
        <!-- Se llena dinámicamente desde api/certificaciones.php -->
      </div>
    </div>

    <div class="footer-bottom">
      <span>© 2026 Transmidiesel S.A.S. Todos los derechos reservados.</span>
      <div class="social-row" aria-label="Redes sociales Transmidiesel">
        <a href="https://www.facebook.com/Transmidiesel" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M13.5 22v-8h2.7l.4-3.1h-3.1V8.9c0-.9.3-1.5 1.6-1.5h1.7V4.6c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.5-4 4.1v2.3H7.6V14h2.8v8h3.1z"/></svg>
        </a>
        <a href="https://www.instagram.com/transmidiesel/" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
        </a>
        <a href="https://www.linkedin.com/company/transmidiesel/" target="_blank" rel="noopener" aria-label="LinkedIn" title="LinkedIn">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M4.98 3.5A2.5 2.5 0 1 0 5 8.5a2.5 2.5 0 0 0-.02-5zM3 9.5h4V21H3V9.5zm6.5 0h3.8v1.6h.05c.53-1 1.83-2.05 3.77-2.05 4.03 0 4.78 2.65 4.78 6.1V21h-4v-5.2c0-1.24-.02-2.84-1.73-2.84-1.73 0-2 1.35-2 2.75V21h-4V9.5z"/></svg>
        </a>
        <a href="https://www.tiktok.com/@transmidiesel" target="_blank" rel="noopener" aria-label="TikTok" title="TikTok">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M16.5 3c.3 1.9 1.4 3.2 3.3 3.5v2.4c-1.2.1-2.3-.2-3.3-.9v6.4c0 3.3-2.6 5.6-5.6 5.6S5.3 17.7 5.3 14.6c0-2.9 2.2-5.3 5.2-5.3.3 0 .5 0 .8.1v2.5c-.3-.1-.5-.1-.8-.1-1.5 0-2.7 1.2-2.7 2.8 0 1.6 1.2 2.7 2.7 2.7 1.5 0 2.7-1.1 2.7-2.7V3h3.3z"/></svg>
        </a>
      </div>
    </div>
  </div>
</footer>
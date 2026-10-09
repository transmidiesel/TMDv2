<?php
// =========================================================
// partials/whatsapp.php
// Módulo flotante de WhatsApp + botón "volver arriba".
// El número se lee desde `configuracion` (telefono_whatsapp).
// Requiere: $base (ruta relativa a la raíz del proyecto)
// =========================================================

require_once __DIR__ . '/../includes/config_loader.php';

$base             = $base ?? './';
$telefonoWhatsApp = preg_replace('/\D/', '', cfg('telefono_whatsapp', '573168775212'));
$linkWA           = 'https://wa.me/' . $telefonoWhatsApp;
?>
<!-- ================= WHATSAPP MODULE ================= -->
<div class="wa-float" id="waFloat">

  <div class="wa-panel glass" id="waPanel">
    <div class="wa-head">
      <div class="wa-logo">
        <img src="<?= $base ?>logos/favicon_io/android-chrome-512x512.png" alt="Transmidiesel" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="logo-fallback" style="display:none;font-size:8px;">LOGO</div>
      </div>
      <div>
        <div class="wa-title">TMD - Línea Nacional</div>
        <div class="wa-sub">Hola! Somos Transmidiesel S.A.S.</div>
      </div>
    </div>
    <div class="wa-body">
      <div class="wa-bubble"><strong>Hola! somos Transmidiesel S.A.S.</strong><br>Es un placer asesorarte; ¿cómo podemos ayudarte?</div>
      <a href="<?= $linkWA ?>" target="_blank" class="neu-btn primary magnetic" style="width:100%;justify-content:center;">Iniciar conversación</a>
    </div>
  </div>

  <button class="back-top magnetic" id="backTop" aria-label="Volver arriba" title="Volver arriba">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M12 19V5"/>
      <path d="M5 12l7-7 7 7"/>
    </svg>
  </button>

  <button class="wa-fab magnetic" id="waFab" aria-label="Abrir WhatsApp">
    <svg viewBox="0 0 32 32" fill="none"><path d="M16 3C9 3 3.3 8.6 3.3 15.5c0 2.6.8 5 2.1 7L3 29l6.7-2.3c1.9 1 4.1 1.6 6.3 1.6 7 0 12.7-5.6 12.7-12.5S23 3 16 3z" fill="#ffffff"/><path d="M12.1 10.4c-.3-.7-.6-.7-.9-.7h-.7c-.3 0-.7.1-1 .5-.3.4-1.3 1.2-1.3 3s1.3 3.5 1.5 3.7c.2.3 2.5 4 6.2 5.5 3 1.2 3.6 1 4.3.9.7-.1 2.1-.8 2.4-1.6.3-.8.3-1.5.2-1.6-.1-.2-.3-.3-.7-.5s-2.1-1.1-2.4-1.2c-.3-.1-.6-.2-.8.2-.2.4-.9 1.2-1.1 1.4-.2.2-.4.3-.7.1-.4-.2-1.5-.6-2.8-1.8-1-1-1.7-2.1-1.9-2.5-.2-.4 0-.6.2-.8l.5-.6c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.6 0-.2-.7-1.9-1-2.7z" fill="var(--accent)"/></svg>
  </button>

  <div class="wa-socials" id="waSocials" aria-label="Redes sociales Transmidiesel">
    <a href="https://www.facebook.com/Transmidiesel" target="_blank" rel="noopener" class="wa-social-btn" aria-label="Facebook" title="Facebook">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 22v-8h2.7l.4-3.1h-3.1V8.9c0-.9.3-1.5 1.6-1.5h1.7V4.6c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.5-4 4.1v2.3H7.6V14h2.8v8h3.1z"/></svg>
    </a>
    <a href="https://www.instagram.com/transmidiesel/" target="_blank" rel="noopener" class="wa-social-btn" aria-label="Instagram" title="Instagram">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
    </a>
    <a href="https://www.tiktok.com/@transmidiesel" target="_blank" rel="noopener" class="wa-social-btn" aria-label="TikTok" title="TikTok">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16.5 3c.3 1.9 1.4 3.2 3.3 3.5v2.4c-1.2.1-2.3-.2-3.3-.9v6.4c0 3.3-2.6 5.6-5.6 5.6S5.3 17.7 5.3 14.6c0-2.9 2.2-5.3 5.2-5.3.3 0 .5 0 .8.1v2.5c-.3-.1-.5-.1-.8-.1-1.5 0-2.7 1.2-2.7 2.8 0 1.6 1.2 2.7 2.7 2.7 1.5 0 2.7-1.1 2.7-2.7V3h3.3z"/></svg>
    </a>
    <a href="https://www.linkedin.com/company/transmidiesel/" target="_blank" rel="noopener" class="wa-social-btn" aria-label="LinkedIn" title="LinkedIn">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5A2.5 2.5 0 1 0 5 8.5a2.5 2.5 0 0 0-.02-5zM3 9.5h4V21H3V9.5zm6.5 0h3.8v1.6h.05c.53-1 1.83-2.05 3.77-2.05 4.03 0 4.78 2.65 4.78 6.1V21h-4v-5.2c0-1.24-.02-2.84-1.73-2.84-1.73 0-2 1.35-2 2.75V21h-4V9.5z"/></svg>
    </a>
  </div>

</div>
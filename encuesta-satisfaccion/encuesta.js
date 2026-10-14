// =========================================================
// encuesta.js
// Carga las preguntas desde /api/encuesta-preguntas.php
// y arma el formulario dinámicamente.
// Envía las respuestas a /api/encuesta-guardar.php (Paso 5).
// =========================================================

(function () {
  'use strict';

  const API_PREGUNTAS = '../api/encuesta-preguntas.php';
  const API_GUARDAR   = '../api/encuesta-guardar.php';

  const loading    = document.getElementById('encuestaLoading');
  const errorBox   = document.getElementById('encuestaError');
  const errorMsg   = document.getElementById('encuestaErrorMsg');
  const form       = document.getElementById('encuestaForm');
  const contenedor = document.getElementById('encuestaPreguntas');
  const submitBtn  = document.getElementById('encuestaSubmitBtn');
  const btnText    = document.getElementById('encuestaBtnText');
  const statusEl   = document.getElementById('encuestaStatus');

  let preguntas = [];

  // ---------- Utilidad: escapar HTML ----------
  function esc(str) {
    if (str == null) return '';
    return String(str)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  // ---------- Utilidad: mostrar error ----------
  function mostrarError(mensaje) {
    loading.style.display = 'none';
    form.style.display = 'none';
    errorBox.style.display = 'block';
    errorMsg.textContent = mensaje || 'Por favor recarga la página o inténtalo más tarde.';
  }

  // ---------- Renderizar una pregunta ----------
  function renderPregunta(p) {
    const req = p.obligatoria ? '<span class="form-req">*</span>' : '';
    const labelBase = `<label class="form-label">${esc(p.texto)} ${req}</label>`;

    if (p.tipo === 'texto') {
      // Detectar si es "Nombre" o "Documento" para autocompletar atributos
      const lower = p.texto.toLowerCase();
      const isNombre = lower.includes('nombre');
      const isDoc    = lower.includes('documento') || lower.includes('nit') || lower.includes('cedula') || lower.includes('cédula');

      const autocomplete = isNombre ? 'name' : (isDoc ? 'off' : 'off');
      const inputmode    = isDoc ? 'text' : 'text';
      const placeholder  = isNombre
        ? 'Escribe tu nombre completo'
        : (isDoc ? 'Ej. 900123456-1 o 52098765' : 'Escribe tu respuesta');

      return `
        <div class="form-group encuesta-pregunta" data-pregunta-id="${p.id}" data-tipo="texto">
          ${labelBase}
          <input
            type="text"
            class="form-input encuesta-input-texto"
            data-pregunta-id="${p.id}"
            placeholder="${esc(placeholder)}"
            autocomplete="${autocomplete}"
            inputmode="${inputmode}"
            ${p.obligatoria ? 'required' : ''}>
        </div>
      `;
    }

    if (p.tipo === 'escala') {
      const min = p.escala_min ?? 1;
      const max = p.escala_max ?? 10;
      const labelMin = p.escala_label_min || '';
      const labelMax = p.escala_label_max || '';

      let radios = '';
      for (let i = min; i <= max; i++) {
        radios += `
          <label class="encuesta-escala-item">
            <input
              type="radio"
              name="pregunta_${p.id}"
              value="${i}"
              data-pregunta-id="${p.id}"
              ${p.obligatoria ? 'required' : ''}>
            <span class="encuesta-escala-num">${i}</span>
          </label>
        `;
      }

      return `
        <div class="form-group encuesta-pregunta" data-pregunta-id="${p.id}" data-tipo="escala">
          ${labelBase}
          <div class="encuesta-escala">
            <div class="encuesta-escala-extremos">
              <span class="encuesta-escala-label encuesta-escala-label-min">${esc(labelMin)}</span>
              <span class="encuesta-escala-label encuesta-escala-label-max">${esc(labelMax)}</span>
            </div>
            <div class="encuesta-escala-opciones">
              ${radios}
            </div>
          </div>
        </div>
      `;
    }

    if (p.tipo === 'opcion') {
      const opciones = Array.isArray(p.opciones) ? p.opciones : [];
      let opts = '<option value="">— Selecciona una opción —</option>';
      opciones.forEach(op => {
        opts += `<option value="${esc(op)}">${esc(op)}</option>`;
      });

      return `
        <div class="form-group encuesta-pregunta" data-pregunta-id="${p.id}" data-tipo="opcion">
          ${labelBase}
          <select
            class="form-select encuesta-input-select"
            data-pregunta-id="${p.id}"
            ${p.obligatoria ? 'required' : ''}>
            ${opts}
          </select>
        </div>
      `;
    }

    // Fallback por si aparece un tipo desconocido
    return '';
  }

  // ---------- Renderizar todas las preguntas ----------
  function renderFormulario() {
    contenedor.innerHTML = preguntas.map(renderPregunta).join('');
    loading.style.display = 'none';
    form.style.display = 'block';
    if (window.revelarNuevos) window.revelarNuevos(form);
  }

  // ---------- Cargar preguntas del backend ----------
  async function cargarPreguntas() {
    try {
      const res = await fetch(API_PREGUNTAS, { cache: 'no-store' });
      if (!res.ok) throw new Error('HTTP ' + res.status);

      const data = await res.json();
      if (!data.ok || !Array.isArray(data.preguntas)) {
        throw new Error(data.error || 'Respuesta inválida del servidor');
      }

      if (data.preguntas.length === 0) {
        mostrarError('Aún no hay preguntas configuradas. Vuelve más tarde.');
        return;
      }

      preguntas = data.preguntas;
      renderFormulario();

    } catch (err) {
      console.error('[encuesta] Error cargando preguntas:', err);
      mostrarError('No pudimos cargar la encuesta. Verifica tu conexión e inténtalo de nuevo.');
    }
  }

  // ---------- Mostrar status de envío ----------
  function setStatus(type, html) {
    statusEl.className = 'form-status ' + type;
    const icon = type === 'success' ? '✅' : '⚠️';
    statusEl.innerHTML = '<span class="ic-status">' + icon + '</span><div>' + html + '</div>';
    statusEl.style.display = 'flex';
    statusEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
  function clearStatus() {
    statusEl.style.display = 'none';
    statusEl.innerHTML = '';
  }

  // ---------- Cambiar estado del botón ----------
  function setBtnState(state) {
    submitBtn.classList.remove('is-sending', 'is-sent', 'is-error');

    if (state === 'sending') {
      submitBtn.disabled = true;
      submitBtn.style.opacity = '0.85';
      btnText.textContent = 'Enviando...';
      submitBtn.classList.add('is-sending');
    } else if (state === 'sent') {
      submitBtn.disabled = false;
      submitBtn.style.opacity = '1';
      btnText.textContent = '¡Encuesta enviada!';
      submitBtn.classList.add('is-sent');
      setTimeout(() => {
        submitBtn.classList.remove('is-sent');
        btnText.textContent = 'Enviar encuesta';
      }, 3500);
    } else if (state === 'error') {
      submitBtn.disabled = false;
      submitBtn.style.opacity = '1';
      btnText.textContent = 'Error, reintentar';
      submitBtn.classList.add('is-error');
      setTimeout(() => {
        submitBtn.classList.remove('is-error');
        btnText.textContent = 'Enviar encuesta';
      }, 3000);
    } else {
      submitBtn.disabled = false;
      submitBtn.style.opacity = '1';
      btnText.textContent = 'Enviar encuesta';
    }
  }

  // ---------- Recopilar respuestas del formulario ----------
  function recopilarRespuestas() {
    const valores = [];

    preguntas.forEach(p => {
      if (p.tipo === 'texto') {
        const input = form.querySelector(`.encuesta-input-texto[data-pregunta-id="${p.id}"]`);
        valores.push({
          pregunta_id: p.id,
          tipo: 'texto',
          valor: input ? input.value.trim() : ''
        });
      } else if (p.tipo === 'escala') {
        const checked = form.querySelector(`input[name="pregunta_${p.id}"]:checked`);
        valores.push({
          pregunta_id: p.id,
          tipo: 'escala',
          valor: checked ? parseInt(checked.value, 10) : null
        });
      } else if (p.tipo === 'opcion') {
        const select = form.querySelector(`.encuesta-input-select[data-pregunta-id="${p.id}"]`);
        valores.push({
          pregunta_id: p.id,
          tipo: 'texto',
          valor: select ? select.value : ''
        });
      }
    });

    return valores;
  }

  // ---------- Validar ----------
  function validar(respuestas) {
    for (let i = 0; i < preguntas.length; i++) {
      const p = preguntas[i];
      const r = respuestas.find(x => x.pregunta_id === p.id);
      if (!p.obligatoria) continue;

      if (p.tipo === 'texto' || p.tipo === 'opcion') {
        if (!r || !r.valor) {
          return {
            ok: false,
            mensaje: `Por favor completa la pregunta: "${p.texto}"`
          };
        }
      } else if (p.tipo === 'escala') {
        if (r.valor === null || r.valor === undefined || isNaN(r.valor)) {
          return {
            ok: false,
            mensaje: `Por favor califica: "${p.texto}"`
          };
        }
      }
    }
    return { ok: true };
  }

  // ---------- Envío ----------
  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    clearStatus();

    const respuestas = recopilarRespuestas();
    const v = validar(respuestas);

    if (!v.ok) {
      setStatus('error', v.mensaje);
      // Enfocar el primer campo con error
      const primerId = respuestas.find(r => r.valor === '' || r.valor === null)?.pregunta_id;
      const campo = form.querySelector(`[data-pregunta-id="${primerId}"]`);
      if (campo) campo.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }

    setBtnState('sending');

    try {
      const res = await fetch(API_GUARDAR, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json; charset=utf-8' },
        body: JSON.stringify({ respuestas })
      });

      const data = await res.json();

      if (data.ok) {
        setBtnState('sent');

        let mensaje = '<strong>¡Gracias por tu opinión!</strong><br>';
        if (data.mensaje) {
          mensaje += data.mensaje;
        } else {
          mensaje += 'Tu encuesta fue registrada correctamente.';
        }

        setStatus('success', mensaje);
        form.reset();

        // Redirigir al inicio después de 3.5 segundos
        setTimeout(() => {
          window.location.href = '../#encuestas';
        }, 3500);

      } else {
        setBtnState('error');
        setStatus('error', data.error || 'No pudimos guardar tu encuesta. Inténtalo de nuevo.');
      }

    } catch (err) {
      console.error('[encuesta] Error guardando:', err);
      setBtnState('error');
      setStatus('error', 'Hubo un problema de conexión. Por favor inténtalo de nuevo más tarde.');
    }
  });

  // ---------- Init ----------
  cargarPreguntas();

})();
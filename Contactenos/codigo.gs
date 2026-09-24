/**
 * ============================================================================
 * TRANSMIDIESEL S.A.S — SCRIPT DE CONEXIÓN DE FORMULARIO DE CONTACTO
 * ============================================================================
 * Este script recibe los datos del formulario de la página "Contáctenos",
 * los guarda automáticamente en una hoja de Google Sheets y envía una
 * notificación por correo electrónico a los destinatarios configurados.
 *
 * AUTO-CREACIÓN DE CAMPOS:
 * Al ejecutarse por primera vez (o al recibir el primer mensaje), el script
 * creará automáticamente las columnas y el formato en la hoja de cálculo.
 * ============================================================================
 */

// ============================================================================
// 1. CONFIGURACIÓN: CORREOS DE NOTIFICACIÓN
// Agrega aquí los correos que recibirán los mensajes de contacto.
// Puedes agregar uno o varios correos separados por comas entre comillas.
// ============================================================================
const CORREOS_NOTIFICACION = [
  "tu-correo@empresa.com",     // <-- REEMPLAZA CON TU CORREO PRINCIPAL
  // "comercial@transmidiesel.com",
  // "gerencia@transmidiesel.com"
];

// Nombre de la pestaña donde se guardarán los contactos
const NOMBRE_HOJA = "Contactos Web";

/**
 * Función que crea y formatea automáticamente las columnas en Google Sheets
 * si la hoja está vacía o no tiene encabezados.
 */
function inicializarHoja() {
  const libro = SpreadsheetApp.getActiveSpreadsheet();
  let hoja = libro.getSheetByName(NOMBRE_HOJA);
  
  if (!hoja) {
    hoja = libro.insertSheet(NOMBRE_HOJA);
  }
  
  // Si la primera fila está vacía, creamos los encabezados
  if (hoja.getLastRow() === 0 || hoja.getRange(1, 1).getValue() === "") {
    const encabezados = [
      "Fecha y Hora",
      "Nombre",
      "Correo Electrónico",
      "Teléfono",
      "Asunto / Consulta",
      "Mensaje",
      "Estado"
    ];
    
    // Escribir encabezados
    const filaEncabezados = hoja.getRange(1, 1, 1, encabezados.length);
    filaEncabezados.setValues([encabezados]);
    
    // Estilo corporativo Transmidiesel (Navy #0F3E68 y Blanco)
    filaEncabezados
      .setBackground("#0F3E68")
      .setFontColor("#FFFFFF")
      .setFontWeight("bold")
      .setFontFamily("Inter")
      .setFontSize(11)
      .setHorizontalAlignment("center")
      .setVerticalAlignment("middle");
      
    hoja.setRowHeight(1, 38);
    hoja.setFrozenRows(1);
    
    // Anchos recomendados por columna
    hoja.setColumnWidth(1, 160); // Fecha
    hoja.setColumnWidth(2, 200); // Nombre
    hoja.setColumnWidth(3, 230); // Correo
    hoja.setColumnWidth(4, 150); // Teléfono
    hoja.setColumnWidth(5, 220); // Asunto
    hoja.setColumnWidth(6, 400); // Mensaje
    hoja.setColumnWidth(7, 120); // Estado
    
    Logger.log("✅ Hoja '" + NOMBRE_HOJA + "' inicializada con éxito.");
  }
  
  return hoja;
}

/**
 * Función principal que recibe las peticiones POST desde la página web
 */
function doPost(e) {
  try {
    const hoja = inicializarHoja();
    
    // 1. Obtener y parsear los datos enviados (soporta JSON y Form URL-Encoded)
    let datos = {};
    if (e && e.postData && e.postData.contents) {
      try {
        datos = JSON.parse(e.postData.contents);
      } catch (errJson) {
        datos = e.parameter || {};
      }
    } else if (e && e.parameter) {
      datos = e.parameter;
    }
    
    const nombre = (datos.nombre || "No especificado").trim();
    const correo = (datos.correo || "No especificado").trim();
    const telefono = (datos.telefono || "No especificado").trim();
    const asunto = (datos.asunto || "Consulta general").trim();
    const mensaje = (datos.mensaje || "").trim();
    
    // Fecha y hora en zona horaria de Colombia
    const fechaHora = Utilities.formatDate(new Date(), "America/Bogota", "dd/MM/yyyy HH:mm:ss");
    
    // 2. Guardar la fila en Google Sheets
    hoja.appendRow([
      fechaHora,
      nombre,
      correo,
      telefono,
      asunto,
      mensaje,
      "Nuevo"
    ]);
    
    // Formatear la última fila insertada
    const ultimaFila = hoja.getLastRow();
    hoja.getRange(ultimaFila, 1, 1, 7)
      .setFontFamily("Inter")
      .setFontSize(10)
      .setVerticalAlignment("middle");
      
    // Ajustar alineación de fecha y estado
    hoja.getRange(ultimaFila, 1).setHorizontalAlignment("center");
    hoja.getRange(ultimaFila, 7).setHorizontalAlignment("center").setBackground("#EBF5FB").setFontColor("#0F3E68").setFontWeight("bold");
    
    // 3. Enviar notificación por correo si hay correos configurados válidos
    const correosValidos = CORREOS_NOTIFICACION.filter(function(c) {
      return c && c.indexOf("@") !== -1 && c.indexOf("tu-correo") === -1;
    });
    
    if (correosValidos.length > 0) {
      enviarCorreoNotificacion(correosValidos, {
        fechaHora: fechaHora,
        nombre: nombre,
        correo: correo,
        telefono: telefono,
        asunto: asunto,
        mensaje: mensaje
      });
    }
    
    // 4. Retornar respuesta exitosa al frontend
    return ContentService.createTextOutput(JSON.stringify({
      status: "success",
      message: "¡Mensaje recibido y registrado correctamente!"
    })).setMimeType(ContentService.MimeType.JSON);
    
  } catch (error) {
    Logger.log("Error al procesar contacto: " + error.toString());
    
    return ContentService.createTextOutput(JSON.stringify({
      status: "error",
      message: error.toString()
    })).setMimeType(ContentService.MimeType.JSON);
  }
}

/**
 * Envía un correo electrónico con plantilla HTML profesional
 */
function enviarCorreoNotificacion(destinatarios, datos) {
  const asuntoEmail = "📥 Nuevo Contacto Web: " + datos.asunto + " — " + datos.nombre;
  
  const cuerpoHtml = 
    "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;border:1px solid #e1e8ed;border-radius:12px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.06);'>" +
      "<div style='background:#0F3E68;color:#ffffff;padding:24px 30px;'>" +
        "<h2 style='margin:0;font-size:22px;'>Transmidiesel S.A.S</h2>" +
        "<p style='margin:6px 0 0;font-size:14px;opacity:0.85;'>Nueva solicitud de contacto recibida desde la página web</p>" +
      "</div>" +
      "<div style='padding:28px 30px;background:#ffffff;'>" +
        "<table style='width:100%;border-collapse:collapse;'>" +
          "<tr><td style='padding:10px 0;border-bottom:1px solid #f0f3f6;color:#5a5f66;font-size:13px;width:130px;'><strong>Fecha y hora:</strong></td><td style='padding:10px 0;border-bottom:1px solid #f0f3f6;color:#1a1a1a;font-size:14px;'>" + datos.fechaHora + "</td></tr>" +
          "<tr><td style='padding:10px 0;border-bottom:1px solid #f0f3f6;color:#5a5f66;font-size:13px;'><strong>Nombre:</strong></td><td style='padding:10px 0;border-bottom:1px solid #f0f3f6;color:#1a1a1a;font-size:14px;font-weight:bold;'>" + datos.nombre + "</td></tr>" +
          "<tr><td style='padding:10px 0;border-bottom:1px solid #f0f3f6;color:#5a5f66;font-size:13px;'><strong>Correo:</strong></td><td style='padding:10px 0;border-bottom:1px solid #f0f3f6;color:#0F3E68;font-size:14px;'><a href='mailto:" + datos.correo + "' style='color:#0F3E68;text-decoration:none;'><strong>" + datos.correo + "</strong></a></td></tr>" +
          "<tr><td style='padding:10px 0;border-bottom:1px solid #f0f3f6;color:#5a5f66;font-size:13px;'><strong>Teléfono:</strong></td><td style='padding:10px 0;border-bottom:1px solid #f0f3f6;color:#1a1a1a;font-size:14px;'><a href='tel:" + datos.telefono + "' style='color:#1a1a1a;text-decoration:none;'>" + datos.telefono + "</a></td></tr>" +
          "<tr><td style='padding:10px 0;border-bottom:1px solid #f0f3f6;color:#5a5f66;font-size:13px;'><strong>Asunto:</strong></td><td style='padding:10px 0;border-bottom:1px solid #f0f3f6;color:#1a1a1a;font-size:14px;font-weight:bold;'>" + datos.asunto + "</td></tr>" +
        "</table>" +
        "<div style='margin-top:20px;padding:16px;background:#f7f9fb;border-left:4px solid #0F3E68;border-radius:4px;'>" +
          "<strong style='color:#0F3E68;font-size:13px;display:block;margin-bottom:6px;'>Mensaje del cliente:</strong>" +
          "<p style='margin:0;color:#333333;font-size:14.5px;line-height:1.6;white-space:pre-wrap;'>" + datos.mensaje + "</p>" +
        "</div>" +
        "<div style='margin-top:26px;text-align:center;'>" +
          "<a href='mailto:" + datos.correo + "?subject=Respuesta a tu consulta en Transmidiesel S.A.S' style='display:inline-block;padding:12px 26px;background:#0F3E68;color:#ffffff;text-decoration:none;border-radius:8px;font-size:14px;font-weight:bold;'>Responder al cliente</a>" +
        "</div>" +
      "</div>" +
      "<div style='background:#f4f6f8;padding:14px;text-align:center;font-size:12px;color:#788590;border-top:1px solid #e1e8ed;'>" +
        "Transmidiesel S.A.S · Sistema automatizado de atención web" +
      "</div>" +
    "</div>";
    
  MailApp.sendEmail({
    to: destinatarios.join(","),
    subject: asuntoEmail,
    htmlBody: cuerpoHtml,
    replyTo: (datos.correo && datos.correo.indexOf("@") !== -1) ? datos.correo : undefined
  });
}

/**
 * Responde a peticiones GET de prueba para verificar que el Web App esté online
 */
function doGet(e) {
  return ContentService.createTextOutput(JSON.stringify({
    status: "online",
    message: "El servicio de formulario web de Transmidiesel S.A.S está activo y listo para recibir mensajes."
  })).setMimeType(ContentService.MimeType.JSON);
}

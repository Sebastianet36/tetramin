/**
 * datos_partida.js
 * Funciones para enviar datos de la partida al backend y mostrar mensajes.
 */

export function mostrarMensaje(mensaje, tipo = 'success') {
    const mensajeDiv = document.createElement('div');
    mensajeDiv.textContent = mensaje;
    mensajeDiv.className = 'mensaje-notificacion ' + tipo;
    mensajeDiv.style.position = 'fixed';
    mensajeDiv.style.right = '20px';
    mensajeDiv.style.top = tipo === 'error' ? '20px' : '80px';
    mensajeDiv.style.zIndex = 9999;
    mensajeDiv.style.padding = '10px 16px';
    mensajeDiv.style.borderRadius = '6px';
    mensajeDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.4)';
    mensajeDiv.style.fontFamily = 'Arial, sans-serif';
    mensajeDiv.style.transition = 'opacity 0.3s ease';
    if (tipo === 'success') {
        mensajeDiv.style.background = '#1e4620';
        mensajeDiv.style.color = '#dff5d8';
    } else {
        mensajeDiv.style.background = '#5a1e1e';
        mensajeDiv.style.color = '#ffdede';
    }
    document.body.appendChild(mensajeDiv);
    setTimeout(() => {
        mensajeDiv.style.opacity = '0';
        setTimeout(() => mensajeDiv.remove(), 350);
    }, 3000);
}

/**
 * Mapa de modos JS -> id_modo (DB)
 * Confirmado por el usuario:
 * classic   -> 1
 * sprint40  -> 2
 * cheese    -> 3
 * survival  -> 4
 */
const modoToId = {
    classic: 1,
    sprint40: 2,
    cheese: 3,
    survival: 4
};

/**
 * Normaliza y convierte el tiempo mostrado por el juego a segundos (int).
 * Acepta formatos:
 *  - "MM:SS:CC" (CC centésimas, p.ej. 03 -> 30ms/10 -> convertimos a segundos con decimales y redondeamos)
 *  - "MM:SS"
 *  - total segundos en string/number
 */
function tiempoAsegundos(tiempoStr) {
    if (tiempoStr == null) return 0;
    tiempoStr = String(tiempoStr).trim();

    // Separar por ':'
    const parts = tiempoStr.split(':').map(p => p.trim());
    if (parts.length === 3) {
        const mm = parseInt(parts[0], 10) || 0;
        const ss = parseInt(parts[1], 10) || 0;
        const cc = parseInt(parts[2], 10) || 0; // centésimas según tu formato (ms/10)
        // Convertimos centésimas a segundos (cc / 100)
        const total = mm * 60 + ss + (cc / 100);
        return Math.round(total); // la DB tiene duracion INT, guardamos segundos redondeados
    } else if (parts.length === 2) {
        const mm = parseInt(parts[0], 10) || 0;
        const ss = parseInt(parts[1], 10) || 0;
        const total = mm * 60 + ss;
        return Math.round(total);
    } else {
        // si viene número entero o string de segundos
        const asNumber = Number(tiempoStr);
        if (!isNaN(asNumber)) return Math.round(asNumber);
    }
    return 0;
}

/**
 * Envía los datos del juego al backend y devuelve la respuesta completa (JSON).
 * 
 * @param {Object} datosJuego - { puntaje, tiempo, nivel, lineas, modo } 
 *    - modo puede ser el string del modo ('classic','cheese',...)
 *    - alternativamente puede incluir id_modo numérico
 * @param {string} url - endpoint PHP
 * @returns {Promise<Object>} - respuesta JSON del servidor
 */
export async function enviarDatosAlServidor(datosJuego, url) {
    try {
        // Validar y normalizar
        if (!datosJuego) throw new Error('No se informaron datos de la partida');

        const puntaje = Number(datosJuego.puntaje) || 0;
        const nivel = Number(datosJuego.nivel) || 1;
        const lineas = Number(datosJuego.lineas) || 0;
        const tiempoStr = datosJuego.tiempo || datosJuego.time || '00:00:00';

        // obtener id_modo: si ya viene numérico lo usamos, sino buscar en mapa
        let id_modo = null;
        if (typeof datosJuego.id_modo !== 'undefined') {
            id_modo = Number(datosJuego.id_modo) || null;
        } else if (typeof datosJuego.modo === 'string') {
            id_modo = modoToId[datosJuego.modo] || null;
        } else if (typeof datosJuego.modo === 'number') {
            id_modo = Number(datosJuego.modo);
        }

        if (!id_modo) {
            // fallback: asumimos clásico
            id_modo = 1;
            console.warn('No se pudo resolver id_modo, usando 1 (classic) por defecto.');
        }

        const duracion_segundos = tiempoAsegundos(tiempoStr);

        const formData = new FormData();
        formData.append('puntaje', puntaje);
        formData.append('tiempo', tiempoStr);
        formData.append('nivel', nivel);
        formData.append('lineas', lineas);
        formData.append('id_modo', id_modo);

        const resp = await fetch(url, {
            method: 'POST',
            body: formData,
            credentials: 'include' // importante si usás sesiones PHP
        });

        const json = await resp.json();

        if (!resp.ok) {
            const message = json && json.error ? json.error : 'Error al guardar la puntuación';
            mostrarMensaje(message, 'error');
            return { success: false, error: message, raw: json };
        }

        // Si el servidor devuelve el record anterior y el actual, podes mostrar comparación
        if (json.success) {
            if (json.nuevo_record) {
                mostrarMensaje('¡Nuevo récord guardado!', 'success');
            } else {
                mostrarMensaje('Puntuación enviada (no superó el récord)', 'success');
            }
        } else {
            mostrarMensaje('Respuesta inesperada del servidor', 'error');
        }

        return json;
    } catch (err) {
        console.error('Error en enviarDatosAlServidor:', err);
        mostrarMensaje('Error de conexión al guardar la puntuación', 'error');
        return { success: false, error: err.message };
    }
}

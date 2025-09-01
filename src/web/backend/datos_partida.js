/**
 * Muestra un mensaje flotante en pantalla.
 * @param {string} mensaje - El texto a mostrar.
 * @param {string} tipo - 'success' o 'error'.
 */
export function mostrarMensaje(mensaje, tipo) {
    const mensajeDiv = document.createElement('div');
    mensajeDiv.textContent = mensaje;
    mensajeDiv.className = 'mensaje-notificacion ' + tipo;
    document.body.appendChild(mensajeDiv);
    setTimeout(() => {
        if (document.body.contains(mensajeDiv)) {
            mensajeDiv.style.opacity = '0';
            setTimeout(() => mensajeDiv.remove(), 300);
        }
    }, 3000);
}

/**
 * Envía los datos del juego al backend y muestra un mensaje según el resultado.
 * @param {Object} datosJuego - Objeto con los datos del juego (puntaje, tiempo, nivel, lineas).
 * @param {string} url - URL absoluta o relativa del endpoint PHP.
 */
export function enviarDatosAlServidor(datosJuego, url) {
    const formData = new FormData();
    Object.entries(datosJuego).forEach(([key, value]) => {
        formData.append(key, value);
    });
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje('¡Puntuación guardada!', 'success');
        } else {
            mostrarMensaje('Error al guardar la puntuación: ' + (data.message || data.error), 'error');
        }
    })
    .catch(error => {
        mostrarMensaje('Error de conexión al guardar la puntuación', 'error');
    });
} 
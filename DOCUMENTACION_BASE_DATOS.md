# DOCUMENTACIÓN - BASE DE DATOS TETRIS REESTRUCTURADA

## 📋 Resumen de la Nueva Estructura

La base de datos ha sido completamente reestructurada para ser más eficiente, escalable y mantenible. Incluye triggers automáticos, procedimientos almacenados, vistas y un sistema de logging completo.

## 🗂️ Tablas Principales

### 1. **usuarios**
- **Propósito**: Almacena información de los usuarios registrados
- **Campos clave**: `id_usuario`, `nombre_usuario`, `email`, `contraseña`
- **Características**: 
  - Usernames y emails únicos
  - Soporte para administradores
  - Información de ubicación (país, ciudad)
  - Control de estado activo/inactivo

### 2. **modos_juego**
- **Propósito**: Define los diferentes modos de juego disponibles
- **Campos clave**: `id_modo`, `nombre_modo`, `descripcion`
- **Modos incluidos**:
  - Clásico (tradicional)
  - Carrera (40 líneas)
  - Excavar (eliminar bloques)
  - Chill-Out (relajado)
  - Sprint (20 líneas)
  - Maratón (150 líneas)

### 3. **partidas**
- **Propósito**: Registra cada partida individual
- **Campos clave**: `id_partida`, `id_usuario`, `id_modo`, `estado`
- **Estadísticas detalladas**:
  - Puntaje, duración, nivel alcanzado
  - Líneas completadas, piezas colocadas
  - Tetris completados, T-spins, perfect clears
  - Combo máximo
- **Estados**: en_progreso, completada, abandonada

### 4. **records_personales**
- **Propósito**: Mantiene los mejores logros de cada usuario por modo
- **Características**:
  - Actualización automática mediante triggers
  - Contadores de partidas jugadas y tiempo total
  - Fecha del último récord personal

### 5. **clasificacion_global**
- **Propósito**: Ranking global de usuarios por modo
- **Características**:
  - Posición global calculada automáticamente
  - Actualización automática cuando se superan récords

### 6. **configuraciones_usuario**
- **Propósito**: Preferencias personalizadas de cada usuario
- **Configuraciones**:
  - Idioma, tema oscuro
  - Sonido y música (activado/desactivado, volumen)
  - Controles personalizados (JSON)

### 7. **log_actividad**
- **Propósito**: Registro de todas las actividades del sistema
- **Tipos de actividad**: login, logout, nueva_partida, record_personal, record_global, configuracion
- **Información adicional**: IP, user agent, datos JSON

## 🔄 Triggers Automáticos

### 1. **actualizar_record_personal**
- **Activación**: Cuando una partida se marca como completada
- **Función**: Actualiza automáticamente los récords personales del usuario
- **Lógica**: Compara con récords existentes y actualiza solo si es mejor

### 2. **actualizar_clasificacion_global**
- **Activación**: Cuando se actualiza un récord personal
- **Función**: Recalcula las posiciones globales
- **Lógica**: Ordena por puntaje y asigna posiciones

## 📊 Vistas Útiles

### 1. **v_top_global**
- **Propósito**: Top 10 global por modo de juego
- **Campos**: posición, usuario, puntaje, fecha, modo

### 2. **v_estadisticas_usuario**
- **Propósito**: Estadísticas completas de cada usuario
- **Campos**: Todos los récords personales y estadísticas

## 🛠️ Procedimientos Almacenados

### 1. **ObtenerRankingModo(modo_id, limite)**
- **Función**: Obtiene el ranking de un modo específico
- **Parámetros**: ID del modo, número de posiciones a mostrar

### 2. **RegistrarNuevaPartida(usuario_id, modo_id)**
- **Función**: Crea una nueva partida en estado "en_progreso"
- **Retorna**: ID de la partida creada

### 3. **FinalizarPartida(partida_id, ...)**
- **Función**: Marca una partida como completada con todas las estadísticas
- **Parámetros**: ID de partida y todas las estadísticas del juego

## 🚀 Ventajas de la Nueva Estructura

### 1. **Escalabilidad**
- Índices optimizados para consultas frecuentes
- Separación clara de responsabilidades
- Soporte para múltiples modos de juego

### 2. **Automatización**
- Triggers automáticos para mantener consistencia
- Actualización automática de rankings
- Logging automático de actividades

### 3. **Flexibilidad**
- Estructura JSON para configuraciones personalizadas
- Soporte para estadísticas detalladas
- Fácil extensión para nuevos modos

### 4. **Seguridad**
- Consultas preparadas en todos los archivos PHP
- Validación de datos en múltiples niveles
- Logging de actividades para auditoría

### 5. **Rendimiento**
- Índices estratégicos en campos frecuentemente consultados
- Vistas materializadas para consultas complejas
- Procedimientos almacenados para operaciones comunes

## 📝 Cómo Usar la Nueva Base de Datos

### 1. **Instalación**
```sql
-- Ejecutar el archivo tetrisdb_nueva.sql en phpMyAdmin
-- Esto creará toda la estructura y datos iniciales
```

### 2. **Registrar una Partida**
```php
// El archivo guardar_datos_juego.php ya está actualizado
// Usa los procedimientos almacenados automáticamente
```

### 3. **Consultar Rankings**
```sql
-- Top 10 global del modo clásico
CALL ObtenerRankingModo(1, 10);

-- O usar la vista
SELECT * FROM v_top_global WHERE nombre_modo = 'Clásico';
```

### 4. **Obtener Estadísticas de Usuario**
```sql
-- Todas las estadísticas de un usuario
SELECT * FROM v_estadisticas_usuario WHERE nombre_usuario = 'usuario';
```

## 🔧 Mantenimiento

### 1. **Backup Regular**
- Hacer backup completo de la base de datos
- Especialmente importante por los triggers y procedimientos

### 2. **Monitoreo de Logs**
- Revisar regularmente la tabla `log_actividad`
- Limpiar logs antiguos si es necesario

### 3. **Optimización**
- Los índices están optimizados para el uso típico
- Monitorear el rendimiento de consultas complejas

## 📈 Próximos Pasos

1. **Migrar datos existentes** (si los hay)
2. **Actualizar archivos PHP** para usar la nueva estructura
3. **Implementar nuevas funcionalidades** aprovechando la flexibilidad
4. **Crear paneles de administración** usando las vistas y procedimientos

---

*Esta nueva estructura proporciona una base sólida para el crecimiento futuro del juego Tetris, con soporte completo para múltiples modos, estadísticas detalladas y un sistema de ranking robusto.* 
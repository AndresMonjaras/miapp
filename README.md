# Task API - Documentación (Swagger)

Este repositorio contiene la API de Tareas y su contrato de interfaz definido en OpenAPI (Swagger).

## Cómo utilizar el contrato de la API (Swagger UI)

La API cuenta con una interfaz gráfica interactiva que lee automáticamente el archivo de contrato `openapi.yaml` para mostrar todos los endpoints, parámetros y respuestas disponibles.

### 1. Acceder a la Documentación
Una vez que el servidor esté corriendo, puedes acceder a la interfaz de Swagger UI desde cualquier navegador:
- **Entorno Local:** `http://localhost:8080/api-docs/`
- **Producción:** `http://topicosweb.celaya.tecnm.mx/22030873/miapp/api/public/api-docs/`

### 2. Explorar y probar los endpoints
Dentro de la página de Swagger:
1. En la parte superior, seleccionar el entorno correcto en el menú desplegable (Local o Producción).
2. En seguida hacer clic sobre cualquier endpoint (ej. `GET /api/tareas`) para expandir los detalles.
3. Se pueden probar la ruta directamente haciendo clic en el botón **"Try it out"**.
4. Llena los datos requeridos (si es necesario) y haz clic en **"Execute"**.  

### 3. Sobre el Archivo YAML
El contrato principal se encuentra en: `api/openapi.yaml`. 
 

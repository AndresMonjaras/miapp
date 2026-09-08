# Reporte de Práctica: Estrategia API-First

## 1. Definición de API-First: Explicación de la estrategia
El enfoque **API-First** es una estrategia de desarrollo en la que la Interfaz de Programación de Aplicaciones (API) se considera el componente central y principal de un sistema, y no un subproducto añadido al final del desarrollo. Bajo este enfoque, antes de escribir cualquier línea de código, los equipos de diseño y desarrollo definen, discuten y acuerdan un contrato claro (generalmente en un formato estándar como OpenAPI o Swagger) sobre cómo se comunicarán los distintos servicios y aplicaciones.

Esta estrategia asegura que las APIs sean tratadas como productos de primera clase. Proporciona una fuente única de verdad, permitiendo que los equipos de front-end y back-end puedan trabajar de manera paralela, simulando respuestas mediante "mock servers" y acelerando el ciclo de vida de desarrollo de software.

## 2. Comparación entre Code-First vs API-First y escalabilidad
| Característica | Code-First | API-First |
| --- | --- | --- |
| **Inicio del desarrollo** | Se inicia escribiendo la lógica de negocio y los controladores, y la API se genera a partir de estos. | Se inicia diseñando la especificación y contrato de la API antes de programar la lógica. |
| **Trabajo en paralelo** | El equipo de Front-end suele depender de que el Back-end termine sus endpoints para empezar a integrar. | Los equipos de Front-end y Back-end trabajan en paralelo usando el contrato acordado y servidores de simulación. |
| **Escalabilidad** | Difícil de escalar en equipos grandes, ya que los cambios no planificados pueden romper la comunicación entre microservicios. | Altamente escalable; la arquitectura se planifica desde el principio y el contrato facilita la adopción por terceros y la integración de microservicios. |
| **Mantenimiento** | La documentación suele estar desactualizada respecto al código real. | La documentación se convierte en la fuente de verdad y se mantiene sincronizada más fácilmente con el desarrollo. |

## 3. Explicación del Contrato de la API y el Rol de OpenAPI
Un **contrato de API** es un documento legible tanto por humanos como por máquinas que define exhaustivamente los endpoints, parámetros, métodos HTTP soportados (GET, POST, PUT, etc.), así como las estructuras de datos esperadas tanto para las peticiones (requests) y respuestas (responses). 

**OpenAPI** es el estándar de la industria para definir estos contratos en el diseño de APIs RESTful. Escribiendo la especificación en YAML o JSON, OpenAPI nos permite:
- Describir de forma precisa cada recurso (ej. Tareas).
- Generar documentación interactiva (como **Swagger UI**), donde los desarrolladores y usuarios finales pueden explorar y probar los endpoints sin necesidad de utilizar herramientas externas en primera instancia.

## 4. Beneficios del Diseño API-First
1. **Desarrollo paralelo y reducción de cuellos de botella:** Al definir el contrato primero, múltiples equipos pueden trabajar al mismo tiempo.
2. **Mejor experiencia para el desarrollador (DX):** La documentación es interactiva, predecible y clara.
3. **Consistencia de diseño:** Las APIs resultan más intuitivas y uniformes.
4. **Menor riesgo de fallos de integración:** El contrato previene malentendidos.
5. **Reusabilidad:** Las APIs diseñadas cuidadosamente son más fáciles de adaptar.

---
## Evidencias (Capturas de Pantalla)

> **Nota:** Por favor, inserta las capturas de pantalla solicitadas en esta sección.

### Swagger UI mostrando el contrato
*(Inserta aquí tu captura de pantalla)*

### Pruebas en Postman
- **Creación de Tarea (POST):**
*(Inserta aquí tu captura de pantalla)*
- **Listado de Tareas (GET):**
*(Inserta aquí tu captura de pantalla)*

### Capturas del Proceso (Código y Despliegue)
*(Inserta aquí las capturas de pantalla)*

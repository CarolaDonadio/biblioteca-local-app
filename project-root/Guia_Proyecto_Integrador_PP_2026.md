ISFDyT N°57
Proyecto Integrador de Práctica Profesionalizante
Guía para definir, desarrollar y defender el proyecto del semestre
Tecnicatura Superior en Ciencia de Datos e Inteligencia Artificial
Segundo semestre 2026
1. Propósito del proyecto
Durante el segundo semestre cada equipo desarrollará una aplicación orientada a resolver una necesidad concreta. El objetivo principal no es “hacer una app” por sí misma, sino recorrer un proceso de trabajo semejante al profesional: comprender un problema, acordar un alcance, diseñar una solución, implementarla, probarla, recibir feedback y entregar un producto funcional.
| Regla central: primero se define el problema; después se decide la solución tecnológica. |
| --- |

Regla central: primero se define el problema; después se decide la solución tecnológica.
2. Organización general
- Trabajo en equipos. Cada equipo será responsable de un único proyecto.
- El docente actuará como cliente y referente de validación durante todo el semestre.
- Cada grupo deberá proponer y justificar el problema que desea resolver.
- El problema puede surgir de una situación real o verosímil: institución, comercio, PyME, club, profesional, organización, actividad cotidiana u otro contexto aprobado.
- No se aprobarán proyectos definidos únicamente por la tecnología (“queremos hacer una app con X”). La propuesta debe comenzar por una necesidad identificable.
- El alcance se definirá como un MVP (Producto Mínimo Viable) alcanzable dentro del semestre.
3. Etapa 1 - Detectar y definir el problema
Cada equipo deberá presentar una propuesta inicial. La formulación debe explicar la situación actual y no sólo enumerar funcionalidades.
| Pregunta | Qué debe quedar claro |
| --- | --- |
| ¿Quién tiene el problema? | Usuario, organización o actor afectado. |
| ¿Qué sucede hoy? | Cómo se resuelve actualmente la situación. |
| ¿Cuál es la dificultad? | Pérdidas de tiempo, errores, falta de información, desorganización, etc. |
| ¿Qué información interviene? | Datos que el sistema debería registrar, consultar o procesar. |
| ¿Qué resultado sería útil? | Mejora concreta que debería producir la solución. |
| ¿Cómo sabremos si funciona? | Criterios observables para validar el MVP. |

Pregunta
Qué debe quedar claro
¿Quién tiene el problema?
Usuario, organización o actor afectado.
¿Qué sucede hoy?
Cómo se resuelve actualmente la situación.
¿Cuál es la dificultad?
Pérdidas de tiempo, errores, falta de información, desorganización, etc.
¿Qué información interviene?
Datos que el sistema debería registrar, consultar o procesar.
¿Qué resultado sería útil?
Mejora concreta que debería producir la solución.
¿Cómo sabremos si funciona?
Criterios observables para validar el MVP.
4. Primera presentación al cliente
Antes de comenzar a desarrollar, el equipo realizará una breve reunión con el cliente (docente). Deberá presentar:
- Nombre provisorio del proyecto.
- Problema o necesidad detectada.
- Usuarios principales.
- Situación actual y consecuencias del problema.
- Propuesta de solución.
- Funciones imprescindibles y funciones deseables.
- Datos principales que deberá manejar la aplicación.
- Riesgos o dudas abiertas.
| La aprobación de la idea no significa que todas las funcionalidades propuestas entren en el semestre. El cliente podrá pedir que se reduzca o redefina el alcance. |
| --- |

La aprobación de la idea no significa que todas las funcionalidades propuestas entren en el semestre. El cliente podrá pedir que se reduzca o redefina el alcance.
5. Alcance: solución ideal, MVP y mejoras futuras
Cada equipo separará explícitamente tres niveles de alcance:
| Nivel | Definición | Ejemplo |
| --- | --- | --- |
| Solución ideal | Todo lo que podría tener el producto si no existieran límites de tiempo. | Sistema completo de gestión de un gimnasio. |
| MVP obligatorio | Conjunto mínimo que debe funcionar para resolver el núcleo del problema. | Socios + cuotas + estado de pago + búsqueda. |
| Backlog futuro | Mejoras que quedan documentadas pero no son necesarias para aprobar. | Turnos, notificaciones, app móvil, métricas avanzadas. |

Nivel
Definición
Ejemplo
Solución ideal
Todo lo que podría tener el producto si no existieran límites de tiempo.
Sistema completo de gestión de un gimnasio.
MVP obligatorio
Conjunto mínimo que debe funcionar para resolver el núcleo del problema.
Socios + cuotas + estado de pago + búsqueda.
Backlog futuro
Mejoras que quedan documentadas pero no son necesarias para aprobar.
Turnos, notificaciones, app móvil, métricas avanzadas.
6. Arquitectura y stack tecnológico
El stack será definido por cada grupo y deberá ser aprobado por el docente. La elección deberá ser coherente con el problema, el tiempo disponible y los conocimientos del equipo.
| Stack recomendado: HTML/CSS/JavaScript + PHP + CodeIgniter 4 + MySQL/MariaDB + Git/GitHub. |
| --- |

Stack recomendado: HTML/CSS/JavaScript + PHP + CodeIgniter 4 + MySQL/MariaDB + Git/GitHub.
La recomendación de CodeIgniter 4 se debe a que permite trabajar explícitamente el patrón MVC y separar responsabilidades entre Modelo, Vista y Controlador. Se podrán proponer otros frameworks o stacks, siempre que el equipo pueda justificar la decisión y mantener una arquitectura clara.
6.1 Requisitos técnicos mínimos
- Aplicación web funcional.
- Persistencia en una base de datos relacional.
- Arquitectura MVC o una separación equivalente claramente justificada y aprobada.
- Operaciones de alta, consulta, modificación y/o baja cuando sean pertinentes al dominio.
- Reglas de negocio y validaciones coherentes con el problema.
- Control de versiones con Git y repositorio del equipo.
- Manejo básico de errores y seguridad.
- Documentación suficiente para instalar, ejecutar y comprender la aplicación.
6.2 MVC: qué deberán poder explicar
- Qué responsabilidad tiene el Modelo en su proyecto.
- Qué responsabilidad tiene el Controlador.
- Qué información presenta cada Vista.
- Cómo viaja una solicitud desde el usuario hasta la base de datos y vuelve a la interfaz.
- Dónde se implementan las validaciones y las reglas de negocio principales.
7. Forma de trabajo
Se utilizará una metodología ágil simplificada. El objetivo no es aplicar Scrum de manera burocrática, sino hacer visible el trabajo del equipo y poder revisar avances.
- Backlog con tareas e historias de usuario.
- Tablero mínimo: Backlog → Pendiente → En curso → Revisión → Terminado.
- Revisión semanal del avance.
- Tareas pequeñas, con responsable y criterio de finalización.
- Uso frecuente de commits. Los avances importantes deben quedar registrados en el repositorio.
- Feedback del cliente durante el proceso, no sólo en la entrega final.
8. Roles y responsabilidad individual
Los equipos pueden distribuir responsabilidades (coordinación, interfaz, datos, backend, pruebas, documentación), pero los roles no son “propiedad exclusiva” de una persona. Todos deberán comprender el producto completo y poder explicar las decisiones principales.
| La evaluación será grupal e individual. La existencia de un buen producto no reemplaza la necesidad de demostrar participación, comprensión y aporte personal. |
| --- |

La evaluación será grupal e individual. La existencia de un buen producto no reemplaza la necesidad de demostrar participación, comprensión y aporte personal.
9. Uso de Inteligencia Artificial
Se permite utilizar herramientas de IA como apoyo para investigar, programar, depurar, documentar o generar alternativas. Su uso no reemplaza la comprensión.
- El equipo es responsable de todo código incorporado al proyecto, aunque haya sido generado con IA.
- Cualquier integrante podrá ser consultado sobre una funcionalidad o fragmento del sistema.
- Se valorará la capacidad para detectar errores, modificar código y justificar decisiones.
- No se evaluará la cantidad de código escrito manualmente, sino la calidad de la solución y el dominio del proyecto.
10. Entregables por etapas
| Etapa | Entregable | Objetivo | Resultado esperado |
| --- | --- | --- | --- |
| 1. Descubrimiento | Ficha de problema | Definir necesidad, usuario y contexto. | Problema aprobado por el cliente. |
| 2. Alcance | MVP + backlog | Priorizar lo imprescindible. | Alcance viable y acordado. |
| 3. Análisis | Historias/requisitos + modelo conceptual de datos | Traducir necesidad a comportamiento y datos. | Requisitos verificables. |
| 4. Diseño | Modelo de datos + prototipo + arquitectura | Definir estructura antes de implementar. | Diseño aprobado. |
| 5. Implementación inicial | Primer flujo completo | Conectar interfaz, lógica y persistencia. | Incremento demostrable. |
| 6. MVP | Aplicación funcional | Completar el núcleo del problema. | MVP usable. |
| 7. Calidad | Pruebas, correcciones y evidencias | Verificar funcionamiento y datos. | Sin errores críticos conocidos. |
| 8. Cierre | Repo, documentación, demo y defensa | Entregar y justificar el trabajo. | Versión final defendible. |

Etapa
Entregable
Objetivo
Resultado esperado
1. Descubrimiento
Ficha de problema
Definir necesidad, usuario y contexto.
Problema aprobado por el cliente.
2. Alcance
MVP + backlog
Priorizar lo imprescindible.
Alcance viable y acordado.
3. Análisis
Historias/requisitos + modelo conceptual de datos
Traducir necesidad a comportamiento y datos.
Requisitos verificables.
4. Diseño
Modelo de datos + prototipo + arquitectura
Definir estructura antes de implementar.
Diseño aprobado.
5. Implementación inicial
Primer flujo completo
Conectar interfaz, lógica y persistencia.
Incremento demostrable.
6. MVP
Aplicación funcional
Completar el núcleo del problema.
MVP usable.
7. Calidad
Pruebas, correcciones y evidencias
Verificar funcionamiento y datos.
Sin errores críticos conocidos.
8. Cierre
Repo, documentación, demo y defensa
Entregar y justificar el trabajo.
Versión final defendible.
11. Criterios generales de evaluación
- Calidad de la definición del problema y pertinencia de la solución.
- Capacidad para delimitar un MVP realista.
- Organización y trabajo colaborativo.
- Aplicación correcta de MVC y del diseño de datos.
- Funcionamiento del producto y cumplimiento de requisitos.
- Pruebas, seguridad básica y manejo de errores.
- Uso responsable de Git, documentación y evidencias de avance.
- Capacidad de explicar decisiones, dificultades y aprendizajes.
- Aporte individual verificable.
12. Plantilla para presentar la idea del proyecto
Nombre provisorio del proyecto:________________________________________________________________________________
Integrantes:________________________________________________________________________________
Problema o necesidad:________________________________________________________________________________
Usuario/s afectados:________________________________________________________________________________
Cómo se resuelve hoy:________________________________________________________________________________
Consecuencias o dificultades actuales:________________________________________________________________________________
Solución propuesta:________________________________________________________________________________
MVP - funciones imprescindibles:________________________________________________________________________________
Funciones deseables / backlog futuro:________________________________________________________________________________
Datos principales que deberá manejar:________________________________________________________________________________
Stack propuesto:________________________________________________________________________________
Justificación del stack:________________________________________________________________________________
Riesgos o dudas iniciales:________________________________________________________________________________

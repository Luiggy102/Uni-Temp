# Uni-Temp 🌡️

**Uni-Temp** es una plataforma centralizada diseñada para el monitoreo, registro y análisis de la temperatura en infraestructuras universitarias. El sistema permite a los administradores capturar datos térmicos de aulas distribuidas en diferentes campus y edificios, transformando esos datos en información estratégica a través de un dashboard de analíticas avanzado.

![WhatsApp Image 2026-01-31 at 12 17 48](https://github.com/user-attachments/assets/c6e1ef7b-3b6c-4f04-a4ac-68440160d0fd)
<img width="1060" height="553" alt="Screenshot From 2026-01-31 12-19-19" src="https://github.com/user-attachments/assets/cf31b48f-78d8-4156-824a-136bcdf69afa" />

## 🏗️ Arquitectura en la Nube (Cloud-Native)

<img width="595" height="577" alt="Screenshot From 2026-01-31 12-10-04" src="https://github.com/user-attachments/assets/de1f4be2-cc3c-474e-9bf4-671cff1dcb20" />

Aunque el ingreso de datos es a través de una interfaz web, el sistema ha sido construido bajo una arquitectura desacoplada y escalable, utilizando servicios de **Amazon Web Services (AWS)**:

1.  **Frontend Administrativo (Laravel + AWS Beanstalk):** Interfaz robusta para la gestión de aulas y el reporte de datos.
2.  **Orquestación de Mensajes (AWS SQS):** El registro de temperaturas se procesa de forma asíncrona a través de colas, garantizando que el sistema nunca se bloquee, sin importar el volumen de tráfico.
3.  **Procesamiento Serverless (AWS Lambda):** Un worker independiente se encarga de procesar los mensajes de la cola y persistirlos.
4.  **Almacenamiento NoSQL (AWS DynamoDB):** Base de datos de alto rendimiento para el almacenamiento de series temporales de temperatura.

<img width="595" height="577" alt="Screenshot From 2026-01-31 12-10-04" src="https://github.com/user-attachments/assets/fd14a972-dea3-42c9-8484-38ca993bee68" />


## ✨ Características Principales

* **Dashboard de Analíticas:** Visualización de promedios por hora, detección de "puntos calientes" y KPIs generales de salud térmica del campus.
* **Gestión de Aulas (CRUD):** Control total sobre la estructura física de la universidad (Campus, Edificios y Aulas) con filtros inteligentes.
* **Filtros Encadenados:** Lógica de búsqueda avanzada que adapta los edificios y aulas disponibles según el campus seleccionado.
* **Reportes Profesionales:** Exportación de datos a Excel y PDF en formato horizontal para auditorías de infraestructura.
* **Arquitectura "IoT-Ready":** Diseñado para que, en una fase futura, los sensores físicos puedan enviar datos directamente a la cola SQS sin modificar el núcleo del sistema.

## 🛠️ Stack Tecnológico

* **Framework:** Laravel 11 / PHP 8.x
* **Base de Datos:** AWS DynamoDB (NoSQL)
* **Infraestructura:** AWS (SQS, Lambda, Elastic Beanstalk)
* **Frontend:** Bootstrap 5, Chart.js, DataTables (jQuery)

## 🚀 Instalación

1. Clona el repositorio.
2. Configura tus credenciales de AWS en el archivo `.env`.
3. Ejecuta `composer install` y `npm install`.
4. Lanza el servidor con `php artisan serve`.

---
Desarrollado como una solución escalable para la gestión de climas en entornos educativos.

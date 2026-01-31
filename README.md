# Uni-Temp 🌡️

**Uni-Temp** es una plataforma centralizada diseñada para el monitoreo, registro y análisis de la temperatura en infraestructuras universitarias. El sistema permite a los administradores capturar datos térmicos de aulas distribuidas en diferentes campus y edificios, transformando esos datos en información estratégica a través de un dashboard de analíticas avanzado.

## 🏗️ Arquitectura en la Nube (Cloud-Native)

Aunque el ingreso de datos es a través de una interfaz web, el sistema ha sido construido bajo una arquitectura desacoplada y escalable, utilizando servicios de **Amazon Web Services (AWS)**:

1.  **Frontend Administrativo (Laravel + AWS Beanstalk):** Interfaz robusta para la gestión de aulas y el reporte de datos.
2.  **Orquestación de Mensajes (AWS SQS):** El registro de temperaturas se procesa de forma asíncrona a través de colas, garantizando que el sistema nunca se bloquee, sin importar el volumen de tráfico.
3.  **Procesamiento Serverless (AWS Lambda):** Un worker independiente se encarga de procesar los mensajes de la cola y persistirlos.
4.  **Almacenamiento NoSQL (AWS DynamoDB):** Base de datos de alto rendimiento para el almacenamiento de series temporales de temperatura.



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
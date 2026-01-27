# 4VCHEF API

Repositorio donde se guarda la api del proyecto 4VCHEF de Sergio de la Fuente.

API REST desarrollada con Symfony para la gestión de recetas de cocina.
Permite crear, consultar, eliminar y valorar recetas, así como gestionar
catálogos fijos de tipos de receta y valores nutricionales.

Esta API está pensada para ser consumida por un frontend desarrollado en Angular.

## Requisitos previos

- Tener un Entorno de desarrollo, como VSCode
- PHP (8.1 o superior)
- Composer
- Symfony CLI
- xampp
- HeidiSQL

## Instalación del proyecto

1. **Ubícate en tu workspace**
2. **Clona el repositorio**
    ```bash
    git clone https://github.com/Sergiodlf/4VChef.git
    ```
3. **Abre una terminal desde dentro de la carpeta principal del proyecto e instala composer**
   ```bash
   composer install
   ```
4. **Configurar la base de datos en el archivo .env dentro de la carpeta principal**
   ```bash
   DATABASE_URL="mysql://root@127.0.0.1:3306/4vchef?serverVersion=10.4.32-MariaDB&charset=utf8mb4"
   ```
5. **Inicia MySQL desde xampp**
6. **Crea la base de datos**
   ```bash
   php bin/console doctrine:database:create
   ```
7. **Ejecuta migraciones**
   ```bash
   php bin/console make:migration
   ```
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
8. **Carga los datos iniciales**
   ```bash
   php bin/console app:load-mock-data
   ```
9. **Lanza la api**
   ```bash
   symfony server:start
   ```
10. **Comprueba que funciona desde el navegador:** (deberían aparecen los tipos de recetas de los datos iniciales)
   ```bash
   localhost:8000/recipe-types
   ```

## Estructura del proyecto

- **`src/`**: Lógica principal de la api.
  - **`Command/`**: Estará el archivo "MockDataCommand.php" con los datos iniciales de la base de datos.
  - **`Controller/`**: Controllers con los Endpoints de recetas, tipos de recetas y tipos de nutrientes.
  - **`Entity/`**: Entidades de la base de datos.
  - **`Model/`**: DTOs de entrada para la base de datos.
  - **`Repository/`**
- **`config/`**: Archivos de configuración de la api.
- **`migrations/`**: En esta carpeta aparecerán los archivos de migración de la base de datos automáticamente.
- **`4VChef.yaml/`**: Especificaciones OpenAPI

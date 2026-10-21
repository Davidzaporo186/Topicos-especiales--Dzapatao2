# Tutorial 05 — Laravel 12 con Docker en Google Cloud

**Materia:** Tópicos Especiales  
**Repositorio de ejercicios:** [Topicos-especiales--Dzapatao2](https://github.com/Davidzaporo186/Topicos-especiales--Dzapatao2)  
**Referencia:** Tutorial 05 proporcionado por el docente (secciones A–F).

## Objetivo

Desplegar una aplicación Laravel 12 dentro de un contenedor Docker sobre una máquina virtual de Google Compute Engine, con conexión a una base de datos MySQL de Cloud SQL.

Este proyecto contiene un `Dockerfile` reproducible: descarga Laravel 12 con Composer **durante la construcción de la imagen**, no requiere descargar un proyecto Laravel por separado. Expone la ruta `/` y la comprobación `/salud`.

> **Estado:** Código y guía preparados para despliegue. **No se afirma que Google Cloud haya sido configurado ni que el despliegue esté comprobado.**

## A. Crear máquina virtual en GCP

1. Ingresar a https://console.cloud.google.com/ con la cuenta institucional y verificar créditos.
2. Activar **Compute Engine** y crear instancia **`proyecto-laravel-docker`**, tipo **`e2-micro`**, imagen **Debian 12**.
3. Permitir tráfico HTTP (80). El tutorial también activa HTTPS, pero aquí no se configura TLS; se puede habilitar después con un proxy y dominio.
4. Guardar la IP externa. Los servicios pueden generar cobros.

## B. Acceso a la VM

Desde **Compute Engine > VM instances**, elegir **SSH**.

## C. Instalar Docker en Debian 12

Los comandos del documento para `apt-key` y el repositorio `buster` son antiguos y no corresponden a Debian 12. Se aporta instalación con el repositorio oficial actual:

```bash
sudo apt-get update
sudo apt-get install -y ca-certificates curl
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/debian/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc
. /etc/os-release
printf 'deb [arch=%s signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/debian %s stable\n' "$(dpkg --print-architecture)" "$VERSION_CODENAME" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
sudo docker version
sudo docker run --rm hello-world
```

## D. Crear base de datos Cloud SQL

1. En Google Cloud, ir a **Cloud SQL > Create instance > MySQL**.
2. Elegir la configuración más económica apropiada según los créditos, y verificar el costo estimado antes de crearla. El material menciona Enterprise, Sandbox o Development y MySQL 8.4 según disponibilidad.
3. Crear la base de datos **`laravelproject`** con `utf8mb4`.
4. Crear un usuario **`laravel_user`** con contraseña fuerte y privilegios necesarios en esa base de datos. Evitar usar `root` para la aplicación.
5. En **Connections**, configurar conectividad desde la VM. **No añadir `0.0.0.0/0`:** eso abre el servidor de bases de datos a cualquier IP. Es preferible **IP privada** dentro de la misma VPC; si se usa IP pública para este taller, autorizar **únicamente la IP de salida real** de la VM con sufijo `/32`, y configurar TLS o el Cloud SQL Auth Proxy según el tipo de conexión. Para una IP pública estable, considerar IP estática o Cloud NAT según arquitectura.
6. Guardar la IP o el método de conexión sin publicarlo en capturas con credenciales.

**Advertencia:** la aplicación de muestra usa acceso TCP directo configurable mediante `DB_HOST`. Si la instancia requiere certificados TLS u otra topología, debe adaptarse la configuración; no se desactiva el cifrado por defecto.

## E. Descargar y configurar en la VM

```bash
git clone https://github.com/Davidzaporo186/Topicos-especiales--Dzapatao2.git
cd Topicos-especiales--Dzapatao2/Tutorial-05-Laravel-Docker
cp .env.example .env
nano .env
```

Cambiar en `.env` los valores `APP_KEY`, `APP_URL`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`. Para generar una clave de Laravel sin instalar PHP en el host:

```bash
sudo docker run --rm php:8.3-cli php -r 'echo "base64:".base64_encode(random_bytes(32)).PHP_EOL;'
```

Copiar el resultado completo al valor `APP_KEY`. **Nunca subir `.env` al repositorio.**

Comprobar instalación:

```bash
sudo docker ps
sudo docker run --rm hello-world
```

## F. Construir y ejecutar Laravel

```bash
sudo docker build -t laravel-app .
sudo docker run -d --name laravel-docker --restart unless-stopped \
  --env-file .env -p 80:80 laravel-app
sudo docker ps
sudo docker logs --tail 50 laravel-docker
```

Abrir en el navegador `http://IP_EXTERNA_VM/` y `http://IP_EXTERNA_VM/salud`. El tutorial del profesor incluye el sufijo `/public/` para su proyecto de ejemplo; en esta configuración **no es necesario**, porque Laravel escucha directamente desde su carpeta pública mediante `artisan serve`.

La ruta `/salud` devuelve JSON. Para una primera prueba de conexión MySQL:

```bash
sudo docker exec laravel-docker php artisan migrate --force
```

Las migraciones requieren acceso efectivo a Cloud SQL y credenciales correctas. Si se ejecutan en producción, revisar primero las tablas que se crearán.

## Prueba opcional local (sin GCP)

Crear `.env` con una clave válida y ejecutar:

```bash
docker compose up --build -d
docker compose exec laravel php artisan migrate --force
```

Abrir http://localhost:8080/ y http://localhost:8080/salud . `compose.yaml` incluye un MySQL **solo para pruebas locales**; en GCP se utiliza Cloud SQL.

## Pregunta final del tutorial

**¿Qué ventaja tiene utilizar Docker para desplegar un proyecto en GCP frente a desplegarlo directamente en una máquina virtual?**

Docker permite empaquetar la aplicación con una versión definida de PHP, las extensiones requeridas y sus dependencias. Así, el entorno se puede reproducir en diferentes computadoras y servidores, se reducen problemas de configuración y es más fácil actualizar o reemplazar el contenedor sin reinstalar manualmente toda la aplicación. Sin embargo, Docker no elimina la necesidad de configurar redes, credenciales, bases de datos, costos y seguridad en GCP.

## Comandos útiles

```bash
sudo docker ps -a
sudo docker logs -f laravel-docker
sudo docker exec -it laravel-docker sh
sudo docker rm -f laravel-docker
sudo docker image ls
```

No ejecutar `docker system prune -a` a ciegas: puede borrar imágenes de otros proyectos que se necesiten.

## Evidencias

Ver [docs/EVIDENCIAS.md](docs/EVIDENCIAS.md). No se aportan evidencias ficticias de ejecución.

## Costos y seguridad

- Cloud SQL puede consumir rápidamente créditos; **detener la instancia cuando no se use**.
- No publicar contraseñas, claves ni `.env`.
- La demostración usa HTTP para seguir el ejercicio. Para un servicio real debe implementarse HTTPS, por ejemplo con balanceador de carga o proxy inverso.
- `php artisan serve` resulta adecuado para este taller, **no para una arquitectura de producción de alto tráfico**.

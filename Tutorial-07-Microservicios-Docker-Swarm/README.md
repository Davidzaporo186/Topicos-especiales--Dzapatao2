# Tutorial 07 — Microservicios con Docker Swarm en GCP

**Materia:** Tópicos Especiales | **Proyecto:** `randomquotes` con Express + Node.js 24.

Este repositorio contiene el código y la automatización correspondientes al **Tutorial 07**. Amplía el Tutorial 06: la ruta `/` responde una frase aleatoria seguida del **ID del contenedor** (`os.hostname()`), para observar el balanceo entre réplicas en un clúster Docker Swarm de dos máquinas virtuales. El documento de clase especifica **cuatro réplicas**.

> **Estado:** código preparado; despliegue en Google Cloud, publicación en Docker Hub y capturas **pendientes de realizar con las cuentas del estudiante**. No afirmar que se completaron pruebas no ejecutadas.

## Archivos

- `index.js`: aplicación Express y contenedor identificado en cada respuesta.
- `package.json`: dependencias y script `npm start`.
- `Dockerfile`: construcción sobre Node 24 Alpine; escucha en **puerto 80**.
- `.github/workflows/docker-image.yml`: publica la imagen en Docker Hub cuando se hace push a `main`.
- `scripts/gcp-startup-docker.sh`: instalación de Docker en Debian 12 para la plantilla GCP.
- `scripts/comandos-swarm.sh`: comandos comentados para iniciar el clúster, desplegar y verificar.
- `evidencias/`: lista de las capturas reales solicitadas.

## A. Plantilla de instancia y dos máquinas virtuales (GCP)

1. Abrir [Google Cloud Console](https://console.cloud.google.com/) > Compute Engine > **Instance templates** > Create.
2. Elegir **e2-micro**, imagen **Debian 12** y activar HTTP/HTTPS en la configuración de firewall correspondiente.
3. En **Management > Automation > Startup script**, copiar el contenido de `scripts/gcp-startup-docker.sh` (actualizado para Debian 12; el tutorial usa el repositorio obsoleto `buster` y `apt-key`).
4. Crear el grupo **instance-group-micro** con la plantilla, **autoscaling desactivado** y **2 instancias**. Registrar la **IP privada** de cada una.
5. Verificar en cada VM por SSH: `docker -v` o `sudo docker version`.

**Seguridad y funcionamiento:** los dos nodos necesitan comunicación **interna** entre sí mediante TCP **2377**, TCP/UDP **7946** y UDP **4789**. Configurar una regla de firewall VPC restringida únicamente a los nodos/red privada del grupo, no abrir esos puertos a Internet. HTTP entrante al puerto 80 para comprobar la app. Revisar las reglas de firewall reales de tu VPC.

**Costos:** GCP puede cobrar por VMs, discos, IPs y otros recursos; revisar facturación y borrar el grupo de instancias cuando termines. En un grupo administrado, detener manualmente una VM puede hacer que sea recreada.

## B. Inicializar Docker Swarm (nodo-01)

Conectar por SSH a la primera VM:

```bash
sudo docker swarm init --advertise-addr IP_PRIVADA_NODO_01
sudo docker swarm join-token manager
sudo docker node ls
```

Guarda el comando de unión **de forma privada**, porque el token permite que otros nodos se unan al clúster. El tutorial utiliza dos managers. En producción normalmente se prefieren **3 o 5 managers** para tolerancia de fallos; **2 managers no ofrecen alta disponibilidad** si falla uno.

## C. Unir nodo-02

En la segunda VM, pega el comando `sudo docker swarm join ...` obtenido en el paso anterior (sin subir tokens a GitHub). Vuelve al nodo-01:

```bash
sudo docker node ls
```

Deben verse **dos nodos Ready**.

## D. Cambiar randomquotes

Se agregó `const os = require('node:os')` y la ruta principal devuelve una frase y `Container Id: <hostname>`. Para ejecutar localmente con Node 24:

```bash
npm install
npm start
```

La aplicación usa **puerto 80** por defecto como pide la fase final del tutorial; en Windows o entornos sin permisos de puerto 80, ejecuta `PORT=3000 npm start` (Linux/macOS) o `$env:PORT=3000; npm start` (PowerShell), y visita `http://localhost:3000/`.

## E. Publicar la imagen en Docker Hub desde GitHub Actions

1. Crear un repositorio público en Docker Hub llamado **`express-randomquotes`**.
2. En GitHub > **Settings > Secrets and variables > Actions**, crear **dos Repository secrets**:
   - `DOCKERHUB_USER`: nombre de usuario de Docker Hub.
   - `DOCKERHUB_TOKEN`: **access token** de Docker Hub con permisos de lectura/escritura (**no colocar la contraseña**).
3. Subir todos los archivos de este proyecto a la **raíz de un repositorio nuevo**, con rama principal `main`. El archivo `.github/workflows/docker-image.yml` **debe estar en la raíz** del repo para que GitHub Actions lo detecte.
4. En **Actions**, confirmar que **Docker Image CI** termina correctamente; en Docker Hub debe existir la etiqueta `TU_USUARIO/express-randomquotes:latest`.

> El documento original solicita `DOCKERHUB_PASS` y contraseña; aquí se sustituye por un **token** para reducir exposición de credenciales. Tampoco se muestran tokens de unión a Swarm.

## F. Servicio Docker Swarm con 4 réplicas

Una vez publicada la imagen, en **nodo-01**:

```bash
sudo docker service ls
sudo docker service create --name randomquotes --replicas 4 --publish published=80,target=80,mode=ingress TU_USUARIO/express-randomquotes:latest
sudo docker service ps randomquotes
sudo docker service ls
```

En ambos nodos, inspeccionar contenedores:

```bash
sudo docker container ls
```

Visitar `http://IP_PUBLICA_NODO_01/` y `http://IP_PUBLICA_NODO_02/`, recargar varias veces (p. ej. modo incógnito). La respuesta incluye `Container Id: ...` y puede variar porque Swarm distribuye solicitudes. **La distribución exacta 2+2 del ejemplo no es garantizada** por el planificador.

### Actividad: eliminar un contenedor

En nodo-01, elegir **solo un contenedor de la aplicación**:

```bash
sudo docker container ls
sudo docker container rm -f ID_DE_UN_CONTENEDOR_RANDOMQUOTES
sudo docker service ps randomquotes
```

**¿Qué pasó?** Docker Swarm detecta que una tarea dejó de existir y programa un contenedor de reemplazo para recuperar el número deseado de cuatro réplicas. La ubicación de la nueva tarea puede ser distinta; verificar con `docker service ps randomquotes`.

### Actualizar el servicio tras un push

GitHub Actions construye y publica `latest`, **pero eso no actualiza automáticamente un servicio que ya está ejecutándose**. Después del push, en nodo-01:

```bash
sudo docker service update --image TU_USUARIO/express-randomquotes:latest --force randomquotes
```

## Verificación y limpieza

- Probar la sintaxis: `node --check index.js`.
- Si Docker está disponible: `docker build -t randomquotes:local .` y `docker run --rm -p 8080:80 randomquotes:local` (navegar a `http://localhost:8080`).
- Guardar capturas **reales** según `evidencias/README.md` y entregar el enlace del repositorio en Teams.
- Al finalizar, eliminar el servicio y recursos de GCP que ya no se necesiten para evitar costos:

```bash
sudo docker service rm randomquotes
```

## Referencia de clase

Presentación 13-B — Tutorial 07: microservicios, Docker Swarm, Docker service, GitHub Actions, Docker Hub y Google Cloud (18 páginas). Las partes de visualizador y despliegue automático avanzado son **retos opcionales**, no se afirman como realizados.

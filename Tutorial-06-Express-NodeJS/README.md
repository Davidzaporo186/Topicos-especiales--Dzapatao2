# Tutorial 06 — Express y Node.js 24 con Docker en Google Cloud

**Asignatura:** Tópicos Especiales  
**Proyecto:** `randomquotes`  
**Objetivo:** crear una aplicación Express que devuelva una frase aleatoria al entrar a `/` y desplegarla dentro de Docker en una VM de Google Cloud Compute Engine.

> Preparado como código de entrega. La creación de recursos en GCP y la ejecución en la nube **deben realizarse con la cuenta del estudiante**. No se incluyen capturas ficticias ni se afirma que el despliegue ya se completó.

## Estructura

```text
Tutorial-06-Express-NodeJS/
├── README.md
├── randomquotes/
│   ├── .dockerignore
│   ├── .gitignore
│   ├── Dockerfile
│   ├── index.js
│   └── package.json
└── evidencias/
    └── README.md
```

## A. Instalación de Node 24

Instalar Node 24 desde https://nodejs.org/en/download y comprobar:

```bash
node -v
npm -v
```

La versión de Node debe comenzar con `v24.`.

## B. Creación del proyecto Express

El proyecto `randomquotes/` ya contiene el código. Para ejecutarlo en el equipo local:

```bash
cd randomquotes
npm install
```

En **PowerShell**:

```powershell
$env:PORT=3000
npm start
```

En **Linux/macOS**:

```bash
PORT=3000 npm start
```

Abrir http://localhost:3000/. La respuesta es una frase en inglés. Pulsar Actualizar para obtener una nueva selección.

La guía original parte de `Hello World!`; aquí se entrega la **versión final** requerida, que responde con frases aleatorias.

## C. Generación de frases aleatorias

`index.js` define las siete frases del documento y selecciona una mediante:

```js
const number = Math.floor(Math.random() * phrases.length);
res.send(phrases[number]);
```

Se usa `phrases.length` para mantener la selección correcta si se agregan frases.

## D. Despliegue con Docker

### 1. Crear VM en Google Cloud

Entrar a https://console.cloud.google.com/ con la cuenta institucional. En **Compute Engine → VM instances → Create instance** crear una VM Debian 12, por ejemplo `proyecto-express-docker`, seleccionando el tamaño apropiado (la guía previa emplea `e2-micro`). Activar **Allow HTTP traffic** para permitir acceso web. Revisar créditos y costo antes de crear la instancia.

### 2. Instalar Docker en Debian 12

Conectar por SSH a la máquina e instalar Docker desde el repositorio actual de Debian:

```bash
sudo apt-get update
sudo apt-get install -y ca-certificates curl git
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/debian/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc
. /etc/os-release
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/debian ${VERSION_CODENAME} stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
sudo docker run --rm hello-world
```

### 3. Clonar el proyecto desde GitHub

**Opción recomendada (subir este ZIP como carpeta a su repositorio existente):**

```bash
git clone https://github.com/Davidzaporo186/Topicos-especiales--Dzapatao2.git
cd Topicos-especiales--Dzapatao2/Tutorial-06-Express-NodeJS/randomquotes
```

**Si el docente exige repositorio nuevo (como dice la página 6),** cree ese repositorio en GitHub, suba esta carpeta y reemplace los dos comandos anteriores por la URL y ruta de ese repositorio nuevo.

### 4. Construir la imagen y ejecutar el contenedor

Desde `randomquotes`:

```bash
sudo docker build -t randomquotes-app .
sudo docker run -d --name randomquotes -p 80:80 --restart unless-stopped randomquotes-app
sudo docker ps
sudo docker logs randomquotes
```

Abrir `http://IP_EXTERNA_DE_LA_VM/` en el navegador (reemplace `IP_EXTERNA_DE_LA_VM`). No se necesita Cloud SQL para este tutorial.

> **Aclaración técnica:** el documento muestra `EXPOSE 8080` en el Dockerfile y cambia `index.js` a puerto 80. En este proyecto ambos usan el puerto **80** para evitar confusión al publicar `-p 80:80`. Para la prueba local se usa `PORT=3000`.

### 5. Comandos útiles

```bash
sudo docker ps -a
sudo docker logs randomquotes
sudo docker stop randomquotes
sudo docker start randomquotes
sudo docker rm -f randomquotes
```

Si el contenedor tiene problemas de acceso, verificar que el firewall de GCP permita TCP:80, que la VM tenga IP externa y que el servicio siga activo.

## Verificación y evidencias para Teams

1. Captura del comando `node -v` con versión 24.
2. Captura local en `http://localhost:3000/` mostrando una frase.
3. Captura de `sudo docker ps` con `randomquotes` activo.
4. Captura del navegador con `http://IP_EXTERNA_DE_LA_VM/` mostrando una frase.
5. Enviar al docente el enlace al repositorio de GitHub desde Teams.

Guardar las capturas **reales** en `evidencias/` cuando se complete el ejercicio.

## Referencia académica

Basado en el documento de clase **“Presentación-12-B-Tutorial-express-nodejs.docx”, Tutorial 06**, apartados A–D. El siguiente tutorial y el Taller 2 reutilizan este despliegue.

**Importante:** al terminar, detener o eliminar recursos de GCP que no se necesiten para evitar cargos. Revisar antes si el próximo taller requiere conservar la VM.

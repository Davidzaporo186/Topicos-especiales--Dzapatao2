#!/usr/bin/env bash
set -euo pipefail
# Ejecutar desde la carpeta Tutorial-05-Laravel-Docker en la VM.
if [ ! -f .env ]; then
  echo 'Primero crea .env desde .env.example y completa los datos.' >&2
  exit 1
fi
if grep -Eq 'REEMPLAZAR|IP_PRIVADA_O_PUBLICA_SQL|IP_EXTERNA_VM' .env; then
  echo 'Hay valores de ejemplo sin configurar en .env.' >&2
  exit 1
fi
sudo docker build -t laravel-app .
sudo docker rm -f laravel-docker 2>/dev/null || true
sudo docker run -d --name laravel-docker --restart unless-stopped \
  --env-file .env -p 80:80 laravel-app
sudo docker ps --filter name=laravel-docker

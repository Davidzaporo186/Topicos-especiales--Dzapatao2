#!/usr/bin/env bash
# GUÍA DE COMANDOS: no ejecutar todo el archivo automáticamente.
# Nodo 01: obtener IP privada desde Compute Engine y colocarla a continuación.
# sudo docker swarm init --advertise-addr IP_PRIVADA_NODO_01
# sudo docker swarm join-token manager
# Nodo 02: ejecutar comando join mostrado en nodo 01 (mantener token privado).
# Nodo 01:
# sudo docker node ls
# sudo docker service create --name randomquotes --replicas 4 --publish published=80,target=80,mode=ingress USUARIO_DOCKERHUB/express-randomquotes:latest
# sudo docker service ls
# sudo docker service ps randomquotes
# curl http://IP_PUBLICA_NODO_01/
# Actualización tras nueva imagen Docker Hub:
# sudo docker service update --image USUARIO_DOCKERHUB/express-randomquotes:latest --force randomquotes
# Experimento: eliminar una tarea de servicio y observar su recreación automática:
# sudo docker container ls
# sudo docker container rm -f ID_CONTENEDOR
# sudo docker service ps randomquotes
# Limpieza al finalizar (solo si ya no necesitas el taller):
# sudo docker service rm randomquotes

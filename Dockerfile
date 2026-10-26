FROM serversideup/php:8.3-fpm-nginx

USER root

RUN apt-get update \
    && apt-get install -y --no-install-recommends mariadb-client \
    && rm -rf /var/lib/apt/lists/*

USER www-data
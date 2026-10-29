FROM serversideup/php:8.3-fpm-nginx

USER root

RUN install-php-extensions gd

RUN apt-get update \
    && apt-get install -y --no-install-recommends mariadb-client libreoffice-writer fonts-liberation \
    && rm -rf /var/lib/apt/lists/*

USER www-data
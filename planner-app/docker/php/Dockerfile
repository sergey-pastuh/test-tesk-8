FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    netcat-openbsd \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ARG UID=1000
ARG GID=1000

RUN addgroup --gid $GID appgroup \
    && adduser --disabled-password --gecos "" --uid $UID --gid $GID appuser

WORKDIR /var/www/html

USER appuser

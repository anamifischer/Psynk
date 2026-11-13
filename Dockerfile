FROM php:8.2-apache

RUN apt-get update && apt-get install -y --no-install-recommends unzip git \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN a2enmod rewrite

RUN sed -i 's/DirectoryIndex index.html/DirectoryIndex pages\/index.php index.php index.html/' /etc/apache2/mods-enabled/dir.conf

WORKDIR /var/www/html

RUN sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf
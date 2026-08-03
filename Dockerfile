FROM php:8.2-apache

# Instala extensões do PHP mais usadas com MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilita mod_rewrite (útil pra URLs amigáveis no futuro)
RUN a2enmod rewrite

# Define a pasta do projeto como raiz do Apache
WORKDIR /var/www/html

# Permite que o .htaccess funcione, se você vier a usar
RUN sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

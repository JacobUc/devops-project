FROM php:8.2-apache

# Instala extensiones requeridas
RUN apt-get update && apt-get install -y \
    libzip-dev unzip git && \
    docker-php-ext-install pdo pdo_mysql zip

RUN a2enmod rewrite

# Copia todo el proyecto ya "compilado"
COPY . /var/www/html

WORKDIR /var/www/html

RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

COPY ./docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
CMD ["apache2-foreground"]

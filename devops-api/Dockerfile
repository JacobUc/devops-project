FROM php:8.2-cli

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    zip

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar aplicación (se usará bind mount en desarrollo)
COPY . .

# Sobreescribir el .env dentro del contenedor
COPY .env.docker .env

# Configurar permisos de almacenamiento
RUN mkdir -p storage/framework/{sessions,views,cache} && \
    chmod -R 775 storage

# Puertos expuestos
EXPOSE 8000

# Instalar dependencias
RUN composer i

# Iniciar servidor Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
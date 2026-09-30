FROM php:8.4-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Extensiones PHP necesarias para Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar proyecto Laravel
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Permisos de Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Railway utilizará el puerto que proporciona mediante PORT
CMD php artisan serve --host=0.0.0.0 --port=${PORT}

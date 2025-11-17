FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    libpq-dev

# Install PostgreSQL drivers
RUN docker-php-ext-install pdo pdo_pgsql

# Install other Laravel extensions
RUN docker-php-ext-install mbstring zip exif pcntl

# Copy project files
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Copy env file
COPY .env.example .env

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Generate app key
RUN php artisan key:generate

CMD php artisan serve --host=0.0.0.0 --port=$PORT
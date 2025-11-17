FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip

# Install MySQL drivers
RUN docker-php-ext-install pdo pdo_mysql

# Install other Laravel extensions
RUN docker-php-ext-install mbstring zip exif pcntl

# Copy project files
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

CMD php artisan serve --host=0.0.0.0 --port=$PORT
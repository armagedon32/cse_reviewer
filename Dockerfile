# Railway Deployment - Fixed for nginx/php-fpm
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libmariadb-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    nginx \
    supervisor

RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chmod -R 775 storage bootstrap/cache public

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]

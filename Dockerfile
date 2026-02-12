FROM php:8.3-fpm

# Установка сразу от root (так и должно быть)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libjpeg-dev \
    libfreetype6-dev \
    default-mysql-client \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/* \
    && update-ca-certificates

RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        xml \
        dom

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN useradd -G www-data,root -u 1000 -m developer

WORKDIR /var/www/html

USER developer

EXPOSE 9000

CMD ["php-fpm"]

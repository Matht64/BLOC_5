FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    unzip \
    zlib1g-dev \
    libwebp-dev \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    default-mysql-client \
    git \
    nodejs \
    npm \
    && docker-php-ext-install \
        gd \
        zip \
        intl \
        opcache \
        pdo \
        pdo_mysql \
    && docker-php-ext-enable pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN git config --global --add safe.directory /var/www/html

WORKDIR /var/www/html

COPY composer.json composer.lock* package.json package-lock.json* ./

RUN composer install --no-dev --no-scripts --optimize-autoloader \
    && npm install --no-audit --no-fund --omit=dev

COPY . .

RUN composer dump-autoload --optimize

RUN a2enmod rewrite

RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html
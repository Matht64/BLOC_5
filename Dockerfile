FROM php:8.2-apache AS base

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libpng-dev \
    libzip-dev \
    unzip \
    libwebp-dev \
    libicu-dev \
    default-mysql-client \
    git \
    nodejs \
    npm

RUN docker-php-ext-install \
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
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

RUN a2enmod rewrite

FROM base AS prod
COPY . .

RUN composer install --no-dev --optimize-autoloader \
    && npm install --omit=dev \
    && npm run build

FROM base AS dev
COPY package*.json composer*.json ./

RUN composer install --optimize-autoloader \
    && npm install
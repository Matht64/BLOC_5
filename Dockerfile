FROM php:8.2-apache

RUN apt-get update && apt-get upgrade -y \
    && apt-get install -y \
        zlib1g-dev \
        libwebp-dev \
        libpng-dev \
        libzip-dev \
        libicu-dev \
        default-mysql-client \
    && docker-php-ext-install \
        gd \
        zip \
        intl \
        opcache \
        pdo \
        pdo_mysql \
    && docker-php-ext-enable \
        pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY composer.json composer.lock* ./

RUN composer install --no-scripts --no-autoloader --no-dev

COPY . .

RUN composer dump-autoload --optimize

RUN a2enmod rewrite
RUN service apache2 restart

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 80
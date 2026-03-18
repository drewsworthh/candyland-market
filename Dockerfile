FROM php:8.2-apache

WORKDIR /var/www/html

# Install extensions and dependencies as needed
RUN docker-php-ext-install pdo pdo_mysql

COPY . /var/www/html

# Enable Apache rewrite module
RUN a2enmod rewrite

EXPOSE 80

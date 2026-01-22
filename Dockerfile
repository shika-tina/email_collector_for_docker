FROM php:8.2-fpm

# Install pdo_mysql extension for MySQL connection
RUN docker-php-ext-install pdo pdo_mysql

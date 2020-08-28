FROM composer:latest as build

WORKDIR /app

COPY . /app

FROM php:7.3-apache

EXPOSE 8080

COPY --from=build /app /var/www/

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

RUN chmod 777 -R /var/www

RUN echo "Listen 8080" >> /etc/apache2/ports.conf

RUN chown -R www-data:www-data /var/www

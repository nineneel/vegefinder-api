FROM composer:latest as build
WORKDIR /app
COPY . /app
RUN composer install

FROM php:8.1-apache
RUN docker-php-ext-install pdo pdo_mysql

EXPOSE 8080
COPY --from=build /app /var/www/
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY .env.example /var/www/.env

# Set the environment variables
ENV APP_URL=https://vegefinder-api-pl6a2qwedq-et.a.run.app
ENV ASSET_URL=https://vegefinder-api-pl6a2qwedq-et.a.run.app
ENV DB_CONNECTION=mysql
ENV DB_HOST=34.123.92.221
ENV DB_PORT=3306
ENV DB_DATABASE=vegefinder-db-dev
ENV DB_USERNAME=root
ENV DB_PASSWORD="vegefinder1234"
ENV GOOGLE_CLOUD_PROJECT_ID="vegefinder-bangkit"
ENV GOOGLE_CLOUD_STORAGE_BUCKET="vegefinder-bucket"

RUN chmod 777 -R /var/www/storage/ && \
    echo "Listen 8080" >> /etc/apache2/ports.conf && \
    chown -R www-data:www-data /var/www/ && \
    chown -R 775 /var/www/ && \
    chown -R 775 /var/www/storage && \
    chown -R 775 /var/www/bootstrap/cache && \
    a2enmod rewrite

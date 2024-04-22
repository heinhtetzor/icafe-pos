FROM php:8.1-fpm
WORKDIR /var/www/icafe-pos-migration

# linux libraries
RUN apt-get update -y && apt-get install -y \
    libicu-dev \
    libmariadb-dev \
    unzip zip \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP Extension
RUN docker-php-ext-install gettext intl pdo_mysql gd
RUN docker-php-ext-install opcache

RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd


#set our application folder as an environment variable
ENV APP_HOME /var/www/icafe-pos-migration
ENV COMPOSER_ALLOW_SUPERUSER=1

#change uid and gid of apache to docker user uid/gid
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# copy sources files
COPY . $APP_HOME

# change ownership of app
RUN chown -R www-data:www-data $APP_HOME

# install all PHP depedencies
RUN composer install --no-interaction

RUN php artisan key:generate

EXPOSE 9000

CMD ["php-fpm"]

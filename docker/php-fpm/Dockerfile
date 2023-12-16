FROM php:8.2-fpm AS build-base
##
# install needed php packages
RUN apt-get update  \
    && apt-get install -y tzdata curl zip unzip vim \
    && docker-php-ext-install pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
##
# set timezone
ENV TZ=Asia/Ho_Chi_Minh
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

##
# define working directory
WORKDIR /mnt/code

##
# stage for development
FROM build-base AS build-dev

# add packages for the development state
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# update user www-data with userid = 1000 and groupid = 1000
RUN usermod -u 1000 www-data \
    && groupmod -g 1000 www-data
USER www-data

COPY docker/php-fpm/custom-xdebug.ini /usr/local/etc/php/conf.d/custom-xdebug.ini
COPY docker/php-fpm/custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini




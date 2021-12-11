FROM php:8.1-apache

RUN apt update
RUN apt install -y \
    libzip-dev \
    libpq-dev

RUN docker-php-ext-install pdo pdo_pgsql zip

RUN mkdir /var/www/tmp

WORKDIR /var/www/tmp

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

WORKDIR /var/www

RUN mkdir koi-wiki
RUN mv ./html ./.html

COPY ./bin ./koi-wiki/bin
COPY ./composer.json ./koi-wiki/composer.json
COPY ./composer.lock ./koi-wiki/composer.lock
COPY ./symfony.lock ./koi-wiki/symfony.lock
COPY ./.env ./koi-wiki/.env
COPY ./config ./koi-wiki/config
COPY ./migrations ./koi-wiki/migrations
COPY ./public ./koi-wiki/public
COPY ./src ./koi-wiki/src
COPY ./templates ./koi-wiki/templates

WORKDIR /var/www/koi-wiki

RUN composer install

WORKDIR /var/www

RUN ln -s ./koi-wiki/public/ ./html

WORKDIR /var/www/koi-wiki
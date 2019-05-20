ARG PHP_IMAGE=php:7.2-fpm-alpine
ARG PHP_IMAGE_VENDOR=socialtech-composer:latest

FROM ${PHP_IMAGE_VENDOR} as app_vendor

# We don't need composer with cache inside image
FROM ${PHP_IMAGE}

# trust this project public key to trust the packages.
ADD https://dl.bintray.com/php-alpine/key/php-alpine.rsa.pub /etc/apk/keys/php-alpine.rsa.pub

RUN apk update
RUN apk add --no-cache --no-progress --virtual BUILD_DEPS ${PHPIZE_DEPS} \
            && apk add --no-cache --no-progress --virtual BUILD_DEPS_PHP_AMQP rabbitmq-c-dev \
            && apk add --no-cache --no-progress rabbitmq-c \
            && pecl install amqp \
            && docker-php-ext-enable amqp

RUN sed -i "s/\(user\|group\) = www-data/\1 = root/" /usr/local/etc/php-fpm.d/www.conf

# Set the WORKDIR to /app so all following commands run in /app
WORKDIR /app

COPY --from=app_vendor /app ./

COPY . ./

CMD ["php-fpm", "-R"]


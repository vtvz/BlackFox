FROM php:8.0.2-alpine

WORKDIR /var/www/app

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apk --no-cache add git libcurl curl libzip-dev curl-dev pcre-dev ${PHPIZE_DEPS} \
  libcap \
  && docker-php-ext-install curl zip sockets \
  && pecl install -o -f redis \
  && docker-php-ext-enable redis \
  && apk del pcre-dev ${PHPIZE_DEPS} curl-dev \
  && curl -L https://github.com/Droplr/aws-env/raw/v0.4/bin/aws-env-linux-amd64 -o /usr/bin/aws-env \
  && chmod +x /usr/bin/aws-env

COPY composer.* ./
RUN composer install --no-ansi --no-dev --no-interaction --no-progress --optimize-autoloader \
  && ./vendor/bin/rr get-binary -l /usr/bin -n \
  && setcap 'cap_net_bind_service=+ep' /usr/bin/rr

COPY . .

COPY .docker/entrypoint.sh /etc/entrypoint.sh
ENTRYPOINT ["/etc/entrypoint.sh"]
CMD ["rr", "serve", "--logFormat", "json", "--workDir", "/var/www/app", "--config", "roadrunner/.rr.yml", "--verbose", "--debug"]

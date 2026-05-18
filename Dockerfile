FROM dunglas/frankenphp:php8.3

RUN install-php-extensions \
    pcntl \
    redis \
    pdo_mysql


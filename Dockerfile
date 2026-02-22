FROM php:8.4-fpm

# Instalar dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libsqlite3-dev \
    && docker-php-ext-install zip pdo pdo_mysql pdo_sqlite

# Instalar Mysqli
RUN docker-php-ext-install mysqli

# Instalar XDebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do projeto
COPY . .

# Instalar dependências do projeto
RUN composer install --no-interaction --optimize-autoloader

# Configurar Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expor a porta do PHP-FPM
EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]

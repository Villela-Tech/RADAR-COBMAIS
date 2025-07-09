FROM php:8.1-fpm

# Instalar dependências
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www

# Copiar arquivos do projeto
COPY . /var/www

# Instalar dependências do projeto
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Configurar permissões
RUN chown -R www-data:www-data /var/www

# Expor porta
EXPOSE 9000

CMD ["php-fpm"] 
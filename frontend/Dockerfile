FROM php:8.3-apache

# Instala dependências do sistema e PHP
RUN apt-get update && apt-get install -y \
    zip unzip git curl ca-certificates zlib1g-dev \
    libzip-dev libonig-dev libpng-dev libjpeg-dev libfreetype-dev libicu-dev libxml2-dev locales \
    && docker-php-ext-install pdo_mysql mbstring zip intl opcache \
    && a2enmod rewrite

# Define diretório de trabalho
WORKDIR /var/www/html

# Copia o projeto para dentro do container
COPY . .

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Ajusta permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configuração do Apache
COPY apache-laravel.conf /etc/apache2/sites-available/000-default.conf

# Inicializa Apache
CMD ["apache2-foreground"]

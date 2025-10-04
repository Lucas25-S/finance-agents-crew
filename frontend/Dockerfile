# Usa uma imagem base do PHP com o servidor Apache
FROM php:8.3-apache

# Instala as dependências para compilar as extensões PHP
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype-dev \
    libicu-dev \
    libpq-dev \
    libxml2-dev \
    locales

# Define o diretório de trabalho dentro do container
WORKDIR /var/www/html

# Instala as extensões PHP necessárias para o Laravel
RUN docker-php-ext-install pdo_mysql mbstring

# Copia os arquivos do projeto para o diretêrio de trabalho
COPY . .

# Instala as dependências do Composer dentro do container
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Garante que as permissões dos arquivos sejam corretas para o servidor web
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Habilita o módulo de reescrita de URL do Apache (necessário para o Laravel)
RUN a2enmod rewrite

# Copia um arquivo de configuração do Apache que aponta para a pasta 'public' do Laravel
COPY apache-laravel.conf /etc/apache2/sites-available/000-default.conf

# Reinicia o servidor Apache para aplicar as configurações
CMD ["apache2-foreground"]
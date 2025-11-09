# Usa uma imagem base do PHP com o servidor Apache
FROM php:8.3-apache

# Define o diretório de trabalho
WORKDIR /var/www/html

# ✅ CORREÇÃO 1: Instala ferramentas de sistema essenciais (git, zip, unzip)
# e as bibliotecas para as extensões do PHP.
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libonig-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype-dev \
    libicu-dev \
    libpq-dev \
    libxml2-dev \
    locales \
    && rm -rf /var/lib/apt/lists/*

# ✅ CORREÇÃO 2: Instala a extensão 'zip' do PHP (necessária para o Composer)
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Habilita o módulo de reescrita do Apache
RUN a2enmod rewrite

# Copia os arquivos do projeto
COPY . .

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala as dependências do projeto
RUN composer install --no-dev --optimize-autoloader

# Ajusta permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copia a configuração do Apache
COPY apache-laravel.conf /etc/apache2/sites-available/000-default.conf

# Comando padrão do container
CMD ["apache2-foreground"]
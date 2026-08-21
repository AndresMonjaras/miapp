FROM php:8.5-apache

# Instalar extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql zip gd mbstring

# Habilitar módulos de Apache
RUN a2enmod rewrite headers expires

# Copiar configuración de seguridad
COPY security.conf /etc/apache2/conf-available/security-custom.conf
RUN a2enconf security-custom

# Habilitar el virtual host (lo haremos luego con un script de inicio)
# Pero lo habilitaremos manualmente después de construir

# Exponer puertos
EXPOSE 80 443

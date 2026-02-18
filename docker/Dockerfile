# ----------------------------------------
# Base image PHP + Apache
# ----------------------------------------
FROM php:8.2-apache

# ----------------------------------------
# Installer dépendances système et PHP
# ----------------------------------------
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    python3 \
    python3-pip \
    && docker-php-ext-install intl zip \
    && rm -rf /var/lib/apt/lists/*

# ----------------------------------------
# Installer extension MongoDB pour PHP
# ----------------------------------------
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# ----------------------------------------
# Installer packages Python
# ----------------------------------------
RUN pip3 install --break-system-packages --no-cache-dir pandas scikit-learn pymongo

# ----------------------------------------
# Installer Composer
# ----------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ----------------------------------------
# Copier le projet Symfony dans le conteneur
# ----------------------------------------
WORKDIR /var/www/html
COPY . /var/www/html

# ----------------------------------------
# Créer public et uploads avec permissions www-data
# ----------------------------------------
RUN mkdir -p /var/www/html/public/uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/public/uploads

# ----------------------------------------
# Optionnel : créer .htaccess si absent
# ----------------------------------------
RUN [ ! -f /var/www/html/public/.htaccess ] && \
    echo -e "RewriteEngine On\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteRule ^(.*)$ index.php [QSA,L]" > /var/www/html/public/.htaccess || true

# ----------------------------------------
# Configurer Apache pour Symfony
# ----------------------------------------
RUN a2enmod rewrite \
    && echo "<VirtualHost *:80>" > /etc/apache2/sites-available/000-default.conf \
    && echo "  DocumentRoot /var/www/html/public" >> /etc/apache2/sites-available/000-default.conf \
    && echo "  <Directory /var/www/html/public>" >> /etc/apache2/sites-available/000-default.conf \
    && echo "    AllowOverride All" >> /etc/apache2/sites-available/000-default.conf \
    && echo "    Require all granted" >> /etc/apache2/sites-available/000-default.conf \
    && echo "  </Directory>" >> /etc/apache2/sites-available/000-default.conf \
    && echo "</VirtualHost>" >> /etc/apache2/sites-available/000-default.conf

# ----------------------------------------
# Exposition du port Apache
# ----------------------------------------
EXPOSE 8002

# ----------------------------------------
# Démarrage d’Apache
# ----------------------------------------
CMD ["apache2-foreground"]

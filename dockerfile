FROM php:8.2-apache

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli zip

# Install curl extension (critical for Supabase API calls)
RUN docker-php-ext-install curl

# Download CA certificate bundle for SSL (fixes your earlier SSL error)
RUN curl -o /usr/local/etc/php/cacert.pem https://curl.se/ca/cacert.pem

# Configure PHP
RUN echo "curl.cainfo=/usr/local/etc/php/cacert.pem" >> /usr/local/etc/php/php.ini

# Copy application files
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configure Apache to allow .htaccess
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/docker-php.conf \
    && a2enconf docker-php

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
```

## 2. Create `.dockerignore` in `/supablog` root:
```
.git
.gitignore
.env
README.md
*.md
.DS_Store
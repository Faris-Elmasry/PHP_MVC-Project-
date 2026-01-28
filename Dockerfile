FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (with full MySQL 8 caching_sha2_password support)
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first
COPY composer.json composer.lock* ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts || composer install --no-dev --no-scripts

# Copy application files
COPY . /var/www/html

# Regenerate autoloader with all files
RUN composer dump-autoload --optimize

# Copy nginx configuration
RUN echo 'server { \n\
    listen 80; \n\
    server_name _; \n\
    root /var/www/html/public; \n\
    index index.php index.html; \n\
    \n\
    location / { \n\
    try_files $uri $uri/ /index.php?$query_string; \n\
    } \n\
    \n\
    location ~ \.php$ { \n\
    fastcgi_pass 127.0.0.1:9000; \n\
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \n\
    include fastcgi_params; \n\
    } \n\
    }' > /etc/nginx/sites-available/default

# Copy supervisor configuration
RUN echo '[supervisord] \n\
    nodaemon=true \n\
    \n\
    [program:php-fpm] \n\
    command=php-fpm -F \n\
    autostart=true \n\
    autorestart=true \n\
    \n\
    [program:nginx] \n\
    command=nginx -g "daemon off;" \n\
    autostart=true \n\
    autorestart=true' > /etc/supervisor/conf.d/supervisord.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

FROM php:8.2-fpm

# Install system dependencies including PostgreSQL development libraries
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (pdo_pgsql for PostgreSQL)
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy codebase
COPY . /var/www

# Install PHP and Node dependencies
RUN composer install --optimize-autoloader --no-dev
RUN rm -f package-lock.json && npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 777 /var/www/storage /var/www/bootstrap/cache

# Copy Nginx and Supervisor configurations
COPY .render/nginx.conf /etc/nginx/sites-available/default
COPY .render/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose Nginx port
EXPOSE 80

# Start: Run Laravel bootstrap tasks then hand off to Supervisor
CMD php /var/www/artisan config:cache && \
    php /var/www/artisan route:cache && \
    php /var/www/artisan view:cache && \
    php /var/www/artisan migrate --force && \
    /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

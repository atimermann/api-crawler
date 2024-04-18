FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

# Create system user to run Composer and Artisan Commands
#RUN useradd -G www-data,root -u $uid -d /home/$user $user
#RUN mkdir -p /home/$user/.composer && \
#    chown -R $user:$user /home/$user

# Install PHP tools
RUN pecl install xdebug redis \
    && docker-php-ext-enable redis

# Set working directory
WORKDIR /var/www/html

# Add application
COPY . /var/www/html

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev
#RUN php artisan l5-swagger:generate


# Adjust the permissions
RUN chown -R www-data:www-data /var/www

# Copy custom PHP configurations
COPY ./docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

COPY ./docker/prod/apache.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/prod/entrypoint.sh /var/www/html
RUN chmod +x /var/www/html/entrypoint.sh

ENTRYPOINT ["/var/www/html/entrypoint.sh"]

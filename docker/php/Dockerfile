# Use an official PHP FPM runtime (Debian-based)
FROM php:8.2-fpm

# -------------------------------
# 1️⃣ Add ec2-user with UID 1000 (new)
# -------------------------------
RUN useradd -u 1000 -m ec2-user

# Install FFmpeg and FFprobe for video processing
RUN apt-get update \
    && apt-get install -y ffmpeg \
    && apt-get clean all
    
# Install PostgreSQL client and its PHP extensions
RUN apt-get update \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo_pgsql pdo

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Imagick
RUN apt-get update \
    && apt-get install --assume-yes --no-install-recommends --quiet \
       build-essential \
       libmagickwand-dev \
    && apt-get clean all

# Install the Imagick PHP extension
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Install ping and nslookup (DNS tools)
RUN apt-get update && apt-get install -y \
    dnsutils iputils-ping \
    && apt-get clean all

# Install GD and Zip with WebP support
RUN apt-get update \
    && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev libzip-dev zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd zip \
    && apt-get clean all

# Install Redis extension (Debian way)
RUN pecl install redis \
    && docker-php-ext-enable redis

# Match application upload validation limits.
RUN { \
        echo "upload_max_filesize=10M"; \
        echo "post_max_size=12M"; \
    } > /usr/local/etc/php/conf.d/uploads.ini

# Install Supervisor
RUN apt-get update && apt-get install -y supervisor

# Copy Supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Ensure supervisor log directory exists and is writable
RUN mkdir -p /var/log/supervisor \
    && chown -R 1000:1000 /var/log/supervisor

# Copy entrypoint script
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# -------------------------------
# Set working directory
# -------------------------------
WORKDIR /var/www/html/buddyscript

# -------------------------------
# Run PHP-FPM as ec2-user (new)
# -------------------------------
USER 1000:1000

ENTRYPOINT ["/entrypoint.sh"]

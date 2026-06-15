FROM php:8.2-fpm

RUN apt-get update \
    && apt-get install -y cron \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo_pgsql pdo

RUN apt-get update \
     && apt-get install --assume-yes --no-install-recommends --quiet \
         build-essential \
         libmagickwand-dev \
         logrotate \
     && apt-get clean all
   
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Add ec2-user with UID and GID 1000 (matches host)
RUN useradd -u 1000 -m ec2-user

# Copy cron and logrotate files
COPY ./docker/backend/crontab /etc/crontab
COPY ./docker/backend/laravel-queue-logrotate /etc/logrotate.d/laravel-queue
COPY ./docker/backend/logrotate-cron.sh /docker/backend/logrotate-cron.sh

RUN chmod 0644 /etc/crontab \
    && chmod +x /docker/backend/logrotate-cron.sh

# Set working directory
WORKDIR /var/www/html/buddyscript

# Run container as root (cron needs root to write /var/run)
USER root

# Run cron in foreground
CMD ["cron", "-f"]

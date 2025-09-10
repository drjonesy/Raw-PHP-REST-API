# Use the image from your docker-compose file as the base
FROM ghcr.io/ndigitals/openlitespeed:latest

# Update package lists, install dependencies, and install Composer
RUN apt-get update && \
    apt-get install -y php-cli unzip curl && \
    curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php && \
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/*
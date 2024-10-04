FROM php:8.0-apache

# Install SQLite3
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set the working directory in the container
WORKDIR /var/www/html

# Copy the application files to the container
COPY . /var/www/html/

# Create the data directory and database file
RUN mkdir -p /var/www/data \
    && touch /var/www/data/words.db \
    && chown -R www-data:www-data /var/www/html /var/www/data \
    && chmod -R 755 /var/www/html /var/www/data \
    && chmod 664 /var/www/data/words.db

# Apache configuration to allow .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
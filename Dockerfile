FROM php:7.4-apache

# Instal ekstensi mysqli untuk koneksi ke database
RUN docker-php-ext-install mysqli

# Buat folder uploads dan berikan izin akses penuh untuk skenario Unrestricted File Upload
RUN mkdir -p /var/www/html/uploads && \
    chmod -R 777 /var/www/html/uploads

# Aktifkan modul rewrite Apache
RUN a2enmod rewrite
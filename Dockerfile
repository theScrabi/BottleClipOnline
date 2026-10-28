FROM php:8.4-fpm

# Update and install necessary packages
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
		nginx \
		openscad && \
    rm -rf /var/lib/apt/lists/*

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    sed -i 's/^expose_php = On/expose_php = Off/' "$PHP_INI_DIR/php.ini"

COPY ./config/nginx.conf /etc/nginx/sites-enabled/default

# Copy your OpenSCAD files into the container (adjust the path accordingly)
# Owned by root so php-fpm (www-data) can read but not modify the app
COPY ./bottleclip_web /app/

# Expose port 80 for Apache
EXPOSE 80

# Start Apache in the foreground
ENTRYPOINT ["/app/docker-entrypoint.sh"]

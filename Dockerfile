FROM php:8.2.8-fpm

# Update and install necessary packages
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
		nginx \
		openscad

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY ./config/nginx.conf /etc/nginx/sites-enabled/default

# Copy your OpenSCAD files into the container (adjust the path accordingly)
COPY --chown=www-data:www-data ./bottleclip_web /app/

# Expose port 80 for Apache
EXPOSE 80

# Start Apache in the foreground
ENTRYPOINT ["/app/docker-entrypoint.sh"]

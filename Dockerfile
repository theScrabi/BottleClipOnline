# Use the official Ubuntu base image
FROM ubuntu:23.10

# Update and install necessary packages
RUN apt-get update && \
    apt-get install -y apache2 php libapache2-mod-php openscad

# Enable Apache modules
RUN a2enmod php8.2

# Copy your OpenSCAD files into the container (adjust the path accordingly)
COPY bottleclip_web /var/www/html/

# Expose port 80 for Apache
EXPOSE 8080

# Start Apache in the foreground
CMD ["apache2ctl", "-D", "FOREGROUND"]

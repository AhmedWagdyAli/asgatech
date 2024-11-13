#!/bin/bash

# Ensure the application has the correct permissions
chown -R www-data:www-data /var/www
chmod -R 755 /var/www

# Run any command passed to the script
exec "$@"

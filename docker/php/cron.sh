#!/bin/sh

# Run scheduler
while [ true ]
do
  php /var/www/artisan schedule:run --verbose --no-interaction &
  sleep 60
done
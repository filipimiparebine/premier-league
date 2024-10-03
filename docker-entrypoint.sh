#!/bin/bash

until nc -z -v -w30 $DB_HOST $DB_PORT
do
echo "Waiting for database connection at $DB_HOST:$DB_PORT..."
sleep 5
done

if [ ! -f /var/www/.seed ]; then
  echo "Running migrations and seeding..."
  php artisan migrate --seed
  touch /var/www/.seed
else
  echo "Database has already been seeded."
  php artisan migrate
fi

# Start the PHP-FPM server
exec "$@"

#!/bin/bash

# Check if migrations have already been run
if [ ! -f /var/www/.seed ]; then
  # Wait for MySQL to be available
  until nc -z -v -w30 $DB_HOST $DB_PORT
  do
    echo "Waiting for database connection at $DB_HOST:$DB_PORT..."
    sleep 5
  done

  # Run migrations and seed the database
  echo "Running migrations and seeding..."
  php artisan migrate --seed

  # Mark that migrations have been run
  touch /var/www/.seed
else
  echo "Database has already been seeded."
  php artisan migrate
fi

# Start the PHP-FPM server
exec "$@"

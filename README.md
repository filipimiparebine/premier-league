# Premier League Simulation

This project is a Premier League football simulation application built with Laravel 10 (backend) and Next.js (frontend).

![Screenshot 2024-10-04 at 16 53 01](https://github.com/user-attachments/assets/a9cbe8ee-061e-43a8-8388-28750337840b)

## Features

- Team selection
- League table generation
- Week fixtures (week matches to be played)
- Match simulation
- Match prediction
- Unit and feature testing with PHPUnit
- Dockerized
- Github CI Pipelines

## Setup

1. Clone the repository:

```bash
git clone https://github.com/filipimiparebine/premier-league-be.git
cd premier-league-be
```

1. Run project:

```bash
./run.sh
```

3. Access the application at `http://localhost:3000`

## Running Tests

To run the backend tests:

```bash
docker-compose exec premier-league-be vendor/bin/phpunit
```

## Seeding the database

Seeding the database is executed once on docker compose up, but you can also use:

```bash
docker compose exec premier-league-be php artisan db:seed --class=DatabaseSeeder
```

## CI/CD

This project uses GitHub Actions for continuous integration. The workflow is defined in `.github/workflows/ci.yml`.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

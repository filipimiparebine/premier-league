# Premier League Simulation

This project is a Premier League football simulation application built with Laravel 10 (backend) and Next.js (frontend).

![premier-league-simulation](https://github.com/user-attachments/assets/da95db5d-382a-426c-a7b7-922d2183460d)

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

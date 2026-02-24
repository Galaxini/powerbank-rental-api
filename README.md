# Powerbank Rental API

Backend API for power bank rental flow built with Laravel 12, PHP 8.3, PostgreSQL, Redis and Docker.

## Features

- Auth with Laravel Sanctum personal access tokens
- Start rental with business validations and transactional updates
- Return rental with overdue calculation and penalty (`penalty_cents`)
- Domain enums for rental and power bank statuses
- Conflict handling with JSON `409 CONFLICT` responses for business conflicts

## Tech Stack

- PHP 8.3
- Laravel 12
- PostgreSQL 15
- Redis 7
- Docker Compose

## Quick Start (Docker)

1. Copy env:

```bash
cp .env.example .env
```

2. Build and start services:

```bash
docker compose up -d --build
```

3. Install dependencies:

```bash
docker compose exec app composer install
```

4. Run migrations:

```bash
docker compose exec app php artisan migrate
```

5. (Optional) Start Laravel dev server inside container:

```bash
docker compose exec app php artisan serve --host=0.0.0.0 --port=8000
```

API base URL:

```text
http://localhost:8000/api
```

## API Endpoints

### Auth

- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `POST /api/v1/auth/logout` (Bearer token)

### Rentals

- `POST /api/v1/rentals/start` (Bearer token)
- `POST /api/v1/rentals/{rental}/return` (Bearer token)

## cURL Examples

Register:

```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password123"}'
```

Login:

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password123"}'
```

Start rental:

```bash
TOKEN="<TOKEN>"
curl -X POST http://localhost:8000/api/v1/rentals/start \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"pickup_point_id":1,"power_bank_id":1}'
```

Return rental:

```bash
TOKEN="<TOKEN>"
RENTAL_ID=1
curl -X POST http://localhost:8000/api/v1/rentals/${RENTAL_ID}/return \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

## Architecture

The project follows a pragmatic layered + DDD-oriented structure:

- `app/Http/*`: transport layer (controllers, requests)
- `app/DTO/*`: input contracts between controller and service
- `app/Services/*`: application services (use-cases orchestration)
- `app/Domain/*`: domain objects and business concepts
- `app/Domain/PowerBank/Enums/*`: power bank domain statuses
- `app/Domain/Rental/Enums/*`: rental domain statuses
- `app/Domain/Rental/Services/*`: domain services (`ReturnRentalService`)
- `app/Domain/Rental/Exceptions/*`: domain exceptions (`ConflictException`)
- `app/Models/*`: persistence (Eloquent models)

### Key business rules

- A power bank can be rented only when `AVAILABLE`
- A user cannot start a new rental if they already have `ACTIVE` or `OVERDUE`
- Rental start and return operations run inside DB transactions
- Return flow calculates overdue and saves `penalty_cents`
- Business conflicts are returned as HTTP `409` with structured JSON

## Notes for Recruiter Review

- `.env` is not committed (`.gitignore`)
- Use `.env.example` as the configuration template
- Run migrations before testing endpoints
- No frontend is included; this repository is API-only

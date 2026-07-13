# Laravel Order Management API

A modern REST API built with **Laravel 12** following clean architecture principles, SOLID design, and production-ready development practices.

This project demonstrates backend development using Laravel, JWT/Sanctum authentication, OpenAPI documentation, structured logging, and a scalable service-oriented architecture.

---

## Features

* RESTful API
* Laravel 12
* Laravel Sanctum Authentication
* Form Request Validation
* OpenAPI (Swagger) Documentation
* Service Layer Architecture
* Repository-friendly project structure
* Custom Logging System
* Telegram Error Notifications
* File Error Logging
* Role-Based Access Control
* Paginated API Responses
* Consistent API Response Structure
* DTO-based Logging
* Reusable OpenAPI Schemas and Components

---

## Technologies

* PHP 8.2+
* Laravel 12
* MySQL
* Laravel Sanctum
* OpenAPI / Swagger
* Telegram Bot API

---

## Authentication

Authentication is implemented using **Laravel Sanctum**.

Protected endpoints require a valid Bearer Token.

```
Authorization: Bearer YOUR_ACCESS_TOKEN
```

---

## API Documentation

Complete API documentation is available through Swagger/OpenAPI.

Documentation includes:

* Endpoints
* Request bodies
* Validation rules
* Response examples
* Error responses
* Authentication requirements

---

## Logging

The project contains a custom logging system built around a common logger interface.

Current logging channels:

* File Logger
* Telegram Logger

Each logger implements the same interface, making it easy to extend the system with additional channels (Slack, Discord, Sentry, etc.).

Logged information includes:

* Date & Time
* Authenticated User
* Request URL
* HTTP Method
* Request Body
* Entity
* Action
* Exception Details

---

## Error Notifications

Critical application exceptions are automatically sent to a Telegram group using a Telegram Bot.

This allows instant monitoring of production issues without accessing server logs.

---

## Architecture

The project follows several software engineering principles:

* SOLID
* Single Responsibility Principle
* Dependency Injection
* Service Layer
* Interface-based Design
* Reusable OpenAPI Components
* Clean Separation of Concerns

---

## API Response Example

```json
{
    "success": true,
    "data": {
        "orders": [],
        "paginate": {}
    }
}
```

Error response example:

```json
{
    "success": false,
    "message": "Access denied.",
    "data": []
}
```

Validation response example:

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": [
            "The name field is required."
        ]
    }
}
```

---

## Installation

```bash
git clone https://github.com/your-repository.git

cd project

composer install

cp .env.example .env

php artisan key:generate

php artisan migrate --seed

php artisan serve
```

---

## Environment Variables

Example:

```
APP_NAME=

APP_URL=

DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=

TELEGRAM_BOT_TOKEN=
TELEGRAM_CHAT_ID=
```

---

## Future Improvements

* Unit Tests
* Feature Tests
* API Versioning
* Docker Support
* Queue-based Notifications
* Redis Cache
* Rate Limiting Improvements

---

## License

This project is intended for educational purposes and portfolio demonstration.

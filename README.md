# Laravel 10 Project

Welcome to our Laravel 10 project! This repository contains the source code for our web application built using Laravel version 10. Please follow the instructions below to get started.

## Requirements

Before you proceed with the setup, ensure your system meets the following requirements:

- PHP >= 8.1
- Composer
- MySQL
- Git (optional but recommended)

## Setup

Follow these steps to set up the project:


```bash
   git clone <repository_url>
   composer install
   cp .env.example .env
   # setup config for MySQL
   php artisan key:generate
   php artisan migrate --seed
```

You should now be able to access the application at http://localhost:8000.

Contributing
If you would like to contribute to this project, please follow the CONTRIBUTING.md guidelines.

License
This project is licensed under the MIT License.

## Swagger API

http://localhost:8000/swagger-ui

Basic Authen:
- Username: admin
- Password: admin
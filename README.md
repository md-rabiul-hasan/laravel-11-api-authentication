# Asset Management Application API

This is the backend API for the Asset Management Application, built using Laravel. The API provides functionality for managing assets, tracking their usage, and maintaining records of asset assignments and conditions.

---

## Table of Contents

1. [Installation](#installation)
2. [API Endpoints](#api-endpoints)

---

## Installation

```bash
git clone https://github.com/your-username/asset-management-api.git
cd asset-management-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
bun dev
```


## API Endpoints

**Base URL**: `http://localhost:82/assetflow-backend-api/api/v1`

### Authentication

- **`POST /auth/login`**  
  Authenticate a user and obtain an API token.

- **`GET /auth/me`**  
  Retrieve authenticated user's details.

- **`POST /auth/logout`**  
  Log out the authenticated user and invalidate the token.

- **`POST /auth/refresh-token`**  
  Refresh the API token for continued authentication.

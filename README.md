# Asset Management Application API

This is the backend API for the Asset Management Application, built using Laravel. The API provides functionality for managing assets, tracking their usage, and maintaining records of asset assignments and conditions.

---

## Table of Contents

1. [Installation](#installation)
2. [API Endpoints](#api-endpoints)

---

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-username/asset-management-api.git
   cd asset-management-api
2. **Install dependencies:**:
   ```bash
   composer install
3. **Set up environment variables**:
   ```bash
   cp .env.example .env
4. **Generate application key**:
   ```bash
   php artisan key:generate
5. **Run database migrations**:
   ```bash
   php artisan migrate
6. **Seed the database (optional)**:
   ```bash
   php artisan db:seed
7. **Serve the application**:
   ```bash
   php artisan serve

## API Endpoints

**Base URL**: `http://localhost:8000/api/v1`

## API Documentation URL

**Base URL**: `http://localhost:8000/api/documentation`

### Authentication

- **`POST /auth/login`**  
  Authenticate a user and obtain an API token.

- **`GET /auth/me`**  
  Retrieve authenticated user's details.

- **`POST /auth/logout`**  
  Log out the authenticated user and invalidate the token.

- **`POST /auth/refresh-token`**  
  Refresh the API token for continued authentication.

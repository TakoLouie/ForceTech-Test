# Short URL Generator

A PHP and MySQL app that turns long URLs into short links and redirects visitors to the original URL.

## Requirements

- PHP 8+ with `pdo_mysql` enabled
- MySQL

## Setup

1. Create the database and table:

   ```powershell
   Get-Content -Raw "C:\ForceTech Test\database\schema.sql" | mysql -u root -p
   ```

2. Create a `.env` file in the project root, then set your MySQL password:

   ```env
   DB_PASSWORD=your_mysql_password
   ```

3. Start the app:

   ```powershell
   cd "C:\ForceTech Test"
   php -S localhost:8000 -t public public/router.php
   ```

4. Open `http://localhost:8000` and shorten a valid `http` or `https` URL.

## Routes

- `POST /api/shorten` creates a short code.
- `GET /{code}` redirects to the stored URL.

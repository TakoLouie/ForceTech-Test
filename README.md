# Short URL Generator

A small PHP and MySQL URL shortener. Submit a valid URL, receive a unique short link, and visiting that link redirects to the original destination.

## Requirements

- PHP 8.0+ with the PDO MySQL extension
- MySQL 8.0+ (or compatible MariaDB)
- Apache with `mod_rewrite` enabled for production URLs, or the PHP development server for local use

## Setup

1. Create a MySQL database and table:

   ```sh
   mysql -u root -p < database/schema.sql
   ```

2. Copy `.env.example` to `.env` and set the database credentials. Set `APP_URL` to the public base URL of the app (for example, `http://localhost:8000`).

3. Serve the `public` directory as the web root. For a local development server, run:

   ```sh
   php -S localhost:8000 -t public public/router.php
   ```

4. Open `http://localhost:8000`, enter a URL, and select **Shorten**.

## Routes

- `POST /api/shorten` accepts JSON such as `{ "url": "https://example.com" }` and returns `{ "shortUrl": "...", "code": "..." }`.
- `GET /{code}` looks up the code and sends an HTTP 302 redirect to its stored URL.

## Notes

The API only accepts absolute `http` and `https` URLs. Codes are eight characters from uppercase/lowercase letters and digits. A database unique constraint guarantees that a collision is retried safely.

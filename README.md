# Short URL Generator

A small PHP and MySQL URL shortener. Submit a valid URL, receive a unique short link, and visiting that link redirects to the original destination.

## Requirements

- PHP 8.0+ with the `pdo_mysql` extension enabled
- MySQL 8.0+ (or compatible MariaDB)
- Apache with `mod_rewrite` for production, or PHP's built-in development server

## Windows setup

### 1. Install PHP and enable MySQL support

Install a complete PHP for Windows distribution. Its `ext` directory must contain `php_pdo_mysql.dll`.

Find the PHP folder in PowerShell:

```powershell
$phpDir = Split-Path (Get-Command php).Source
```

Create a PHP configuration file if one does not exist:

```powershell
Copy-Item "$phpDir\php.ini-development" "$phpDir\php.ini"
notepad "$phpDir\php.ini"
```

In `php.ini`, enable this extension (with no leading semicolon and no backslash):

```ini
extension=pdo_mysql
```

Open a new PowerShell window and check that it loaded:

```powershell
php -m | findstr /I pdo_mysql
```

The command must print `pdo_mysql` before the application can connect to MySQL.

### 2. Install and initialize MySQL

Install MySQL Server, set a password for the `root` account, then create the project database and table:

```powershell
Get-Content -Raw "C:\ForceTech Test\database\schema.sql" | mysql -u root -p
```

PowerShell does not support the `<` input-redirection syntax commonly used in Command Prompt. If `mysql` is not in your PATH, use the installed executable directly, for example:

```powershell
Get-Content -Raw "C:\ForceTech Test\database\schema.sql" | & "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -p
```

### 3. Configure the app

Copy `.env.example` to a new file called `.env` in the project root. It must not be named `.env.txt`.

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=short_url
DB_USER=root
DB_PASSWORD=YOUR_MYSQL_ROOT_PASSWORD
APP_URL=http://localhost:8000
```

Replace `YOUR_MYSQL_ROOT_PASSWORD` with the password chosen during MySQL setup. Do not use quotes around the password.

### 4. Run the app

From the project folder, start the PHP development server:

```powershell
cd "C:\ForceTech Test"
php -S localhost:8000 -t public public/router.php
```

Open `http://localhost:8000`, enter a URL, and select **Shorten**. Stop the server with `Ctrl + C`; run the command again to restart it after changing `.env` or `php.ini`.

## Troubleshooting

- `could not find driver`: Enable `extension=pdo_mysql` in the `php.ini` loaded by `php --ini`, then restart PHP.
- `Unable to load dynamic library 'pdo_mysql'`: Verify that `php_pdo_mysql.dll` exists in PHP's `ext` folder. Install a complete PHP distribution if it does not.
- `Access denied ... using password: NO`: Create/update the `.env` file and set `DB_PASSWORD` to the MySQL `root` password, then restart the PHP server.
- `mysql is not recognized`: Install MySQL Server or call `mysql.exe` using its full path shown above.

## Routes

- `POST /api/shorten` accepts JSON such as `{ "url": "https://example.com" }` and returns `{ "shortUrl": "...", "code": "..." }`.
- `GET /{code}` looks up the code and sends an HTTP 302 redirect to its stored URL.

## Notes

The API only accepts absolute `http` and `https` URLs. Codes are eight characters from uppercase/lowercase letters and digits. A database unique constraint guarantees that a collision is retried safely.

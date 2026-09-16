<?php
declare(strict_types=1);

/** Load simple KEY=VALUE settings from .env without requiring a dependency. */
function env(string $key, ?string $default = null): ?string
{
    static $settings = null;

    if ($settings === null) {
        $settings = [];
        $envPath = dirname(__DIR__) . '/.env';
        if (is_file($envPath)) {
            foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }
                [$name, $value] = explode('=', $line, 2);
                $settings[trim($name)] = trim($value, " \t\n\r\0\x0B\"'");
            }
        }
    }

    $systemValue = getenv($key);
    return $_ENV[$key] ?? ($systemValue !== false ? $systemValue : ($settings[$key] ?? $default));
}

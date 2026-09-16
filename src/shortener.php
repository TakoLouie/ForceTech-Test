<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

const CODE_LENGTH = 8;
const CODE_ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

function isValidUrl(string $url): bool
{
    if (strlen($url) > 2048 || filter_var($url, FILTER_VALIDATE_URL) === false) {
        return false;
    }

    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
    return in_array($scheme, ['http', 'https'], true);
}

function generateCode(int $length = CODE_LENGTH): string
{
    $code = '';
    $maxIndex = strlen(CODE_ALPHABET) - 1;
    for ($index = 0; $index < $length; $index++) {
        $code .= CODE_ALPHABET[random_int(0, $maxIndex)];
    }
    return $code;
}

function createShortCode(string $url): string
{
    $statement = database()->prepare(
        'INSERT INTO urls (short_code, original_url) VALUES (:code, :url)'
    );

    // The unique index makes collisions harmless, even under concurrent requests.
    for ($attempt = 0; $attempt < 10; $attempt++) {
        $code = generateCode();
        try {
            $statement->execute(['code' => $code, 'url' => $url]);
            return $code;
        } catch (PDOException $exception) {
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }
        }
    }

    throw new RuntimeException('Unable to create a unique short code. Please try again.');
}

function findOriginalUrl(string $code): ?string
{
    $statement = database()->prepare('SELECT original_url FROM urls WHERE short_code = :code LIMIT 1');
    $statement->execute(['code' => $code]);
    $url = $statement->fetchColumn();

    return $url === false ? null : (string) $url;
}

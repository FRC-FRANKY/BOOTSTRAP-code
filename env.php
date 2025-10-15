<?php
// Lightweight .env loader. Reads KEY=VALUE pairs from a local .env file
// in this directory and exposes them via getenv()/$_ENV without requiring
// shell environment setup. Existing env vars are not overridden.

if (!function_exists('load_env_file')) {
    function load_env_file(string $path): void {
        if (!is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || $trimmed[0] === '#' || $trimmed[0] === ';') {
                continue;
            }

            $pos = strpos($trimmed, '=');
            if ($pos === false) {
                continue;
            }

            $key = trim(substr($trimmed, 0, $pos));
            $value = trim(substr($trimmed, $pos + 1));

            // Remove optional surrounding single or double quotes (PHP 7 compatible)
            $len = strlen($value);
            if ($len >= 2) {
                $first = $value[0];
                $last = $value[$len - 1];
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, $len - 2);
                }
            }

            if ($key === '') {
                continue;
            }

            // Populate env for getenv(), $_ENV, and putenv()
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            // putenv fails silently on some shared hosts; ignore return value
            @putenv($key . '=' . $value);
        }
    }
}

// Load project .env located next to this file. Support `.env.txt` as a fallback (Notepad)
$envPath = __DIR__ . DIRECTORY_SEPARATOR . '.env';
if (is_readable($envPath)) {
    load_env_file($envPath);
} else {
    $envTxtPath = __DIR__ . DIRECTORY_SEPARATOR . '.env.txt';
    load_env_file($envTxtPath);
}

?>


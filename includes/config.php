<?php
// includes/config.php
// Centralized config. Prefer environment variables for secrets.
// For OpenRouter access, set the following environment variables:
// - OPENROUTER_API_KEY (general default key for most models)
// - OPENROUTER_GEMINI_API_KEY (optional, used when requesting Gemini models)

// Read keys from environment. Do NOT hardcode secrets in the repo for production.
// Primary default key (set this in your environment for production)
// If a local .env exists, this simple loader will try to read it when getenv returns null.
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
	$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach ($lines as $line) {
		if (strpos(trim($line), '#') === 0) continue;
		if (strpos($line, '=') !== false) {
			[$k, $v] = explode('=', $line, 2);
			$k = trim($k);
			$v = trim($v);
			if (getenv($k) === false || getenv($k) === null || getenv($k) === '') {
				putenv(sprintf('%s=%s', $k, $v));
				$_ENV[$k] = $v;
			}
		}
	}
}

define('OPENROUTER_API_KEY', getenv('OPENROUTER_API_KEY') ?: null);
// Accept either OPENROUTER_GEMINI_API_KEY or GEMINI_API_KEY for backward compatibility
// Fallback to provided .env value only for local testing; remove in production.
define('OPENROUTER_GEMINI_API_KEY', getenv('OPENROUTER_GEMINI_API_KEY') ?: getenv('GEMINI_API_KEY') ?: null);

// Additional config values may live here.

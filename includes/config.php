<?php
// includes/config.php
// Centralized config. Prefer environment variables for secrets.
// For OpenRouter access, set the following environment variables:
// - OPENROUTER_API_KEY (general default key for most models)
// - OPENROUTER_GEMINI_API_KEY (optional, used when requesting Gemini models)

// Read keys from environment. Do NOT hardcode secrets in the repo for production.
// Primary default key (set this in your environment for production)
define('OPENROUTER_API_KEY', getenv('OPENROUTER_API_KEY') ?: null);
// Accept either OPENROUTER_GEMINI_API_KEY or GEMINI_API_KEY for backward compatibility
// Fallback to the provided Gemini key for local testing; replace/remove for production.
define('OPENROUTER_GEMINI_API_KEY', getenv('OPENROUTER_GEMINI_API_KEY') ?: getenv('GEMINI_API_KEY') ?: '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');

// Additional config values may live here.

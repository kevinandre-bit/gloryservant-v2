Upgrade Plan: Laravel 8 -> 10/11 (PHP 8.1/8.2)

Overview
- Target PHP: 8.1 or 8.2 (recommended)
- Target Laravel: 10 (LTS) or 11 (current)
- Strategy: Upgrade PHP first, then dependencies, then app code fixes

1) Prerequisites
- Upgrade the runtime to PHP 8.1 or 8.2 in all environments.
- Ensure extensions enabled: mbstring, openssl, pdo, tokenizer, xml, ctype, json, curl, fileinfo, bcmath.
- Clear caches before upgrading: `php artisan cache:clear && php artisan config:clear && php artisan route:clear`.

2) Composer constraints (proposed)
- In composer.json (require):
  - "php": "^8.1"
  - "laravel/framework": "^10.0" (or "^11.0" if choosing 11)
  - "laravel/sanctum": "^3.2"
  - "fruitcake/laravel-cors": "^3.0"
  - Remove: "fideloper/proxy" (integrated into framework)
  - Consider upgrading: "phpoffice/phpspreadsheet": "^1.30" (addresses advisories)
- In composer.json (require-dev):
  - Replace "facade/ignition" with "spatie/laravel-ignition": "^2.0"
  - "phpunit/phpunit": "^10.5" (Laravel 10) or "^11" (Laravel 11)
  - "nunomaduro/collision": "^7" (Laravel 10) or "^8" (Laravel 11)

3) Upgrade commands (Laravel 10 path)
```bash
php -v # confirm 8.1/8.2
composer remove fideloper/proxy facade/ignition -W
composer require laravel/framework:^10.0 laravel/sanctum:^3.2 fruitcake/laravel-cors:^3.0 spatie/laravel-ignition:^2.0 phpoffice/phpspreadsheet:^1.30 -W
composer require --dev phpunit/phpunit:^10.5 nunomaduro/collision:^7 -W
```

4) Code updates
- Ignition: No code changes; new package handles error pages in dev.
- Proxies: `App/Http/Middleware/TrustProxies.php` remains; package removed.
- CORS: Keep config/cors.php; package updated.
- Tests: Migrate to PHPUnit 10 config if needed (convert phpunit.xml schema, remove deprecated annotations).
- Livewire (if used): consider upgrading to Livewire 3 (check release notes).
- Sanctum: Update config if using SPA stateful domains; review changelog.

5) Post-upgrade
- Clear caches, regenerate optimized autoloaders:
  - `php artisan optimize:clear && composer dump-autoload -o`
- Run the test suite and smoke test routes:
  - `php artisan test`
- Review deprecations in logs; address any runtime warnings.

6) Laravel 11 notes
- Prefer `laravel/framework:^11.0`, `spatie/laravel-ignition:^2.5`, `nunomaduro/collision:^8`.
- Follow the official 10->11 upgrade guide for minor framework changes.

Rollback plan
- Keep a branch with the current composer.lock.
- If issues arise, revert to the lockfile and restore PHP 8.0 runtime temporarily.

References
- Laravel upgrade guides: https://laravel.com/docs/10.x/upgrade and 11.x/upgrade
- Sanctum, Fruitcake CORS, Ignition package release notes

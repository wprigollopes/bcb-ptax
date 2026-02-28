# BCB PTAX Library — Full Overhaul Design

**Date:** 2026-02-28
**Target:** PHP 8.1+, Guzzle HTTP, PHPUnit 10+, PSR-4

## Goals

- Fix existing bugs (duplicate `checkDate`, unused import, misleading default format)
- Modernize to PHP 8.1+ with type hints, enums, readonly properties
- Add Guzzle for HTTP, enabling testability and retry
- Add PHPUnit tests with mocked HTTP
- Add i18n for exception messages (en_US default, pt_BR included)
- Extract currency list to a PHP 8.1 enum
- Return typed value objects instead of raw stdClass/false

## Project Structure

```
src/
  Currency.php              # PHP 8.1 backed enum (177 currency codes)
  PTAX.php                  # Main class, non-static, Guzzle-injected
  PTAXResult.php            # Readonly value object for API response
  Exception/
    InvalidCurrencyException.php
    InvalidDateException.php
    ApiException.php
  I18n/
    Messages.php            # Message resolver
    en_US.php               # English translations
    pt_BR.php               # Portuguese translations

tests/
  Unit/
    CurrencyTest.php
    PTAXTest.php            # Mocked HTTP
    PTAXResultTest.php
  Integration/
    BcbApiTest.php          # Real API, @group integration

composer.json               # PSR-4, PHP 8.1+, Guzzle, PHPUnit
phpunit.xml                 # Test configuration
```

## Namespace

`BcbPtax\` — PascalCase, PSR-4 compliant.

## API Design

```php
use BcbPtax\PTAX;
use BcbPtax\Currency;

// Simple usage (default Guzzle client, en_US locale)
$ptax = new PTAX();
$result = $ptax->get(Currency::USD, new \DateTime('2024-01-15'));

$result->buyRate;      // float
$result->sellRate;     // float
$result->date;         // DateTimeImmutable
$result->bulletinType; // string

// Custom client and locale
$ptax = new PTAX(
    client: new GuzzleHttp\Client(['timeout' => 10]),
    locale: 'pt_BR'
);
```

## Currency Enum

PHP 8.1 string-backed enum with all 177 ISO 4217 codes currently supported.

```php
enum Currency: string {
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    // ... 174 more
}
```

Eliminates runtime validation — invalid currencies are caught at compile time.

## PTAXResult Value Object

```php
readonly class PTAXResult {
    public function __construct(
        public float $buyRate,
        public float $sellRate,
        public DateTimeImmutable $date,
        public string $bulletinType,
    ) {}
}
```

## DateTime Input

Accept `\DateTimeInterface` instead of string+format. The library formats it internally for the API (`m-d-Y`). This eliminates `checkDate()` and the confusing default format parameter.

## Exception Hierarchy

| Scenario                   | Exception                  |
|----------------------------|----------------------------|
| API returns non-200        | `ApiException`             |
| JSON decode fails          | `ApiException`             |
| No closing PTAX for date   | `ApiException`             |
| Network error              | Guzzle `RequestException`  |

`InvalidCurrencyException` exists for programmatic construction from strings (e.g., user input → `Currency::tryFrom()`), but the enum prevents most misuse.

`InvalidDateException` is thrown if a null/invalid DateTimeInterface is passed.

## I18n

- Default locale: `en_US`
- Translation files are simple PHP arrays returning `['key' => 'message']`
- `Messages::get(string $key, string $locale, array $params = []): string`
- Exceptions accept locale and use Messages internally

## HTTP Client

- Guzzle 7.x via constructor injection
- Default: `new Client()` if none provided
- Enables MockHandler in tests — no real API calls in unit tests

## Testing

- PHPUnit 10+
- Unit tests: mock Guzzle responses for all scenarios (success, error, empty, malformed JSON)
- Integration tests: `@group integration`, call real BCB API, skippable
- Currency enum test: verify all 177 codes are valid

## Composer

- `php: >=8.1`
- `guzzlehttp/guzzle: ^7.0`
- `phpunit/phpunit: ^10.0` (dev)
- PSR-4 autoload: `"BcbPtax\\": "src/"`

# BCB PTAX

A PHP library to fetch daily PTAX exchange rates from Banco Central do Brasil's public API.

## Background

This project was born out of a real operational need at a Brazilian import company. Every business day, the company needed the official PTAX closing rate to price imports, settle invoices, and comply with Brazilian customs regulations. What started as a quick utility script grew into a Composer package shared with the community.

In 2026, the library was modernized with the help of [Claude Code](https://claude.ai/claude-code) — rewritten from the ground up with PHP 8.1+ features, proper type safety, full test coverage, and i18n support while keeping the same simple API.

## Requirements

- PHP 8.1 or higher
- Composer

## Installation

```bash
composer require wprigollopes/bcb-ptax
```

## Usage

### Basic example

```php
use BcbPtax\PTAX;
use BcbPtax\Currency;

$ptax = new PTAX();
$result = $ptax->get(Currency::USD, new \DateTime('2024-01-15'));

echo $result->buyRate;      // 4.8759 (cotacaoCompra)
echo $result->sellRate;     // 4.8765 (cotacaoVenda)
echo $result->bulletinType; // "Fechamento PTAX"
echo $result->date->format('Y-m-d'); // "2024-01-15"
```

### Custom HTTP client

If you need to configure timeouts, proxies, or other HTTP options, pass your own Guzzle client:

```php
use BcbPtax\PTAX;
use BcbPtax\Currency;
use GuzzleHttp\Client;

$ptax = new PTAX(
    client: new Client(['timeout' => 10]),
);

$result = $ptax->get(Currency::EUR, new \DateTime('2024-06-20'));
```

### Portuguese error messages

Exception messages default to English (`en_US`). To get messages in Portuguese:

```php
$ptax = new PTAX(locale: 'pt_BR');

// Throws: "Nenhum PTAX de fechamento encontrado para USD em 01-01-2024"
$ptax->get(Currency::USD, new \DateTime('2024-01-01')); // Holiday, no PTAX
```

### Handling errors

```php
use BcbPtax\PTAX;
use BcbPtax\Currency;
use BcbPtax\Exception\ApiException;

$ptax = new PTAX();

try {
    $result = $ptax->get(Currency::USD, new \DateTime('2024-01-15'));
    echo "Buy rate: {$result->buyRate}";
} catch (ApiException $e) {
    // API returned an error, no data for that date, or malformed response
    echo "Error: {$e->getMessage()}";
}
```

### Working with user input

The `Currency` enum provides safe parsing from strings:

```php
use BcbPtax\PTAX;
use BcbPtax\Currency;

$input = 'USD';
$currency = Currency::tryFrom($input);

if ($currency === null) {
    echo "Unsupported currency: {$input}";
    return;
}

$ptax = new PTAX();
$result = $ptax->get($currency, new \DateTime('2024-01-15'));
```

## Supported Currencies

The BCB PTAX service provides daily closing rates for the following currencies against BRL:

| Code | Currency |
|------|----------|
| AUD  | Australian Dollar |
| CAD  | Canadian Dollar |
| CHF  | Swiss Franc |
| DKK  | Danish Krone |
| EUR  | Euro |
| GBP  | British Pound |
| JPY  | Japanese Yen |
| NOK  | Norwegian Krone |
| SEK  | Swedish Krona |
| USD  | US Dollar |

Source: [BCB PTAX Moedas endpoint](https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/Moedas?$format=json)

## API Reference

### `PTAX`

```php
new PTAX(
    ClientInterface $client = new Client(), // Custom Guzzle client (optional)
    string $locale = 'en_US',               // 'en_US' or 'pt_BR'
)
```

#### `get(Currency $currency, DateTimeInterface $date): PTAXResult`

Fetches the closing PTAX rate for the given currency and date. Throws `ApiException` if the API returns an error, the response is malformed, or no closing PTAX exists for that date (weekends, holidays).

### `PTAXResult`

| Property | Type | Description |
|----------|------|-------------|
| `buyRate` | `float` | Purchase rate (cotacaoCompra) |
| `sellRate` | `float` | Selling rate (cotacaoVenda) |
| `date` | `DateTimeImmutable` | Quotation timestamp |
| `bulletinType` | `string` | Bulletin type (always "Fechamento PTAX") |

### `Currency`

PHP 8.1 string-backed enum with all 10 supported currency codes. Use `Currency::tryFrom('USD')` for safe parsing from user input.

## Development

```bash
# Install dependencies
composer install

# Run unit tests
vendor/bin/phpunit --exclude-group integration

# Run integration tests (calls real BCB API)
vendor/bin/phpunit --group integration

# Static analysis
vendor/bin/phpstan analyse src/ --level=max

# Code modernization checks
vendor/bin/rector --dry-run
```

## License

[MIT](LICENSE)

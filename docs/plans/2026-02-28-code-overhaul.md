# BCB PTAX Library Overhaul — Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Modernize the bcb-ptax library to PHP 8.1+ with enums, typed value objects, Guzzle HTTP, i18n exceptions, and full PHPUnit test coverage.

**Architecture:** Non-static PTAX class with constructor-injected Guzzle client. Currency enum replaces inline array. Typed PTAXResult replaces raw stdClass. Custom exceptions with i18n message resolution (en_US default).

**Tech Stack:** PHP 8.1+, Guzzle 7, PHPUnit 10+, PSR-4

---

### Task 1: Project Infrastructure

**Files:**
- Modify: `composer.json`
- Create: `phpunit.xml`

**Step 1: Update composer.json**

```json
{
    "name": "wprigollopes/bcb-ptax",
    "description": "PHP client for Banco Central do Brasil PTAX currency exchange rates",
    "type": "library",
    "license": "MIT",
    "authors": [
        {
            "name": "William Prigol Lopes",
            "email": "william.prigol.lopes@gmail.com"
        }
    ],
    "minimum-stability": "stable",
    "require": {
        "php": ">=8.1",
        "guzzlehttp/guzzle": "^7.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0"
    },
    "autoload": {
        "psr-4": {
            "BcbPtax\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "BcbPtax\\Tests\\": "tests/"
        }
    }
}
```

**Step 2: Create phpunit.xml**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         cacheDirectory=".phpunit.cache">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>src</directory>
        </include>
    </source>
</phpunit>
```

**Step 3: Create directory structure and install dependencies**

Run:
```bash
mkdir -p src/Exception src/I18n tests/Unit tests/Integration
composer install
```
Expected: Dependencies installed, autoload files generated.

**Step 4: Verify PHPUnit runs**

Run: `vendor/bin/phpunit --version`
Expected: `PHPUnit 10.x.x`

**Step 5: Commit**

```bash
git add composer.json phpunit.xml
git commit -m "chore: update project infrastructure to PHP 8.1+, PSR-4, Guzzle, PHPUnit"
```

---

### Task 2: Currency Enum

**Files:**
- Create: `src/Currency.php`
- Create: `tests/Unit/CurrencyTest.php`

**Step 1: Write the failing test**

```php
<?php

declare(strict_types=1);

namespace BcbPtax\Tests\Unit;

use BcbPtax\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    public function test_usd_exists(): void
    {
        $this->assertSame('USD', Currency::USD->value);
    }

    public function test_eur_exists(): void
    {
        $this->assertSame('EUR', Currency::EUR->value);
    }

    public function test_xau_exists(): void
    {
        $this->assertSame('XAU', Currency::XAU->value);
    }

    public function test_total_currency_count(): void
    {
        $this->assertCount(177, Currency::cases());
    }

    public function test_try_from_valid(): void
    {
        $currency = Currency::tryFrom('USD');
        $this->assertSame(Currency::USD, $currency);
    }

    public function test_try_from_invalid(): void
    {
        $currency = Currency::tryFrom('INVALID');
        $this->assertNull($currency);
    }
}
```

**Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit tests/Unit/CurrencyTest.php -v`
Expected: FAIL — `Currency` class not found.

**Step 3: Write implementation**

```php
<?php

declare(strict_types=1);

namespace BcbPtax;

enum Currency: string
{
    case AFN = 'AFN';
    case ETB = 'ETB';
    case THB = 'THB';
    case PAB = 'PAB';
    case VEF = 'VEF';
    case BOB = 'BOB';
    case GHS = 'GHS';
    case CRC = 'CRC';
    case SVC = 'SVC';
    case NIO = 'NIO';
    case DKK = 'DKK';
    case ISK = 'ISK';
    case NOK = 'NOK';
    case SEK = 'SEK';
    case CZK = 'CZK';
    case GMD = 'GMD';
    case DZD = 'DZD';
    case KWD = 'KWD';
    case BHD = 'BHD';
    case IQD = 'IQD';
    case JOD = 'JOD';
    case LYD = 'LYD';
    case MKD = 'MKD';
    case RSD = 'RSD';
    case SDG = 'SDG';
    case TND = 'TND';
    case SSP = 'SSP';
    case SDR = 'SDR';
    case MAD = 'MAD';
    case AED = 'AED';
    case STD = 'STD';
    case AUD = 'AUD';
    case BSD = 'BSD';
    case BMD = 'BMD';
    case CAD = 'CAD';
    case GYD = 'GYD';
    case NAD = 'NAD';
    case BBD = 'BBD';
    case BZD = 'BZD';
    case BND = 'BND';
    case KYD = 'KYD';
    case SGD = 'SGD';
    case CLF = 'CLF';
    case FJD = 'FJD';
    case HKD = 'HKD';
    case TTD = 'TTD';
    case XCD = 'XCD';
    case USD = 'USD';
    case JMD = 'JMD';
    case LRD = 'LRD';
    case NZD = 'NZD';
    case SBD = 'SBD';
    case SRD = 'SRD';
    case VND = 'VND';
    case AMD = 'AMD';
    case CVE = 'CVE';
    case ANG = 'ANG';
    case AWG = 'AWG';
    case HUF = 'HUF';
    case CDF = 'CDF';
    case BIF = 'BIF';
    case KMF = 'KMF';
    case XAF = 'XAF';
    case XOF = 'XOF';
    case XPF = 'XPF';
    case DJF = 'DJF';
    case GNF = 'GNF';
    case MGA = 'MGA';
    case RWF = 'RWF';
    case CHF = 'CHF';
    case HTG = 'HTG';
    case PYG = 'PYG';
    case UAH = 'UAH';
    case JPY = 'JPY';
    case GEL = 'GEL';
    case ALL = 'ALL';
    case HNL = 'HNL';
    case SLL = 'SLL';
    case MDL = 'MDL';
    case RON = 'RON';
    case BGN = 'BGN';
    case GIP = 'GIP';
    case EGP = 'EGP';
    case GBP = 'GBP';
    case FKP = 'FKP';
    case LBP = 'LBP';
    case SHP = 'SHP';
    case SYP = 'SYP';
    case SZL = 'SZL';
    case LSL = 'LSL';
    case TMT = 'TMT';
    case MZN = 'MZN';
    case ERN = 'ERN';
    case NGN = 'NGN';
    case AOA = 'AOA';
    case TWD = 'TWD';
    case TRY = 'TRY';
    case PEN = 'PEN';
    case BTN = 'BTN';
    case MRO = 'MRO';
    case MRU = 'MRU';
    case TOP = 'TOP';
    case MOP = 'MOP';
    case ARS = 'ARS';
    case CLP = 'CLP';
    case COP = 'COP';
    case CUP = 'CUP';
    case DOP = 'DOP';
    case PHP = 'PHP';
    case MXN = 'MXN';
    case UYU = 'UYU';
    case BWP = 'BWP';
    case MWK = 'MWK';
    case ZMW = 'ZMW';
    case GTQ = 'GTQ';
    case MMK = 'MMK';
    case PGK = 'PGK';
    case HRK = 'HRK';
    case LAK = 'LAK';
    case ZAR = 'ZAR';
    case CNY = 'CNY';
    case CNH = 'CNH';
    case QAR = 'QAR';
    case OMR = 'OMR';
    case YER = 'YER';
    case IRR = 'IRR';
    case SAR = 'SAR';
    case KHR = 'KHR';
    case MYR = 'MYR';
    case RUB = 'RUB';
    case BYN = 'BYN';
    case TJS = 'TJS';
    case MUR = 'MUR';
    case NPR = 'NPR';
    case SCR = 'SCR';
    case LKR = 'LKR';
    case INR = 'INR';
    case IDR = 'IDR';
    case MVR = 'MVR';
    case PKR = 'PKR';
    case ILS = 'ILS';
    case KGS = 'KGS';
    case UZS = 'UZS';
    case BDT = 'BDT';
    case WST = 'WST';
    case KZT = 'KZT';
    case MNT = 'MNT';
    case VUV = 'VUV';
    case KRW = 'KRW';
    case TZS = 'TZS';
    case KES = 'KES';
    case UGX = 'UGX';
    case SOS = 'SOS';
    case PLN = 'PLN';
    case EUR = 'EUR';
    case XAU = 'XAU';
}
```

**Step 4: Run test to verify it passes**

Run: `vendor/bin/phpunit tests/Unit/CurrencyTest.php -v`
Expected: All 6 tests PASS.

**Step 5: Commit**

```bash
git add src/Currency.php tests/Unit/CurrencyTest.php
git commit -m "feat: add Currency enum with all 177 supported codes"
```

---

### Task 3: I18n Messages

**Files:**
- Create: `src/I18n/Messages.php`
- Create: `src/I18n/en_US.php`
- Create: `src/I18n/pt_BR.php`
- Create: `tests/Unit/MessagesTest.php`

**Step 1: Write the failing test**

```php
<?php

declare(strict_types=1);

namespace BcbPtax\Tests\Unit;

use BcbPtax\I18n\Messages;
use PHPUnit\Framework\TestCase;

class MessagesTest extends TestCase
{
    public function test_get_english_message(): void
    {
        $msg = Messages::get('invalid_currency', 'en_US', ['currency' => 'XYZ']);
        $this->assertSame('Invalid currency: XYZ', $msg);
    }

    public function test_get_portuguese_message(): void
    {
        $msg = Messages::get('invalid_currency', 'pt_BR', ['currency' => 'XYZ']);
        $this->assertSame('Moeda inválida: XYZ', $msg);
    }

    public function test_falls_back_to_english(): void
    {
        $msg = Messages::get('invalid_currency', 'fr_FR', ['currency' => 'XYZ']);
        $this->assertSame('Invalid currency: XYZ', $msg);
    }

    public function test_all_message_keys_exist_in_both_locales(): void
    {
        $en = require __DIR__ . '/../../src/I18n/en_US.php';
        $pt = require __DIR__ . '/../../src/I18n/pt_BR.php';
        $this->assertSame(array_keys($en), array_keys($pt));
    }
}
```

**Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit tests/Unit/MessagesTest.php -v`
Expected: FAIL — `Messages` class not found.

**Step 3: Create en_US.php**

```php
<?php

declare(strict_types=1);

return [
    'invalid_currency' => 'Invalid currency: :currency',
    'api_error' => 'BCB API returned HTTP :code',
    'json_decode_error' => 'Failed to decode API response',
    'no_closing_ptax' => 'No closing PTAX found for :currency on :date',
];
```

**Step 4: Create pt_BR.php**

```php
<?php

declare(strict_types=1);

return [
    'invalid_currency' => 'Moeda inválida: :currency',
    'api_error' => 'A API do BCB retornou HTTP :code',
    'json_decode_error' => 'Falha ao decodificar resposta da API',
    'no_closing_ptax' => 'Nenhum PTAX de fechamento encontrado para :currency em :date',
];
```

**Step 5: Create Messages.php**

```php
<?php

declare(strict_types=1);

namespace BcbPtax\I18n;

class Messages
{
    private static array $cache = [];

    public static function get(string $key, string $locale, array $params = []): string
    {
        $messages = self::load($locale);

        if (!isset($messages[$key])) {
            $messages = self::load('en_US');
        }

        $message = $messages[$key] ?? $key;

        foreach ($params as $placeholder => $value) {
            $message = str_replace(':' . $placeholder, (string) $value, $message);
        }

        return $message;
    }

    private static function load(string $locale): array
    {
        if (isset(self::$cache[$locale])) {
            return self::$cache[$locale];
        }

        $file = __DIR__ . '/' . $locale . '.php';

        if (!file_exists($file)) {
            $file = __DIR__ . '/en_US.php';
            $locale = 'en_US';
        }

        self::$cache[$locale] = require $file;

        return self::$cache[$locale];
    }
}
```

**Step 6: Run test to verify it passes**

Run: `vendor/bin/phpunit tests/Unit/MessagesTest.php -v`
Expected: All 4 tests PASS.

**Step 7: Commit**

```bash
git add src/I18n/ tests/Unit/MessagesTest.php
git commit -m "feat: add i18n message system with en_US and pt_BR locales"
```

---

### Task 4: Custom Exceptions

**Files:**
- Create: `src/Exception/InvalidCurrencyException.php`
- Create: `src/Exception/ApiException.php`
- Create: `tests/Unit/ExceptionTest.php`

**Step 1: Write the failing test**

```php
<?php

declare(strict_types=1);

namespace BcbPtax\Tests\Unit;

use BcbPtax\Exception\InvalidCurrencyException;
use BcbPtax\Exception\ApiException;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    public function test_invalid_currency_english(): void
    {
        $e = InvalidCurrencyException::make('XYZ', 'en_US');
        $this->assertSame('Invalid currency: XYZ', $e->getMessage());
    }

    public function test_invalid_currency_portuguese(): void
    {
        $e = InvalidCurrencyException::make('XYZ', 'pt_BR');
        $this->assertSame('Moeda inválida: XYZ', $e->getMessage());
    }

    public function test_api_error_with_http_code(): void
    {
        $e = ApiException::httpError(500, 'en_US');
        $this->assertSame('BCB API returned HTTP 500', $e->getMessage());
        $this->assertSame(500, $e->getCode());
    }

    public function test_api_error_json_decode(): void
    {
        $e = ApiException::jsonError('en_US');
        $this->assertSame('Failed to decode API response', $e->getMessage());
    }

    public function test_api_error_no_closing_ptax(): void
    {
        $e = ApiException::noClosingPtax('USD', '01-15-2024', 'en_US');
        $this->assertSame('No closing PTAX found for USD on 01-15-2024', $e->getMessage());
    }
}
```

**Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit tests/Unit/ExceptionTest.php -v`
Expected: FAIL — exception classes not found.

**Step 3: Create InvalidCurrencyException.php**

```php
<?php

declare(strict_types=1);

namespace BcbPtax\Exception;

use BcbPtax\I18n\Messages;

class InvalidCurrencyException extends \InvalidArgumentException
{
    public static function make(string $currency, string $locale = 'en_US'): self
    {
        return new self(Messages::get('invalid_currency', $locale, ['currency' => $currency]));
    }
}
```

**Step 4: Create ApiException.php**

```php
<?php

declare(strict_types=1);

namespace BcbPtax\Exception;

use BcbPtax\I18n\Messages;

class ApiException extends \RuntimeException
{
    public static function httpError(int $httpCode, string $locale = 'en_US'): self
    {
        return new self(
            Messages::get('api_error', $locale, ['code' => (string) $httpCode]),
            $httpCode,
        );
    }

    public static function jsonError(string $locale = 'en_US'): self
    {
        return new self(Messages::get('json_decode_error', $locale));
    }

    public static function noClosingPtax(string $currency, string $date, string $locale = 'en_US'): self
    {
        return new self(Messages::get('no_closing_ptax', $locale, [
            'currency' => $currency,
            'date' => $date,
        ]));
    }
}
```

**Step 5: Run test to verify it passes**

Run: `vendor/bin/phpunit tests/Unit/ExceptionTest.php -v`
Expected: All 5 tests PASS.

**Step 6: Commit**

```bash
git add src/Exception/ tests/Unit/ExceptionTest.php
git commit -m "feat: add custom exceptions with i18n support"
```

---

### Task 5: PTAXResult Value Object

**Files:**
- Create: `src/PTAXResult.php`
- Create: `tests/Unit/PTAXResultTest.php`

**Step 1: Write the failing test**

```php
<?php

declare(strict_types=1);

namespace BcbPtax\Tests\Unit;

use BcbPtax\PTAXResult;
use PHPUnit\Framework\TestCase;

class PTAXResultTest extends TestCase
{
    public function test_creates_from_api_response(): void
    {
        $apiData = (object) [
            'cotacaoCompra' => 5.1234,
            'cotacaoVenda' => 5.1240,
            'dataHoraCotacao' => '2024-01-15 13:09:48.193',
            'tipoBoletim' => 'Fechamento PTAX',
        ];

        $result = PTAXResult::fromApiResponse($apiData);

        $this->assertSame(5.1234, $result->buyRate);
        $this->assertSame(5.1240, $result->sellRate);
        $this->assertSame('2024-01-15', $result->date->format('Y-m-d'));
        $this->assertSame('Fechamento PTAX', $result->bulletinType);
    }

    public function test_readonly_properties(): void
    {
        $apiData = (object) [
            'cotacaoCompra' => 5.0,
            'cotacaoVenda' => 5.1,
            'dataHoraCotacao' => '2024-01-15 13:00:00.000',
            'tipoBoletim' => 'Fechamento PTAX',
        ];

        $result = PTAXResult::fromApiResponse($apiData);

        $this->expectException(\Error::class);
        $result->buyRate = 999.0;
    }
}
```

**Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit tests/Unit/PTAXResultTest.php -v`
Expected: FAIL — `PTAXResult` class not found.

**Step 3: Write implementation**

```php
<?php

declare(strict_types=1);

namespace BcbPtax;

class PTAXResult
{
    public function __construct(
        public readonly float $buyRate,
        public readonly float $sellRate,
        public readonly \DateTimeImmutable $date,
        public readonly string $bulletinType,
    ) {}

    public static function fromApiResponse(object $data): self
    {
        return new self(
            buyRate: $data->cotacaoCompra,
            sellRate: $data->cotacaoVenda,
            date: new \DateTimeImmutable($data->dataHoraCotacao),
            bulletinType: $data->tipoBoletim,
        );
    }
}
```

**Step 4: Run test to verify it passes**

Run: `vendor/bin/phpunit tests/Unit/PTAXResultTest.php -v`
Expected: All 2 tests PASS.

**Step 5: Commit**

```bash
git add src/PTAXResult.php tests/Unit/PTAXResultTest.php
git commit -m "feat: add PTAXResult readonly value object"
```

---

### Task 6: PTAX Main Class

**Files:**
- Create: `src/PTAX.php` (replaces `src/bcbPTAX/PTAX.php`)
- Create: `tests/Unit/PTAXTest.php`

**Step 1: Write the failing test**

```php
<?php

declare(strict_types=1);

namespace BcbPtax\Tests\Unit;

use BcbPtax\Currency;
use BcbPtax\PTAX;
use BcbPtax\PTAXResult;
use BcbPtax\Exception\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class PTAXTest extends TestCase
{
    private function createPtax(MockHandler $mock): PTAX
    {
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        return new PTAX(client: $client);
    }

    private function successBody(): string
    {
        return json_encode([
            '@odata.context' => 'https://example.com',
            'value' => [
                [
                    'cotacaoCompra' => 4.9704,
                    'cotacaoVenda' => 4.9710,
                    'dataHoraCotacao' => '2024-01-15 13:09:48.193',
                    'tipoBoletim' => 'Abertura',
                ],
                [
                    'cotacaoCompra' => 4.9680,
                    'cotacaoVenda' => 4.9686,
                    'dataHoraCotacao' => '2024-01-15 13:09:48.193',
                    'tipoBoletim' => 'Fechamento PTAX',
                ],
            ],
        ]);
    }

    public function test_get_returns_closing_ptax(): void
    {
        $mock = new MockHandler([
            new Response(200, [], $this->successBody()),
        ]);

        $ptax = $this->createPtax($mock);
        $result = $ptax->get(Currency::USD, new \DateTime('2024-01-15'));

        $this->assertInstanceOf(PTAXResult::class, $result);
        $this->assertSame(4.9680, $result->buyRate);
        $this->assertSame(4.9686, $result->sellRate);
        $this->assertSame('Fechamento PTAX', $result->bulletinType);
    }

    public function test_get_throws_on_http_error(): void
    {
        $mock = new MockHandler([
            new Response(500, [], 'Server Error'),
        ]);

        $ptax = $this->createPtax($mock);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('BCB API returned HTTP 500');
        $ptax->get(Currency::USD, new \DateTime('2024-01-15'));
    }

    public function test_get_throws_on_invalid_json(): void
    {
        $mock = new MockHandler([
            new Response(200, [], 'not json'),
        ]);

        $ptax = $this->createPtax($mock);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Failed to decode API response');
        $ptax->get(Currency::USD, new \DateTime('2024-01-15'));
    }

    public function test_get_throws_when_no_closing_ptax(): void
    {
        $body = json_encode([
            '@odata.context' => 'https://example.com',
            'value' => [
                [
                    'cotacaoCompra' => 4.97,
                    'cotacaoVenda' => 4.98,
                    'dataHoraCotacao' => '2024-01-15 13:09:48.193',
                    'tipoBoletim' => 'Abertura',
                ],
            ],
        ]);

        $mock = new MockHandler([
            new Response(200, [], $body),
        ]);

        $ptax = $this->createPtax($mock);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('No closing PTAX found for USD on 01-15-2024');
        $ptax->get(Currency::USD, new \DateTime('2024-01-15'));
    }

    public function test_get_throws_when_empty_value_array(): void
    {
        $body = json_encode([
            '@odata.context' => 'https://example.com',
            'value' => [],
        ]);

        $mock = new MockHandler([
            new Response(200, [], $body),
        ]);

        $ptax = $this->createPtax($mock);

        $this->expectException(ApiException::class);
        $ptax->get(Currency::USD, new \DateTime('2024-01-15'));
    }

    public function test_locale_affects_exception_messages(): void
    {
        $body = json_encode(['@odata.context' => '', 'value' => []]);
        $mock = new MockHandler([
            new Response(200, [], $body),
        ]);

        $ptax = $this->createPtax($mock);
        $ptax = new PTAX(
            client: new Client(['handler' => HandlerStack::create($mock)]),
            locale: 'pt_BR',
        );

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Nenhum PTAX de fechamento encontrado para USD');
        $ptax->get(Currency::USD, new \DateTime('2024-01-15'));
    }

    public function test_default_client_is_created(): void
    {
        $ptax = new PTAX();
        $this->assertInstanceOf(PTAX::class, $ptax);
    }
}
```

**Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit tests/Unit/PTAXTest.php -v`
Expected: FAIL — new `PTAX` class not found (old one is under different namespace/path).

**Step 3: Write implementation**

```php
<?php

declare(strict_types=1);

namespace BcbPtax;

use BcbPtax\Exception\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class PTAX
{
    private const BASE_URL = 'https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata';

    private ClientInterface $client;
    private string $locale;

    public function __construct(
        ?ClientInterface $client = null,
        string $locale = 'en_US',
    ) {
        $this->client = $client ?? new Client();
        $this->locale = $locale;
    }

    public function get(Currency $currency, \DateTimeInterface $date): PTAXResult
    {
        $formattedDate = $date->format('m-d-Y');

        $url = self::BASE_URL . '/CotacaoMoedaDia(moeda=@moeda,dataCotacao=@dataCotacao)';

        $response = $this->client->request('GET', $url, [
            'query' => [
                '@moeda' => "'{$currency->value}'",
                '@dataCotacao' => "'{$formattedDate}'",
                '$format' => 'json',
            ],
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            throw ApiException::httpError($statusCode, $this->locale);
        }

        $body = $response->getBody()->getContents();
        $data = json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE || !is_object($data)) {
            throw ApiException::jsonError($this->locale);
        }

        if (!isset($data->value) || !is_array($data->value)) {
            throw ApiException::jsonError($this->locale);
        }

        foreach ($data->value as $quotation) {
            if ($quotation->tipoBoletim === 'Fechamento PTAX') {
                return PTAXResult::fromApiResponse($quotation);
            }
        }

        throw ApiException::noClosingPtax($currency->value, $formattedDate, $this->locale);
    }
}
```

**Step 4: Run test to verify it passes**

Run: `vendor/bin/phpunit tests/Unit/PTAXTest.php -v`
Expected: All 7 tests PASS.

**Step 5: Commit**

```bash
git add src/PTAX.php tests/Unit/PTAXTest.php
git commit -m "feat: add PTAX main class with Guzzle and i18n support"
```

---

### Task 7: Integration Test

**Files:**
- Create: `tests/Integration/BcbApiTest.php`

**Step 1: Write integration test**

```php
<?php

declare(strict_types=1);

namespace BcbPtax\Tests\Integration;

use BcbPtax\Currency;
use BcbPtax\PTAX;
use BcbPtax\PTAXResult;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 */
class BcbApiTest extends TestCase
{
    public function test_fetches_real_usd_ptax(): void
    {
        $ptax = new PTAX();
        $result = $ptax->get(Currency::USD, new \DateTime('2024-01-15'));

        $this->assertInstanceOf(PTAXResult::class, $result);
        $this->assertGreaterThan(0, $result->buyRate);
        $this->assertGreaterThan(0, $result->sellRate);
        $this->assertSame('Fechamento PTAX', $result->bulletinType);
    }

    public function test_fetches_real_eur_ptax(): void
    {
        $ptax = new PTAX();
        $result = $ptax->get(Currency::EUR, new \DateTime('2024-01-15'));

        $this->assertInstanceOf(PTAXResult::class, $result);
        $this->assertGreaterThan(0, $result->buyRate);
    }
}
```

**Step 2: Run unit tests only (skip integration)**

Run: `vendor/bin/phpunit --exclude-group integration -v`
Expected: All previous tests still PASS.

**Step 3: Run integration tests (requires internet)**

Run: `vendor/bin/phpunit --group integration -v`
Expected: 2 tests PASS (calls real BCB API).

**Step 4: Commit**

```bash
git add tests/Integration/BcbApiTest.php
git commit -m "test: add integration tests for real BCB API calls"
```

---

### Task 8: Cleanup Old Files

**Files:**
- Delete: `src/bcbPTAX/PTAX.php`
- Delete: `src/bcbPTAX/` (directory)
- Delete: `tests/test.php`

**Step 1: Remove old files**

```bash
rm -rf src/bcbPTAX/
rm tests/test.php
```

**Step 2: Run all unit tests to confirm nothing broke**

Run: `vendor/bin/phpunit --exclude-group integration -v`
Expected: All tests PASS.

**Step 3: Commit**

```bash
git add -A
git commit -m "chore: remove legacy code and test file"
```

---

### Task 9: Final Verification

**Step 1: Run full test suite**

Run: `vendor/bin/phpunit -v`
Expected: All tests PASS (unit + integration).

**Step 2: Verify directory structure**

Run: `find src tests -type f | sort`
Expected:
```
src/Currency.php
src/Exception/ApiException.php
src/Exception/InvalidCurrencyException.php
src/I18n/Messages.php
src/I18n/en_US.php
src/I18n/pt_BR.php
src/PTAX.php
src/PTAXResult.php
tests/Integration/BcbApiTest.php
tests/Unit/CurrencyTest.php
tests/Unit/ExceptionTest.php
tests/Unit/MessagesTest.php
tests/Unit/PTAXResultTest.php
tests/Unit/PTAXTest.php
```

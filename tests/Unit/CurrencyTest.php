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

    public function test_jpy_exists(): void
    {
        $this->assertSame('JPY', Currency::JPY->value);
    }

    public function test_total_currency_count(): void
    {
        $this->assertCount(10, Currency::cases());
    }

    public function test_all_currencies_match_bcb_api(): void
    {
        $expected = ['AUD', 'CAD', 'CHF', 'DKK', 'EUR', 'GBP', 'JPY', 'NOK', 'SEK', 'USD'];
        $actual = array_map(fn(Currency $c) => $c->value, Currency::cases());
        sort($actual);
        $this->assertSame($expected, $actual);
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

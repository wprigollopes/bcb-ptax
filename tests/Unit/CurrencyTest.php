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
        $this->assertCount(156, Currency::cases());
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

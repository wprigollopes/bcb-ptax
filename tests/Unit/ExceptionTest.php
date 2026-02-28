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

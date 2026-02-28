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

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

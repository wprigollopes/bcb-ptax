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
    private function createPtax(MockHandler $mock, string $locale = 'en_US'): PTAX
    {
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        return new PTAX(client: $client, locale: $locale);
    }

    private function successBody(): string
    {
        return json_encode([
            '@odata.context' => 'https://example.com',
            'value' => [
                [
                    'paridadeCompra' => 1.0,
                    'paridadeVenda' => 1.0,
                    'cotacaoCompra' => 4.9704,
                    'cotacaoVenda' => 4.9710,
                    'dataHoraCotacao' => '2024-01-15 13:09:48.193',
                    'tipoBoletim' => 'Abertura',
                ],
                [
                    'paridadeCompra' => 1.0,
                    'paridadeVenda' => 1.0,
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
                    'paridadeCompra' => 1.0,
                    'paridadeVenda' => 1.0,
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

        $ptax = $this->createPtax($mock, 'pt_BR');

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

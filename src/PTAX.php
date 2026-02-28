<?php

declare(strict_types=1);

namespace BcbPtax;

use BcbPtax\Exception\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class PTAX
{
    private const BASE_URL = 'https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata';

    public function __construct(private readonly ClientInterface $client = new Client(), private readonly string $locale = 'en_US')
    {
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
            'http_errors' => false,
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

        /** @var list<object{cotacaoCompra: float, cotacaoVenda: float, dataHoraCotacao: string, tipoBoletim: string}> $values */
        $values = $data->value;

        foreach ($values as $quotation) {
            if ($quotation->tipoBoletim === 'Fechamento PTAX') {
                return PTAXResult::fromApiResponse($quotation);
            }
        }

        throw ApiException::noClosingPtax($currency->value, $formattedDate, $this->locale);
    }
}

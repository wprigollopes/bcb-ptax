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

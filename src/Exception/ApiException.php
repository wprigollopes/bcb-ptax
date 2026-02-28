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

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

<?php

declare(strict_types=1);

namespace BcbPtax;

enum Currency: string
{
    case AUD = 'AUD';
    case CAD = 'CAD';
    case CHF = 'CHF';
    case DKK = 'DKK';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case JPY = 'JPY';
    case NOK = 'NOK';
    case SEK = 'SEK';
    case USD = 'USD';
}

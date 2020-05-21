# bcb-ptax composer package

A simple composer package to connect on "Banco Central do Brasil" public API and returns a cotation from BRL to a specific day and currency.

How to install
--------------

```
composer require wprigollopes/bcb-ptax
```

How to use
----------

You can call `use bcbPTAX\PTAX` and call statically `PTAX::getPTAX(<currency>, <date>, <dateFormat>)` passing the currency (1), date (2) and date format (3) parameters. As the API is located on "Banco Central do Brazil", all quotations are converted from "BRL" to `<currency>`.

```
use bcbPTAX\PTAX;
$return = PTAX::getPTAX('USD', '2018-04-23', 'Y-m-d');
```

This will extract the currency quotation on specified day.

Currencies supported (ISO 4217 pattern) - More info in https://en.wikipedia.org/wiki/ISO_4217: 

`['AFN', 'ETB', 'THB', 'PAB', 'VEF', 'BOB', 'GHS', 'CRC', 'SVC', 'NIO', 'DKK', 'ISK', 'NOK', 'SEK', 'CZK', 'GMD', 'DZD', 'KWD', 'BHD', 'IQD', 'JOD', 'LYD', 'MKD', 'RSD', 'SDG', 'TND', 'SSP', 'SDR', 'MAD', 'AED', 'STD', 'AUD', 'BSD', 'BMD', 'CAD', 'GYD', 'NAD', 'BBD', 'BZD', 'BND', 'KYD', 'SGD', 'CLF', 'FJD', 'HKD', 'TTD', 'XCD', 'USD', 'JMD', 'LRD', 'NZD', 'SBD', 'SRD', 'VND', 'AMD', 'CVE', 'ANG', 'AWG', 'HUF', 'CDF', 'BIF', 'KMF', 'XAF', 'XOF', 'XPF', 'DJF', 'GNF', 'MGA', 'RWF', 'CHF', 'HTG', 'PYG', 'UAH', 'JPY', 'GEL', 'ALL', 'HNL', 'SLL', 'MDL', 'RON', 'BGN', 'GIP', 'EGP', 'GBP', 'FKP', 'LBP', 'SHP', 'SYP', 'SZL', 'LSL', 'TMT', 'MZN', 'ERN', 'NGN', 'AOA', 'TWD', 'TRY', 'PEN', 'BTN', 'MRO', 'MRU', 'TOP', 'MOP', 'ARS', 'CLP', 'COP', 'CUP', 'DOP', 'PHP', 'MXN', 'UYU', 'BWP', 'MWK', 'ZMW', 'GTQ', 'MMK', 'PGK', 'HRK', 'LAK', 'ZAR', 'CNY', 'CNH', 'QAR', 'OMR', 'YER', 'IRR', 'SAR', 'KHR', 'MYR', 'RUB', 'BYN', 'TJS', 'MUR', 'NPR', 'SCR', 'LKR', 'INR', 'IDR', 'MVR', 'PKR', 'ILS', 'KGS', 'UZS', 'BDT', 'WST', 'KZT', 'MNT', 'VUV', 'KRW', 'TZS', 'KES', 'UGX', 'SOS', 'PLN', 'EUR', 'XAU']`

Please, feel free to suggest changes!

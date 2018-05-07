<?php
namespace bcbPTAX;
use \Datetime;
use \Exception;
use \curl;

class PTAX
{
    //
    // getPtax
    // @parameters 
    //  $currency: Desired currency PTAX
    //  $date: Desired currency PTAX date
    //  $dateFormat: PTAX date format (default value: 'dd/mm/yyyy') - Uses DateTime format defaults
    //
    // @returns
    //  Returns an object with info if the parameters are valid, otherwise, false
    //
    public static function getPTAX($currency, $date, $dateFormat = 'dd/mm/yyyy')
    {
        $format = 'json';
        $currencies = ['AFN', 
            'ETB',
            'THB',
            'PAB',
            'VEF',
            'BOB',
            'GHS',
            'CRC',
            'SVC',
            'NIO',
            'DKK',
            'ISK',
            'NOK',
            'SEK',
            'CZK',
            'GMD',
            'DZD',
            'KWD',
            'BHD',
            'IQD',
            'JOD',
            'LYD',
            'MKD',
            'RSD',
            'SDG',
            'TND',
            'SSP',
            'SDR',
            'MAD',
            'AED',
            'STD',
            'AUD',
            'BSD',
            'BMD',
            'CAD',
            'GYD',
            'NAD',
            'BBD',
            'BZD',
            'BND',
            'KYD',
            'SGD',
            'CLF',
            'FJD',
            'HKD',
            'TTD',
            'XCD',
            'USD',
            'JMD',
            'LRD',
            'NZD',
            'SBD',
            'SRD',
            'VND',
            'AMD',
            'CVE',
            'ANG',
            'AWG',
            'HUF',
            'CDF',
            'BIF',
            'KMF',
            'XAF',
            'XOF',
            'XPF',
            'DJF',
            'GNF',
            'MGA',
            'RWF',
            'CHF',
            'HTG',
            'PYG',
            'UAH',
            'JPY',
            'GEL',
            'ALL',
            'HNL',
            'SLL',
            'MDL',
            'RON',
            'BGN',
            'GIP',
            'EGP',
            'GBP',
            'FKP',
            'LBP',
            'SHP',
            'SYP',
            'SZL',
            'LSL',
            'TMT',
            'MZN',
            'ERN',
            'NGN',
            'AOA',
            'TWD',
            'TRY',
            'PEN',
            'BTN',
            'MRO',
            'MRU',
            'TOP',
            'MOP',
            'ARS',
            'CLP',
            'COP',
            'CUP',
            'DOP',
            'PHP',
            'MXN',
            'UYU',
            'BWP',
            'MWK',
            'ZMW',
            'GTQ',
            'MMK',
            'PGK',
            'HRK',
            'LAK',
            'ZAR',
            'CNY',
            'CNH',
            'QAR',
            'OMR',
            'YER',
            'IRR',
            'SAR',
            'KHR',
            'MYR',
            'RUB',
            'BYN',
            'TJS',
            'MUR',
            'NPR',
            'SCR',
            'LKR',
            'INR',
            'IDR',
            'MVR',
            'PKR',
            'ILS',
            'KGS',
            'UZS',
            'BDT',
            'WST',
            'KZT',
            'MNT',
            'VUV',
            'KRW',
            'TZS',
            'KES',
            'UGX',
            'SOS',
            'PLN',
            'EUR',
            'XAU'
            ]; 

        if (!in_array($currency, $currencies)) {
            throw new Exception('Moeda inválida');
        }
        $datePTAX = self::checkDate($dateFormat, $date);
        if ($datePTAX == false) {
            throw new Exception('Data informada é inválida');
        }

        
        $datePTAX = self::checkDate($dateFormat, $date);
        $url = 'https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoMoedaDia(moeda=@moeda,dataCotacao=@dataCotacao)?%40moeda=%27'.$currency.'%27&%40dataCotacao=%27'.$datePTAX.'%27&%24format='.$format;
        $call = curl_init($url);
        curl_setopt($call, CURLOPT_RETURNTRANSFER, 1);       
        $result = curl_exec($call);
        $httpcode = curl_getinfo($call, CURLINFO_HTTP_CODE);        
        curl_close($call);
        if ($httpcode != '200') {
            throw new Exception('A API retornou um código de erro. Código HTTP '.$httpcode);
        }
        $cotation = json_decode($result);
        if (is_array($cotation->value)) {
            foreach ($cotation->value as $cot) {
                if ($cot->tipoBoletim == 'Fechamento PTAX') {
                    return $cot;
                }
            }
        }
        return false;
    }

    private static function checkDate($dateFormat, $date)
    {
        $d = DateTime::createFromFormat($dateFormat, $date);
        if ($d && $d->format($dateFormat) == $date)
        {
            return $d->format('m-d-Y');
        }
        return false;
    }
}

<?php 
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload
use bcbPTAX\PTAX;
$return = PTAX::getPTAX('USD', '2018-04-23', 'Y-m-d');

var_dump($return->cotacaoCompra);
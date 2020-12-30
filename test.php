<?php

use Hidehalo\Nanoid\Client as NanoId;

require 'vendor/autoload.php';
require 'lib/ravendb/session.php';


$raven = new \RavenDB\Session('http://192.168.0.102:28080', 'omgfin-exchange');

/*var_dump($raven->put('product/ApTZ8g1PYnTWI0UGq23Ah', [
    'name' => 'PS5',
    'price' => '100',
    '@metadata' => [
        '@collection' => 'Products'
    ]
]));*/
var_dump($raven->add('product/', [
    'name' => 'Window 10',
    'price' => '300',
    '@metadata' => [
        '@collection' => 'Products'
    ]
]));
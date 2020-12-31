<?php
require 'vendor/autoload.php';
require 'lib/ravendb/session.php';

$raven = new \RavenDB\Session('http://192.168.0.102:28080', 'omgfin-exchange');

Co\run(function () use ($raven) {
    var_dump($new = $raven->add('product/', [
        'name' => 'Window 10',
        'price' => '300',
        '@metadata' => [
            '@collection' => 'Products'
        ]
    ]));

    var_dump($raven->get($new->Id));

    var_dump($raven->del($new->Id));
});
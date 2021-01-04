<?php
require 'vendor/autoload.php';
require 'lib/raven/session.php';

use Hidehalo\Nanoid\Client as NanoId;

if (true) {

    $raven = new \RavenDB\Session('http://192.168.0.102:28080', 'omgfin-exchange');

    Co\run(function () use ($raven) {

        $nano = new NanoId();
        $id = 'product/' . $nano->generateId(21, NanoId::MODE_DYNAMIC);

        $rss = [0, 0];

        for ($i = 0; $i < 1000; $i++) {
            $rs = $raven->set($id, [
                'name' => 'Window 10',
                'price' => '300',
                '@metadata' => [
                    '@collection' => 'Products'
                ]
            ]);
            if ($rs) {
                $rss[0]++;
            } else {
                $rss[1]++;
            }
        }

        var_dump($rss);

        return;

        var_dump($new = $raven->add('product/', [
            'name' => 'Window 10',
            'price' => '300',
            '@metadata' => [
                '@collection' => 'Products'
            ]
        ]));

        var_dump($raven->get($new->Id));

        //var_dump($raven->del("product/0GtlUl1_CbEsYHvm4GOrZ"));
    });

}
<?php
require 'vendor/autoload.php';
require 'lib/raven/session.php';

use EasySwoole\FastCache\Cache;
use EasySwoole\FastCache\Job;

$job = new Job();
$job->setData("siam"); // any type of data
$job->setQueue("siam_queue");
$jobId = Cache::getInstance()->putJob($job);
var_dump($jobId);


exit(0);

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

    //var_dump($raven->del("product/0GtlUl1_CbEsYHvm4GOrZ"));
});
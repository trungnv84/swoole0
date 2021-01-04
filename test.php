<?php
require 'vendor/autoload.php';
require 'lib/raven/session.php';

use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\Pool;
use EasySwoole\Queue\Driver\Redis;
use EasySwoole\Queue\Queue;
use EasySwoole\Queue\Job;

$config = new RedisConfig([
    'host' => '127.0.0.1',
    'port' => '6379',
    'auth' => '',
    'serialize' => RedisConfig::SERIALIZE_NONE
]);
$redis = new Pool($config);

$driver = new Redis($redis);
$queue = new Queue($driver);

go(function () use ($queue) {
    while (1) {
        $job = new Job();
        $job->setJobData(time());
        $id = $queue->producer()->push($job);
        var_dump('job create for Id :' . $id);
        \co::sleep(3);
    }
});

go(function () use ($queue) {
    $queue->consumer()->listen(function (Job $job) {
        var_dump($job);
        \co::sleep(5);
    });
});


if (false) {

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

}
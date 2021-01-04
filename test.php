<?php
require 'vendor/autoload.php';
require 'lib/raven/session.php';

use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\Pool;
use EasySwoole\Queue\Driver\Redis;
use EasySwoole\Queue\Queue;
use EasySwoole\Queue\Job;

go(function () {
    //queue组件会自动强制进行序列化
    \EasySwoole\RedisPool\RedisPool::getInstance()->register(new \EasySwoole\Redis\Config\RedisConfig(
        [
            'host' => '127.0.0.1',
            'port' => '6379',
            'auth' => '',
        ]
    ), 'queue');
    $redisPool = \EasySwoole\RedisPool\RedisPool::getInstance()->getPool('queue');
    $driver = new \EasySwoole\Queue\Driver\Redis($redisPool, 'queue');
    $queue = new EasySwoole\Queue\Queue($driver);

    // 生产者
    go(function () use ($queue) {
        $i = 0;
        while (1) {
            $job = new \EasySwoole\Queue\Job();
            $data = ++$i;
            $job->setJobData($data);
            $id = $queue->producer()->push($job);
            echo('create1 data:' . $data . PHP_EOL);
            \co::sleep(3);
        }
    });

    // 消费者
    go(function () use ($queue) {
        $queue->consumer()->listen(function (\EasySwoole\Queue\Job $job) {
            \co::sleep(rand(2, 10));
            echo "job1 data:" . $job->getJobData() . PHP_EOL;
        });
    });


    /*$driver = new \EasySwoole\Queue\Driver\Redis($redisPool,'queue2');
    $queue2 = new EasySwoole\Queue\Queue($driver);
    go(function ()use($queue2){
        while (1){
            $job = new \EasySwoole\Queue\Job();
            $data = "2:".rand(1,99);
            $job->setJobData($data);
            $id = $queue2->producer()->push($job);
            echo ('create2 data :'.$data.PHP_EOL);
            \co::sleep(3);
        }
    });
    go(function ()use($queue2){
        $queue2->consumer()->listen(function (\EasySwoole\Queue\Job $job){
            echo "job2 data:".$job->getJobData().PHP_EOL;
        });
    });*/
});


if (false) {


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
            \co::sleep(rand(2, 10));
            var_dump($job);
        });
    });

}


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
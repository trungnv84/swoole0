<?php
require 'vendor/autoload.php';
require 'lib/raven/session.php';

use EasySwoole\FastCache\Cache;
use EasySwoole\FastCache\Job;

/*protected static $_default_config = array(
    'socket_type' => 'tcp',
    'host' => '127.0.0.1',
    'password' => NULL,
    'port' => 6379,
    'timeout' => 0
);*/

go(function () {
// get the task that failed to execute can be resent
    $job = new Job();
    $job->setData("siam");
    $job->setQueue("siam_queue");
    $jobId = Cache::getInstance()->putJob($job);
    var_dump($jobId);

    $job = Cache::getInstance()->getJob('siam_queue');
    var_dump($job);

    if ($job === null) {
        echo "No task\n";
    } else {
        // Execution of business logic
        $doRes = false;
        if (!$doRes) {
            // Business logic failed and needs to be resent
            // If the delay queue needs to be resent immediately, you need to clear the delay attribute here.
            // $job->setDelay(0);
            // If the normal queue needs to delay retransmission, set the delay attribute
            // $job->setDelay(5);
            $res = Cache::getInstance()->releaseJob($job);
            var_dump($res);
        } else {
            // To delete or resend after execution, otherwise the timeout will be automatically resent.
            Cache::getInstance()->deleteJob($job);
        }
    }
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
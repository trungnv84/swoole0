<?php
$http = new Swoole\HTTP\Server("0.0.0.0", 38030, SWOOLE_BASE);

$http->on('start', function ($server) {
    echo "Swoole http server is started at http://127.0.0.1:38030\n";
});

$http->on('request', function ($request, $response) {
    $response->header("Content-Type", "text/plain");
    $response->end("Hello World\n");
});

$http->start();
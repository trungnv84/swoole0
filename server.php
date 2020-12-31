<?php
require 'vendor/autoload.php';
require 'lib/ravendb/session.php';

Co\run(function() {

    go(function() {
        $http = new Swoole\HTTP\Server("0.0.0.0", 38030, SWOOLE_BASE);

        $http->on('start', function ($server) {
            echo "Swoole http server is started at http://127.0.0.1:38030\n";
        });

        $http->on('request', function ($request, $response) {
            $newProduct = null;
            if (@$request['get']['raven']) {
                $raven = new \RavenDB\Session('http://192.168.0.102:28080', 'omgfin-exchange');
                $newProduct = $raven->add('product/', [
                    'name' => 'Window 10 ' . rand(),
                    'price' => '300' . rand(),
                    '@metadata' => [
                        '@collection' => 'Products'
                    ]
                ]);
            }
            $response->header("Content-Type", "text/plain");
            $response->end(var_export($newProduct, true) . "\nHello World\n");
        });

        $http->start();
    });

    //===

    return;
    go(function() {
        $http = new Swoole\HTTP\Server("0.0.0.0", 38031, SWOOLE_PROCESS);

        $http->on('start', function ($server) {
            echo "Swoole http server is started at http://127.0.0.1:38031\n";
        });

        $http->on('request', function ($request, $response) {
            $newProduct = null;
            if (@$request['get']['raven']) {
                $raven = new \RavenDB\Session('http://192.168.0.102:28080', 'omgfin-exchange');
                $newProduct = $raven->add('product/', [
                    'name' => 'Window 10 ' . rand(),
                    'price' => '300' . rand(),
                    '@metadata' => [
                        '@collection' => 'Products'
                    ]
                ]);
            }
            $response->header("Content-Type", "text/plain");
            $response->end(var_export($newProduct, true) . "\nHello World\n");
        });

        $http->start();
    });

    //===

    go(function() {
        $http = new Swoole\HTTP\Server("0.0.0.0", 38032, SWOOLE_BASE);

        $http->on('start', function ($server) {
            echo "Swoole http server is started at http://127.0.0.1:38032\n";
        });

        $http->on('request', function ($request, $response) {
            Co\run(function () use ($request, $response) {
                $newProduct = null;
                if (@$request['get']['raven']) {
                    $raven = new \RavenDB\Session('http://192.168.0.102:28080', 'omgfin-exchange');
                    $newProduct = $raven->add('product/', [
                        'name' => 'Window 10 ' . rand(),
                        'price' => '300' . rand(),
                        '@metadata' => [
                            '@collection' => 'Products'
                        ]
                    ]);
                }
                $response->header("Content-Type", "text/plain");
                $response->end(var_export($newProduct, true) . "\nHello World\n");
            });
        });

        $http->start();
    });

    //===

    go(function() {
        $http = new Swoole\HTTP\Server("0.0.0.0", 38033, SWOOLE_PROCESS);

        $http->on('start', function ($server) {
            echo "Swoole http server is started at http://127.0.0.1:38033\n";
        });

        $http->on('request', function ($request, $response) {
            Co\run(function () use ($request, $response) {
                $newProduct = null;
                if (@$request['get']['raven']) {
                    $raven = new \RavenDB\Session('http://192.168.0.102:28080', 'omgfin-exchange');
                    $newProduct = $raven->add('product/', [
                        'name' => 'Window 10 ' . rand(),
                        'price' => '300' . rand(),
                        '@metadata' => [
                            '@collection' => 'Products'
                        ]
                    ]);
                }
                $response->header("Content-Type", "text/plain");
                $response->end(var_export($newProduct, true) . "\nHello World\n");
            });
        });

        $http->start();
    });

});
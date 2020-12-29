<?php
require 'lib/omg/raven.php';
$pem = file_get_contents('certificate\\omg-v2.Cluster.Settings\\admin.client.certificate.omg-v2.pem');
$raven = new \OMG\RavenDB('https://b.omg-v2.ravendb.community:28080', 'omgfin-exchange', $pem);
var_dump($raven->get('ApTZ8g1PYnTWI0UGq23Ah'));
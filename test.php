<?php
require 'lib/omg/raven.php';
$cert = file_get_contents('certificate\\omg-v2.Cluster.Settings\\admin.client.certificate.omg-v2.crt');
$raven = new \OMG\RavenDB('https://b.omg-v2.ravendb.community:28080', 'omgfin-exchange', $cert);
var_dump($raven->get('ApTZ8g1PYnTWI0UGq23Ah'));
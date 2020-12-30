<?php

namespace RavenDB;

require_once 'base.php';

use \RavenDB\Base as RavenBase;

class Session extends RavenBase
{
    public function __construct($server, $database, $pem = null)
    {
        parent::__construct($server, $database, $pem);
        try {
            chmod(stream_resolve_include_path('lockfile'), 0755);
        } catch (\Exception $e) {

        }
    }

    public function &openSession()
    {
        // check va khoi chay nodejs session ravendb
        return $this;
    }

}
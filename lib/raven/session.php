<?php

namespace RavenDB;

require_once 'base.php';

use RavenDB\Lock as RavenLock;
use RavenDB\Base as RavenBase;

class Session extends RavenBase
{
    private $status = false;
    private $queries = [];

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
        // khoi tao thanh cong
        $this->status = true;
        $this->queries = [];
        return $this;
    }

    public function clear()
    {
        $this->queries = [];
    }

    public function saveChanges()
    {

    }

    public function store(array $entity, string $id_prefix = '', ?string $changeVector = null)
    {
        $t = 1;
        if (array_key_exists('id', $entity)) {
            $id = $entity['id'];
            $old = $this->get($id);
            if ($old) $entity = array_merge((array)$old, $entity);
        } else {
            $nano = new NanoId();
            do {
                $id = $id_prefix . $nano->generateId(21, NanoId::MODE_DYNAMIC);
                $old = $this->get($id);
            } while ($old && $t++ < 10);
        }
        if ($t < 10) {
            //RavenLock::lock(self::LOCK_KEY . $id);
            $this->queries[] = [
                'type' => 'store',
                'entity' => $entity,
                'id' => $id,
                'changeVector' => $changeVector
            ];
            return (object)['Id' => $id, 'ChangeVector' => $changeVector];
        }
    }

    public function update()
    {

    }

    public function load()
    {
        
    }

    public function delete()
    {

    }

}
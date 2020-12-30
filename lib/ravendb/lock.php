<?php
/*
 * Require: symfony/lock
 *
 * https://packagist.org/packages/symfony/lock
 * Install: composer require symfony/lock
 *
 * */

namespace RavenDB;

use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Lock\LockFactory;

class Lock
{
    private static $factory;
    private static $locks = [];

    private static function &_init()
    {
        if (!isset(self::$factory)) {
            $store = new FlockStore(sys_get_temp_dir());
            self::$factory = new LockFactory($store);
        }
        return self::$factory;
    }

    private static function &_lock($key, ?float $ttl = 300.0)
    {
        $key = md5($key);
        if (!array_key_exists($key, self::$locks))
            self::$locks[$key] = self::_init()->createLock($key, $ttl);
        return self::$locks[$key];
    }

    public static function lock($key, ?float $ttl = 300.0)
    {
        $key = md5($key);
        $lock =& self::_lock($key, $ttl);
        if ($lock->acquire()) $acquired = $lock->acquire(true);
        else $acquired = false;
        return $acquired;
    }

    public static function isAcquired($key)
    {
        return self::_lock(md5($key))->acquire();
    }

    public static function isLocked($key)
    {
        return !self::_lock(md5($key))->acquire();
    }

    public static function release($key)
    {
        self::_lock(md5($key))->release();
    }
}
<?php

namespace WPStack_Connect_Vendor\Aws;

use WPStack_Connect_Vendor\Doctrine\Common\Cache\Cache;
class DoctrineCacheAdapter implements \WPStack_Connect_Vendor\Aws\CacheInterface, \WPStack_Connect_Vendor\Doctrine\Common\Cache\Cache
{
    /** @var Cache */
    private $cache;
    public function __construct(\WPStack_Connect_Vendor\Doctrine\Common\Cache\Cache $cache)
    {
        $this->cache = $cache;
    }
    public function get($key)
    {
        return $this->cache->fetch($key);
    }
    public function fetch($key)
    {
        return $this->get($key);
    }
    public function set($key, $value, $ttl = 0)
    {
        return $this->cache->save($key, $value, $ttl);
    }
    public function save($key, $value, $ttl = 0)
    {
        return $this->set($key, $value, $ttl);
    }
    public function remove($key)
    {
        return $this->cache->delete($key);
    }
    public function delete($key)
    {
        return $this->remove($key);
    }
    public function contains($key)
    {
        return $this->cache->contains($key);
    }
    public function getStats()
    {
        return $this->cache->getStats();
    }
}

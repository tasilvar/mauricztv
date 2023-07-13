<?php

namespace bpmj\wpidea\shared\infrastructure\cache;

use Psr\SimpleCache\CacheInterface;

class In_Memory_Cache implements CacheInterface
{
    private array $cache;

    public function __construct()
    {
        $this->cache = [];
    }

    public function get($key, $default = null)
    {
        return $this->cache[$key] ?? $default;
    }

    public function set($key, $value, $ttl = null)
    {
        $this->cache[$key] = $value;
    }

    public function delete($key)
    {
        if (isset($this->cache[$key])) {
            unset($this->cache[$key]);
        }
    }

    public function clear()
    {
        $this->cache = [];
    }

    public function getMultiple($keys, $default = null)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }

        return $result;
    }

    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
    }

    public function has($key)
    {
        return isset($this->cache[$key]);
    }
}
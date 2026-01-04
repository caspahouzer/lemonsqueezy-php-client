<?php

declare(strict_types=1);

namespace LemonSqueezy\Cache;

use Psr\SimpleCache\CacheInterface as PsrCacheInterface;

class FileCache implements CacheInterface
{
    private string $cacheDir;

    public function __construct(string $cacheDir)
    {
        $this->cacheDir = rtrim($cacheDir, '/') . '/';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $file = $this->getCacheFile($key);
        if (!file_exists($file)) {
            return $default;
        }

        $data = unserialize(file_get_contents($file));
        if ($data['ttl'] !== null && $data['time'] + $data['ttl'] < time()) {
            unlink($file);
            return $default;
        }

        return $data['value'];
    }

    public function set(string $key, mixed $value, \DateInterval|int|null $ttl = null): bool
    {
        if ($ttl instanceof \DateInterval) {
            $ttl = (new \DateTime())->add($ttl)->getTimestamp() - time();
        }

        $file = $this->getCacheFile($key);
        $data = [
            'value' => $value,
            'time' => time(),
            'ttl' => $ttl,
        ];

        return (bool) file_put_contents($file, serialize($data));
    }

    public function delete(string $key): bool
    {
        $file = $this->getCacheFile($key);
        if (file_exists($file)) {
            return unlink($file);
        }

        return false;
    }

    public function clear(): bool
    {
        $files = glob($this->cacheDir . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }

        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->get($key, $default);
        }

        return $values;
    }

    public function setMultiple(iterable $values, \DateInterval|int|null $ttl = null): bool
    {
        $success = true;
        foreach ($values as $key => $value) {
            if (!$this->set($key, $value, $ttl)) {
                $success = false;
            }
        }

        return $success;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        $success = true;
        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                $success = false;
            }
        }

        return $success;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    private function getCacheFile(string $key): string
    {
        return $this->cacheDir . sha1($key) . '.cache';
    }
}

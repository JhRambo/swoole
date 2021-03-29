<?php

/**
 * redis连接池，结合channel
 * 可用于并发连接处理
 */
class RedisPool
{
    protected $channel;
    protected static $instance;

    public static function i(): self
    {
        return !empty(static::$instance) ? static::$instance : (static::$instance = new static());
    }

    public function __construct(int $size = 100)
    {
        $this->channel = new Swoole\Coroutine\Channel($size);
        while ($size--) {
            $redis = new Swoole\Coroutine\Redis();
            $res = $redis->connect('127.0.0.1', 6379);
            $res = $redis->auth('123');
            if ($res === true) {
                $this->put($redis);
            } else {
                throw new RuntimeException('cannot connect redis');
            }
        }
    }

    public function put(Swoole\Coroutine\Redis $redis): void
    {
        $this->channel->push($redis);
    }

    public function get(float $timeout = -1): ?Swoole\Coroutine\Redis
    {
        return $this->channel->pop($timeout) ?: null;
    }

    public function close()
    {
        return $this->channel->close();
    }
}

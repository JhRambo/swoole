<?php

/**
 * Redis协程客户端
 * Redis连接池，结合channel
 * 可用于并发连接处理
 */
class RedisPool
{
    protected $channel;
    protected static $instance;

    //单例模式
    public static function getInstance(): self
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

    //连接放回池子里
    public function put(Swoole\Coroutine\Redis $redis): void
    {
        $this->channel->push($redis);
    }

    //$timeout = -1 表示：永不超时
    public function get(float $timeout = -1): ?Swoole\Coroutine\Redis
    {
        return $this->channel->pop($timeout) ?: null;
    }

    public function close()
    {
        return $this->channel->close();
    }
}

<?php

require 'RedisPool.php';

Co\run(function () {
    go(function () {
        redisPool::i();
        for ($c = 1000; $c--;) {
            $pool = RedisPool::i();
            $redis = $pool->get();
            $redis->lpush('redis_pool_list',$c);
            $pool->put($redis); //释放连接，放回连接池
        }
    });
});

// #redis协程客户端
// go(function () {
//     $redis = new Swoole\Coroutine\Redis();
//     $redis->connect('127.0.0.1', 6379);
//     $redis->auth('123');
//     print_r($redis);exit;
//     for($i=1000;$i>0;$i--){
//         $val = $redis->lpush('swoole_list', $i);
//     }
// });

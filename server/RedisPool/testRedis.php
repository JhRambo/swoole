<?php
/*
 * @Author: your name
 * @Date: 2021-04-01 11:02:52
 * @LastEditTime: 2021-04-01 11:30:27
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /swoole/server/RedisPool/testRedis.php
 */

require 'RedisPool.php';

Co\run(function () {
    go(function () {
        for ($c = 100; $c--;) {
            $pool = RedisPool::getInstance();
            $redis = $pool->get();
            $redis->lpush('redis_pool_list', $c); //获取一个redis对象用于实际生产操作，操作完成之后，重新放回连接池
            $pool->put($redis); //释放连接，放回连接池
        }
    });
});
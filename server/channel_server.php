<?php
/*
 * @Descripttion: 
 * 同个进程，不同协程，通道channel演示
 * 类似与redis的push和pop
 * Coroutine\Channel 使用本地内存，不同的进程之间内存是隔离的。只能在同一进程的不同协程内进行 push 和 pop 操作
 * 
 * @version: 
 * @Author: ZQW
 * @Date: 2021-04-24 05:25:18
 * @LastEditTime: 2021-04-24 05:29:24
 */

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use function Swoole\Coroutine\run;
use Swoole\Coroutine\Redis;

run(function () {
    $channel = new Channel(1);
    Coroutine::create(function () use ($channel) {
        for ($i = 0; $i < 10; $i++) {
            Coroutine::sleep(1.0);
            $channel->push(['rand' => rand(1000, 9999), 'index' => $i]);
            echo "{$i}\n";
        }
    });
    //消费者1
    Coroutine::create(function () use ($channel) {
        while (1) {
            Coroutine::sleep(0.1);   //如果这里的协程没有挂起，则第二个协程没有机会执行消费动作
            $data = $channel->pop(2.0);
            if ($data) {
                var_dump($data);
                echo '消费者1' . PHP_EOL;
                //redis协程客户端
                $redis = new Redis();
                $redis->connect('127.0.0.1', 6379);
                $redis->lpush('swoole_list', $data['index']);
            } else {
                assert($channel->errCode === SWOOLE_CHANNEL_TIMEOUT);
                break;
            }
        }
    });
    //消费者2
    Coroutine::create(function () use ($channel) {
        while (1) {
            $data = $channel->pop(2.0);
            if ($data) {
                var_dump($data);
                echo '消费者2' . PHP_EOL;
                //redis协程客户端
                $redis = new Redis();
                $redis->connect('127.0.0.1', 6379);
                $redis->lpush('swoole_list', $data['index']);
            } else {
                assert($channel->errCode === SWOOLE_CHANNEL_TIMEOUT);
                break;
            }
        }
    });
    //保证生产者协程不挂起的前提下，在php的register_shutdown_function()函数中，去实现未完成的消费者功能
    register_shutdown_function(function () use ($channel) {
        go(function () use ($channel) {
            /**
             * stats返回值
             * consumer_num 消费者数量，表示当前通道为空，有 N 个协程正在等待其他协程调用 push 方法生产数据
             * producer_num 生产者数量，表示当前通道已满，有 N 个协程正在等待其他协程调用 pop 方法消费数据
             * queue_num 通道中的元素数量
             */
            $queue_num = $channel->stats()["queue_num"];
            for ($i = 0; $i < $queue_num; $i++) {
                var_dump($channel->pop());
            }
        });
    });
});

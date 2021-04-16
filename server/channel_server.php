<?php
/*
 * @Author: your name
 * @Date: 2020-09-10 18:39:56
 * @LastEditTime: 2021-04-16 10:59:33
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /swoole/server/channel_server.php
 */
/**
 * 同个进程，不同协程，通道channel演示
 * 类似与redis的push和pop
 * Coroutine\Channel 使用本地内存，不同的进程之间内存是隔离的。只能在同一进程的不同协程内进行 push 和 pop 操作
 */
Co\run(function () {
    $chan = new Swoole\Coroutine\Channel(2);
    //生产者
    Swoole\Coroutine::create(function () use ($chan) {
        for ($i = 0; $i < 10; $i++) {
            $chan->push(['rand' => rand(1000, 9999), 'index' => $i]);
        }
    });

    //消费者1
    // co::sleep(3);
    Swoole\Coroutine::create(function () use ($chan) {
        co::sleep(0.000000001);   //如果这里的协程没有挂起，则第二个协程没有机会执行消费动作
        $data = $chan->pop();   //先进先出，类似队列
        while (!empty($data)) {
            echo '消费者1'.PHP_EOL;
            //redis协程客户端
            $redis = new Swoole\Coroutine\Redis();
            $redis->connect('127.0.0.1', 63799);
            $val = $redis->lpush('swoole_list', $data['index']);
        }
    });
    //消费者2
    Swoole\Coroutine::create(function () use ($chan) {
        $data = $chan->pop();   //先进先出，类似队列
        while (!empty($data)) {
            echo '消费者2'.PHP_EOL;
            var_dump($data);
        }
    });
});

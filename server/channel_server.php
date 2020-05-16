<?php
/**
 * 协程channel演示
 * 类似与redis的push和pop
 * Coroutine\Channel 使用本地内存，不同的进程之间内存是隔离的。只能在同一进程的不同协程内进行 push 和 pop 操作
 */
Co\run(function(){
    $chan = new Swoole\Coroutine\Channel(2);
    // print_r($chan->stats());

    //生产者
    Swoole\Coroutine::create(function () use ($chan) {
        for($i = 0; $i < 10000; $i++) {
            $chan->push(['rand' => rand(1000, 9999), 'index' => $i]);
        }
    });
    // print_r($chan->stats());

    //消费者
    // co::sleep(3);
    Swoole\Coroutine::create(function () use ($chan) {
        co::sleep(0.000000001);   //如果这里的协程没有挂起，则第二个协程没有机会执行消费动作
        while(1) {
            echo '消费者1'.PHP_EOL;
            $data = $chan->pop();   //先进先出，类似队列
            //redis协程客户端
            $redis = new Swoole\Coroutine\Redis();
            $redis->connect('127.0.0.1', 6379);
            $redis->auth('123');
            $val = $redis->lpush('swoole_list', $data['index']);
            // var_dump($data);
        }
    });
    Swoole\Coroutine::create(function () use ($chan) {
        // co::sleep(4);
        while(1) {
            echo '消费者2'.PHP_EOL;
            $data = $chan->pop();   //先进先出，类似队列
            var_dump($data);
        }
    });

    // print_r($chan->stats());

    // $chan->close();
    // Swoole\Coroutine::create(function () use ($chan) {
    //     while(1) {
    //         $data = $chan->pop();
    //         var_dump($data);
    //     }
    // });
});
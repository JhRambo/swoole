<?php
/**
 * 协程channel演示
 * 类似与redis的push和pop
 * Coroutine\Channel 使用本地内存，不同的进程之间内存是隔离的。只能在同一进程的不同协程内进行 push 和 pop 操作
 */
Co\run(function(){
    $chan = new Swoole\Coroutine\Channel(2);
    // var_dump($chan);
    Swoole\Coroutine::create(function () use ($chan) {
        for($i = 0; $i < 10; $i++) {
            co::sleep(1.0);
            $chan->push(['rand' => rand(1000, 9999), 'index' => $i]);
            echo "$i\n";
        }
    });
    Swoole\Coroutine::create(function () use ($chan) {
        while(1) {
            $data = $chan->pop();
            var_dump($data);
        }
    });
    // $chan->close();
    // Swoole\Coroutine::create(function () use ($chan) {
    //     while(1) {
    //         $data = $chan->pop();
    //         var_dump($data);
    //     }
    // });
});
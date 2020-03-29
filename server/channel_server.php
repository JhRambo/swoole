<?php
/**
 * 协程channel演示
 * 类似与redis的push和pop
 */
Co\run(function(){
    $chan = new Swoole\Coroutine\Channel(2);
    print_r($chan);
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
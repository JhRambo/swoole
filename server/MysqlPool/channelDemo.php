<?php
/**
 * 协程channelDemo
 * @author tanjiajun
 */

use \Swoole\Coroutine\Channel;

$chan = new Channel();
$time = 10;
go(function () use ($chan, $time) {
    echo "我是第一个协程，等待".$time."秒内有push就执行返回" . PHP_EOL;
    $p = $chan->pop(1);
    echo "pop返回结果" . PHP_EOL;
    var_dump($p);
});
go(function () use ($chan,$time) {
    co::sleep($time);
    $chan->push(2);
});
echo "main" . PHP_EOL;
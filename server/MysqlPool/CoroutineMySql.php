<?php

/**
 * 协程Mysql客户端Demo
 * @author tanjiajun
 */
go(function () {
    $start = microtime(true);
    $db = new Swoole\Coroutine\MySQL();
    $db->connect([
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => '123456',
        'database' => 'test',
        'timeout' => 6
    ]);
    $db->query("select sleep(5)");
    echo "我是第一个sleep五秒之后\n";
    $ret = $db->query("select * from book");
    var_dump($ret);
    $use = microtime(true) - $start;
    echo "协程mysql输出用时：" . $use . PHP_EOL;
});

go(function () {
    
    echo '1';
});

go(function () {
    sleep(8);
    echo '2';
});
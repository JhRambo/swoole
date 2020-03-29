<?php

/**
 * 协程Mysql客户端Demo
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
    co::sleep(3);
    // $db->query("select sleep(3)");
    $ret = $db->query("select * from user limit 1");
    var_dump($ret);
    $use = microtime(true) - $start;
    echo "协程mysql输出用时：" . $use . PHP_EOL;
    // co::sleep(3);   //注意与sleep(3)的区别，sleep(3)会阻塞
    // echo '3';
});

go(function () {
    echo '1';
});

go(function () {
    co::sleep(5);
    echo '2';
});
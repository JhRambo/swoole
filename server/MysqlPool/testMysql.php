<?php
/**
 * 数据库连接池协程方式
 * 协程的客户端内执行其实是同步的，不要理解为异步，它只是遇到IO阻塞时能让出执行权，切换到其他协程而已，不能和异步混淆
 */
require "MysqlPool.php";

$httpServer = new swoole_http_server('127.0.0.1', 9501);
$httpServer->set(['work_num' => 1]);
$httpServer->on('WorkerStart', function ($request, $response) {
    MysqlPool::getInstance()->init()->recycleFreeConnection();
});
$httpServer->on('Request', function ($request, $response) {
    $conn = MysqlPool::getInstance()->getConn();
    $conn->query('SELECT * FROM user WHERE id=1');
    MysqlPool::getInstance()->recycle($conn);
});
$httpServer->start();


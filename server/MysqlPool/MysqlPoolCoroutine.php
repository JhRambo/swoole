<?php

/**
 * 数据库连接池协程方式
 * @author tanjiajun 
 * Date: 2018/9/8
 * Time: 11:30
 * 协程的客户端内执行其实是同步的，不要理解为异步，它只是遇到IO阻塞时能让出执行权，切换到其他协程而已，不能和异步混淆
 */
require "AbstractPool.php";

class MysqlPoolCoroutine extends AbstractPool
{
    public static $instance;

    /**
     * 单例模式用于实例化对象
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new MysqlPoolCoroutine();
        }
        return self::$instance;
    }

    protected function createDb()
    {
        $db = new Swoole\Coroutine\Mysql();
        $db->connect(
            $this->dbConfig
        );
        return $db;
    }
}

//http协议
$httpServer = new swoole_http_server('0.0.0.0', 9501);
$httpServer->set(
    ['worker_num' => 1]
);
$httpServer->on("WorkerStart", function () {
    MysqlPoolCoroutine::getInstance()->init();
});

$httpServer->on("request", function ($request, $response) {
    $db = null;
    $obj = MysqlPoolCoroutine::getInstance()->getConnection();
    if (!empty($obj)) {
        $db = $obj ? $obj['db'] : null;
    }
    if ($db) {
        $db->query("select sleep(2)");
        $ret = $db->query("select * from book");
        MysqlPoolCoroutine::getInstance()->free($obj);
        $response->end(json_encode($ret, true));
    }
});
$httpServer->start();

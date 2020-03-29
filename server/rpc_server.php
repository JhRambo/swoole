<?php

/**
 * RPC服务端
 * TCP协议
 * 结合进程构造了一个异步PRC服务器
 */

use Swoole\Process;

echo '当前主进程ID：' . getmypid() . PHP_EOL;
cli_set_process_title('mymain');  //设置主进程名

//自 PHP 5.3.0 起可以使用一个匿名函数，类自动加载
spl_autoload_register(function ($classname) {
    require_once("./{$classname}.php");
});

$process1 = new Process(function () {
    $serv = new Swoole\Server('127.0.0.1', 9501);
    $serv->set(array('worker_num' => 2));
    $serv->on('start', function () {
        echo '当前子进程ID：' . getmypid() . PHP_EOL;
        cli_set_process_title('mymaster');  //设置子进程名
    });
    $serv->on('managerstart', function () {
        echo '当前子进程ID：' . getmypid() . PHP_EOL;
        cli_set_process_title('mymanager');  //设置子进程名
    });
    $serv->on('workerstart', function () {
        echo '当前子进程ID：' . getmypid() . PHP_EOL;
        cli_set_process_title('myworker');  //设置子进程名
    });
    $serv->on("receive", function ($serv, $fd, $from_id, $data) {
        //解析客户端协议
        $info = json_decode($data, true);   //json传输协议
        $classname = $info['service'];
        $action = $info['action'];
        $param = $info['param'];

        //调用一个类
        $classobj = new $classname;
        $result = $classobj->$action($param);
        $serv->send($fd, json_encode(['data' => $result]));
    });
    $serv->start();
});

$process1->start();

//回收子进程，拦截SIGCHLD信号进行处理
Process::signal(SIGCHLD, function($sig){
    //必须非阻塞
    while ($ret = Process::wait(false)){
        // echo '进程ID：'.$ret['pid'];
    }   
});
while(1){
    sleep(1);
}




<?php
/*
 * @Author: your name
 * @Date: 2020-08-27 15:08:04
 * @LastEditTime: 2020-09-22 16:10:47
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /swoole/server/time_server.php
 */

    $serv = new Swoole\Server("0.0.0.0", 9511);

    $serv->set(array(
        'worker_num' => 1,    //worker process num
    ));

    $serv->on('connect', function ($server, $fd){
        echo "客户端 ".$fd."连接成功 \n";
    });
    $serv->on('receive', function ($server, $fd, $reactor_id, $data) {
        echo "客户端 ".$fd."发来消息：".$data."\n";
        $server->send($fd, 'Swoole已经接受到您发送的消息: '.$data);
    });
    $serv->on('close', function ($server, $fd) {
        echo "客户端 {$fd}关闭连接\n";
    });

    $serv->on('WorkerStart', function ($server, $worker_id){
        $server->tick(5000, function(){
            echo "执行定时器任务 ".time()." \n";
        });
        // Swoole\Timer::clearAll();   //清除定时任务
    });
    $serv->start();
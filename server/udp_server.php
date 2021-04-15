<?php
/*
 * @Author: your name
 * @Date: 2020-09-22 15:02:06
 * @LastEditTime: 2020-09-22 15:02:06
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: /swoole/server/udp_server.php
 */

//创建Server对象，监听 127.0.0.1:9501端口，类型为SWOOLE_SOCK_UDP
$serv = new Swoole\Server("127.0.0.1", 9501, SWOOLE_PROCESS, SWOOLE_SOCK_UDP); 

//监听数据接收事件
$serv->on('Packet', function ($serv, $data, $clientInfo) {
    $serv->sendto($clientInfo['address'], $clientInfo['port'], "Server ".$data);
    var_dump($clientInfo);
});

//启动服务器
$serv->start(); 
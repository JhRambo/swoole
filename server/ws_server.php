<?php
/*
 * @Author: your name
 * @Date: 2021-03-29 14:57:01
 * @LastEditTime: 2021-03-29 14:57:02
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: /swoole/server/ws_server.php
 */

    /**
     * swoole websocket实现简单的聊天室
     * 异步websocket服务器
     */
    $server = new Swoole\WebSocket\Server("0.0.0.0", 9501);

    $server->on('open', function (Swoole\WebSocket\Server $server, $request) {
        echo "server: handshake success with fd{$request->fd}\n";
    });

    $server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        foreach ($server->connections as $k => $v) {
            print_r($v);
            $server->push($v, "会员" . $frame->fd . ":" . $frame->data);    //推送到所有在线的用户，类似与群聊
        }
    });

    $server->on('close', function ($ser, $fd) {
        echo "client {$fd} closed\n";
    });

    $server->start();

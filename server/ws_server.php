<?php
/*
 * @Author: your name
 * @Date: 2020-08-13 10:02:18
 * @LastEditTime: 2021-04-07 10:01:34
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /swoole/server/ws_server.php
 */

    /**
     * swoole websocket实现简单的聊天室
     * 异步websocket服务器
     */
    $server = new Swoole\WebSocket\Server("0.0.0.0", 9511);

    /**
     * 连上服务器时，默认触发
     */
    $server->on('open', function ($server, $request) {
        foreach ($server->connections as $k => $v) {
            $server->push($v, "我是会员" . $request->fd . '，I AM COMING');    //推送到所有在线的用户，类似与群聊
        }
    });

    /**
     * 服务端接收客户端发送的数据，并发送消息给客户端
     */
    $server->on('message', function ($server, $frame) {
        foreach ($server->connections as $k => $v) {
            $server->push($v, "我是会员" . $frame->fd . "：" . $frame->data);    //推送到所有在线的用户，类似与群聊
        }
    });

    /**
     * 关闭
     */
    $server->on('close', function ($server, $fd) {
        foreach ($server->connections as $k => $v) {
            $server->push($v, "会员" . $fd . '已退出');    //推送到所有在线的用户，类似与群聊
        }
        echo "client {$fd} closed\n";
    });
    
    /**
     * 启动
     */
    $server->start();

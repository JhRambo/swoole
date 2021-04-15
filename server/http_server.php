<?php
/*
 * @Author: your name
 * @Date: 2020-08-27 16:15:48
 * @LastEditTime: 2021-04-02 10:09:58
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /swoole/server/http_server.php
 */

/**
 * 异步http服务器
 */

$http = new Swoole\Http\Server("0.0.0.0", 9501);

//启动服务时触发
$http->on("start", function ($server) {
    echo "Swoole http server is started at http://127.0.0.1:9501\n";
});

//监听连接关闭事件，无状态连接，响应完成就会断开
$http->on('close', function ($server, $fd) {
    echo "Client: Close.".$fd."\n";
});

//客户端连上服务端时触发
$http->on('request', function($request, $response){
    $db = new Swoole\Coroutine\MySQL();  //回调函数中使用MySql协程客户端
    $db->connect([
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'user'     => 'root',
        'password' => '123456',
        'database' => 'test',
    ]);
    $res = $db->query('select * from tp_book limit 2');

    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
        $response->status(404);
        $response->end(); //writed() 的区别，write，header, Transfer-Encoding: chunked
        return;
    }

    $response->header("Content-Type", "text/html; charset=utf-8");
    //返回给客户端
    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
    // $response->write("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});

$http->start();

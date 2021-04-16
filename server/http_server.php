<?php
/*
 * @Author: your name
 * @Date: 2020-08-27 16:15:48
 * @LastEditTime: 2021-04-16 10:38:29
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /swoole/server/http_server.php
 */

/**
 * 异步http服务器
 */
/**
 * SWOOLE_PROCESS(默认模式)
 * 说明：
 * 当前模式下，具有完整的master,manager,worker进程
 * 当前master进程ID：18146
 * 当前manager进程ID：18151
 * 当前woker进程ID：18153
 * root     18146  7779  0 10:34 pts/8    00:00:00 mymaster
 * root     18151 18146  0 10:34 pts/8    00:00:00 mymanager
 * root     18153 18151  0 10:34 pts/8    00:00:00 myworker
 * 
 * 
 * SWOOLE_BASE
 * 说明1：
 * 当work_num=1时，没有manager进程，worker与master进程是同一个进程
 * 当前master进程ID：18522
 * 当前woker进程ID：18522
 * root     18522  7779  0 10:36 pts/8    00:00:00 myworker
 * 
 * 说明2：
 * 当work_num>1时，master与manager是同一个进程，这时多了个manager进程用于管理worker进程
 * 当前master进程ID：18690
 * 当前manager进程ID：18690
 * 当前woker进程ID：18694
 * 当前woker进程ID：18695
 * root     18690  7779  0 10:37 pts/8    00:00:00 mymanager
 * root     18694 18690  0 10:37 pts/8    00:00:00 myworker
 * root     18695 18690  0 10:37 pts/8    00:00:00 myworker
 * 
 */
$http = new Swoole\Http\Server("0.0.0.0", 9501, SWOOLE_BASE);
$http->set([
    'worker_num' => 2,
]);
//启动服务时触发
$http->on("start", function ($server) {
    //这里才是端口9501对应的进程ID
    echo '当前master进程ID：'.getmypid().PHP_EOL;
    cli_set_process_title('mymaster');  //设置子进程名
    echo "Swoole http server is started at http://127.0.0.1:9501\n";
});
$http->on('managerstart', function(){
    echo '当前manager进程ID：'.getmypid().PHP_EOL;
    cli_set_process_title('mymanager');  //设置子进程名
});
$http->on('workerstart', function(){
    echo '当前woker进程ID：'.getmypid().PHP_EOL;
    cli_set_process_title('myworker');  //设置子进程名
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

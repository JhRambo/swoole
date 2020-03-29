<?php

/**
 * tcp keepalive 与 swoole心跳包的区别
 * 通过tcp_keepalive的方式实现心跳的功能，优点是简单，不要写代码就可以完成这个功能，
 * 并且发送的心跳包小。缺点是依赖于系统的网络环境，必须保证服务器和客户端都实现了这样的功能，需要客户端配合发心跳包。
 * 还有一个更为严重的缺点是如果客户端和服务器不是直连的，而是通过代理来进行连接的，
 * 例如socks5代理，它只会转发应用层的包，不会转发更为底层的tcp探测包，那这个心跳功能就失效了。
 * 所以，Swoole就提供了其他的解决方案，一组检测死连接的配置。
 * 
 * heartbeat和tcp keepalive还是有一定的区别的，tcp keepalive有保活连接的功能，
 * 但是heartbeat纯粹是检测如果没有数据的连接，然后关闭它，并且只可以在服务端这边配置，如果需要保活，也可以让客户端配合发送心跳。
 */

/**
 * 异步风格TCP
 */
// $server = new Swoole\Server('127.0.0.1', 9501);

// $server->set([
//     'worker_num' => 1,
//     'open_tcp_keepalive' => 1, // 开启tcp_keepalive
//     'tcp_keepidle' => 4, // 4s没有数据传输就进行检测
//     'tcp_keepinterval' => 1, // 1s探测一次
//     'tcp_keepcount' => 5, // 探测的次数，超过5次后还没有回包close此连接
//     //======注意这里不会close掉，因为操作系统底层会自动的给客户端回ack，
//     //所以这个连接不会在5次探测后被关闭。操作系统底层会持续不断的发送这样的一组包
//     //可以使用tcpdump 抓包工具测试 tcpdump -i lo port 9501
// ]);

// $server->set([
//     'worker_num' => 1,
//     'heartbeat_check_interval' => 1, // 1s探测一次
//     'heartbeat_idle_time' => 5, // 5s未发送数据包就close此连接======会close掉
// ]);

// $server->on('connect', function ($server, $fd) {
//     var_dump("Client: Connect $fd");
// });

// $server->on('receive', function ($server, $fd, $reactor_id, $data) {
//     var_dump($data);
//     $server->send($fd, '服务端收到数据');
// });

// $server->on('close', function ($server, $fd) {
//     var_dump("close fd $fd");
// });

// $server->start();

/**
 * 协程风格TCP
 */
//协程容器
Co\run(function () {
    $server = new Swoole\Coroutine\Server('127.0.0.1', 9501);
    $server->handle(function (Swoole\Coroutine\Server\Connection $conn) {
        //接收数据
        $data = $conn->recv();
        if (empty($data)) {
            //关闭连接
            $conn->close();
        }
        var_dump($data);
        //发送数据
        $conn->send("服务端收到数据");
    });
    //开始监听端口
    $server->start();
});

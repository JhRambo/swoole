<?php

/**
 * 协程tcp客户端
 */
Co\run(function () {
    go(function(){
        $client = new Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
        if (!$client->connect('127.0.0.1', 9511, 0.5)) {
            echo "connect failed. Error: {$client->errCode}\n";
            //重新连接
            //关闭已有socket
            $client->close();
            //重试
            $client->connect('127.0.0.1', 9501);
        }
        co::sleep(3);   //遇到阻塞，优先执行第二个go
        if(!$client->send("hello tcp")){
            echo "发送失败，错误代码：".$client->errCode;
        }
        echo $client->recv();   //接受服务器返回的数据
        $client->close();
    });

    go(function(){
        echo '111'.PHP_EOL;
    });
    
});

/**
 * 同步tcp客户端
 */
// $client = new Swoole\Client(SWOOLE_SOCK_TCP);
// if (!$client->connect('127.0.0.1', 9501, -1)) {
//     exit("connect failed. Error: {$client->errCode}\n");
// }
// $client->send("hello tcp");
// echo $client->recv();
// $client->close();
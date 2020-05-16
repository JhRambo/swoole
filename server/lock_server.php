<?php
// //创建锁对象
// $lock = new swoole_lock(SWOOLE_MUTEX); //互斥锁
// echo "创建互斥锁\n";

// //开始锁定 主进程
// $lock->lock();

// $pid = pcntl_fork();

// if ($pid > 0) {
//     cli_set_process_title('mymain');  //设置进程名
//     //解锁
//     $lock->unlock();
//     //回收子进程
//     pcntl_signal(SIGCHLD,SIG_IGN);
//     while (1) {//主进程一直不退出
//         sleep(1);
//     }

// } elseif ($pid == 0) {
//     cli_set_process_title('mychild');  //设置进程名
//     echo "子进程 等到锁\n";
//     //上锁
//     $lock->lock();
//     echo "子进程 获取锁\n";
//     sleep(3);
//     //释放锁
//     $lock->unlock();
//     echo "子进程退出\n";
// } else {
//     echo "我是主进程，我慌得一批，开启子进程失败了\n";
// }

//演示文件锁
$serv = new \Swoole\Server('127.0.0.1', 9501);

$serv->on('connect', function ($serv, $fd) {
    var_dump("Client: Connect $fd");
});

//监听数据发送事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "ServerEnd\n");    //向客户端发送
});

$serv->on('close', function ($serv, $fd) {
    var_dump("close fd $fd");
});

$serv->start();


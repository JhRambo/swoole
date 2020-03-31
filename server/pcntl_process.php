<?php

$num = 1;
$str = "EasySwoole,Easy学swoole\n";
$pid = pcntl_fork();    //fork子进程
if ($pid > 0) {//主进程代码
    cli_set_process_title('mymain');  //设置进程名
    echo "我是主进程，我的进程ID：".getmypid()."\n";
    pcntl_async_signals(true);
    //1回收子进程
    pcntl_signal(SIGCHLD, SIG_IGN);
    //2回收子进程
    // pcntl_signal(SIGCHLD, function () {
    //     echo '子进程退出了,请及时处理' . PHP_EOL;
    //     pcntl_wait($status,WNOHANG);
    // });
    while (1) {//主进程一直不退出
        sleep(1);
    }

} elseif ($pid == 0) {
    cli_set_process_title('mychild');  //设置进程名
    echo "我是子进程，我的进程ID：" . getmypid() . "\n";
} else {
    echo "我是主进程，我慌得一批，开启子进程失败了\n";
}
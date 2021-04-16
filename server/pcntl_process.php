<?php
/*
 * @Author: your name
 * @Date: 2021-04-16 10:14:27
 * @LastEditTime: 2021-04-16 10:14:45
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /swoole/server/pcntl_process.php
 */
// $pid = pcntl_fork();    //fork子进程
// if ($pid > 0) {//主进程代码
//     cli_set_process_title('mymain');  //设置进程名
//     echo "我是主进程，我的进程ID：".getmypid()."\n";
//     pcntl_async_signals(true);
//     //1回收子进程
//     pcntl_signal(SIGCHLD, SIG_IGN);
//     //2回收子进程
//     // pcntl_signal(SIGCHLD, function () {
//     //     echo '子进程退出了,请及时处理' . PHP_EOL;
//     //     pcntl_wait($status,WNOHANG);
//     // });
//     while (1) {//主进程一直不退出
//         sleep(1);
//     }

// } elseif ($pid == 0) {
//     cli_set_process_title('mychild');  //设置进程名
//     echo "我是子进程，我的进程ID：" . getmypid() . "\n";
// } else {
//     echo "我是主进程，我慌得一批，开启子进程失败了\n";
// }

$pid = pcntl_fork();
//父进程和子进程都会执行下面代码
if ($pid == -1) {
    //错误处理：创建子进程失败时返回-1.
    die('could not fork');
} elseif ($pid) {
    //父进程会得到子进程号，所以这里是父进程执行的逻辑
     pcntl_wait($status); //等待子进程中断，防止子进程成为僵尸进程。
} else {
    //子进程得到的$pid为0, 所以这里是子进程执行的逻辑。
}

<?php
/*
 * @Author: your name
 * @Date: 2021-04-16 10:59:54
 * @LastEditTime: 2021-04-16 11:00:01
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /swoole/server/child2.php
 */

echo '当前child2子进程ID：'.getmypid().PHP_EOL;
cli_set_process_title('mychild2');  //设置子进程名
//保证不退出，防止出现僵尸进程
while (1) {
    echo getmypid().PHP_EOL;
    sleep(5);
}

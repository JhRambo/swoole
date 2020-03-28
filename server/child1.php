<?php
$str = '';
echo $str .= '当前child1子进程ID：'.getmypid().PHP_EOL;
cli_set_process_title('mychild1');  //设置子进程名
//保证不退出，防止出现僵尸进程
while(1){
    echo getmypid().PHP_EOL;
    sleep(5);
}

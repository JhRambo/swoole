<?php
/*
 * @Author: your name
 * @Date: 2021-03-30 13:35:56
 * @LastEditTime: 2021-03-30 13:48:06
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /swoole/server/table_server.php
 */

/**
 * 解决进程之间不能通信的问题
 * swoole_table 结合read，write函数实现数据共享，纯内存操作
 */
$table = new swoole_table(1024);

$table->column('id', $table::TYPE_INT, 4);
$table->column('name', $table::TYPE_STRING, 64);
$table->column('age', $table::TYPE_INT, 1);
$table->create();

$table->set('swoole',['id'=>1,'name'=>'test','age'=>18]);  //key=>value;

$data = $table->get('swoole');

use Swoole\Process;
echo '当前进程ID：'.getmypid().PHP_EOL;
cli_set_process_title('mymain');  //设置进程名

//创建子进程1
$process1 = new Process(function (Process $p1) use($data) {
    cli_set_process_title('mychild1');  //设置子进程名
    while(1){
        $p1->write(json_encode($data,true));   //往主进程中写入数据
        sleep(3);
    }
},false,1,true);
$process1->start();

//创建子进程2
$process2 = new Process(function (Process $p2) {
    cli_set_process_title('mychild2');  //设置子进程名
    while(1){
        $data_a = $p2->read();    //读取主进程向子进程2写入的数据
        if($data_a){
            echo '进程1推送数据到进程2成功，值为：'.$data_a.PHP_EOL;
        }
        sleep(1);
    }
});
$process2->start();

//主进程
while(1){
    $data_a = $process1->read();  //读取子进程1向主进程写入的数据
    if($data_a){
        $process2->write($data_a);    //往子进程2写入数据
    }
}

// 回收子进程
Process::signal(SIGCHLD, function($sig){
    //必须非阻塞
    while ($ret = Process::wait(false)){
    }   
});

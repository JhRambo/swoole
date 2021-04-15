<?php
/*
 * @Author: your name
 * @Date: 2020-08-27 15:08:04
 * @LastEditTime: 2021-04-13 16:14:08
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /swoole/server/time_server.php
 */

$serv = new Swoole\Server("0.0.0.0", 9501);

// $serv->set(array(
//     'worker_num' => 1,    //worker process num
// ));

// $serv->on('connect', function ($server, $fd){
//     echo "客户端 ".$fd."连接成功 \n";
// });
// $serv->on('receive', function ($server, $fd, $reactor_id, $data) {
//     echo "客户端 ".$fd."发来消息：".$data."\n";
//     $server->send($fd, 'Swoole已经接受到您发送的消息: '.$data);
// });
// $serv->on('close', function ($server, $fd) {
//     echo "客户端 {$fd}关闭连接\n";
// });

// $serv->on('WorkerStart', function ($server, $worker_id){
//     $server->tick(5000, function(){
//         echo "执行定时器任务 ".time()." \n";
//     });
//     // Swoole\Timer::clearAll();   //清除定时任务
// });
// $serv->start();

const N = 100000;

function test()
{
    global $timers;
    shuffle($timers);
    $stime = microtime(true);
    foreach ($timers as $id) {
        swoole_timer_clear($id);
    }
    $etime = microtime(true);
    echo "del " . N . " timer :" . ($etime - $stime) . "s\n";
}

$timers = [];
$stime = microtime(true);
for ($i = 0; $i < N; $i++) {
    $timers[] = swoole_timer_after(rand(1, 9999999), 'test');
}
$etime = microtime(true);
echo "add " . N . " timer :" . ($etime - $stime) . "s\n";
swoole_event_wait();    //启动事件监听

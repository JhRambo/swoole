<?php
/*
 * @Author: your name
 * @Date: 2020-08-27 19:52:21
 * @LastEditTime: 2020-09-22 15:02:18
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /swoole/server/task_server.php
 */

$serv = new Swoole\Server("127.0.0.1", 9501);

//设置异步任务的工作进程数量
$serv->set([
    'task_worker_num' => 3,
    'task_enable_coroutine' => true,
]);

cli_set_process_title('mymain');  //设置进程名
echo '当前进程ID：'.getmypid().PHP_EOL;

$serv->on('start', function(){
    echo '当前子进程ID：'.getmypid().PHP_EOL;
    cli_set_process_title('mymaster');  //设置子进程名
});
$serv->on('managerstart', function(){
    echo '当前子进程ID：'.getmypid().PHP_EOL;
    cli_set_process_title('mymanager');  //设置子进程名
});
$serv->on('workerstart', function(){
    echo '当前work子进程ID：'.getmypid().PHP_EOL;
    cli_set_process_title('myworker');  //设置子进程名
});

//此回调函数在worker进程中执行
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    //投递异步任务
    $task_id = $serv->task($data);
    echo "Dispatch AsyncTask: id=$task_id\ndata=$data";
});

//处理异步任务(此回调函数在task进程中执行)
$serv->on('task', function ($serv, Swoole\Server\Task $task) {
    print_r($task);
    // //来自哪个`Worker`进程
    // $task->worker_id;
    // //任务的编号
    // $task->id;
    // //任务的类型，taskwait, task, taskCo, taskWaitMulti 可能使用不同的 flags
    // $task->flags;
    // //任务的数据
    // $task->data;
    //协程 API
    
    // go(function () {
    //     $redis = new Swoole\Coroutine\Redis();  //redis协程客户端
    //     $redis->connect('127.0.0.1', 6379);
    //     $redis->auth('123');
    //     $redis->setOptions(['compatibility_mode' => true]);   //重要，开启后，支持协程中使用php redis操作　　　
    //     $redis->hmset('sanguo',['name'=>'liubei','age'=>20]);
    //     var_dump($redis->hgetall('sanguo'));
    // });
    //完成任务，结束并返回数据
    $task->finish([$task->worker_id, 'hello']);
});

//处理异步任务的结果(此回调函数在worker进程中执行)
$serv->on('finish', function ($serv, $task_id, $data) {
    echo "AsyncTask[$task_id] Finish: ".json_encode($data) . PHP_EOL;
});

$serv->start();
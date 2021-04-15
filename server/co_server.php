<?php

/**
 * 协程的客户端内执行其实是同步的，不要理解为异步，它只是遇到IO阻塞时能让出执行权，切换到其他协程而已，不能和异步混淆。
 * 协程如果没有被阻塞的话，会按顺序执行
 */

#demo1
// use Swoole\Process;

// cli_set_process_title('mymain');    //设置进程名称
// echo '主进程ID：'.getmypid().PHP_EOL;

// //子进程中开启协程案例
// $process1 = new Process(function(){
//     cli_set_process_title('mychild1');
//     echo '子进程ID：'.getmypid().PHP_EOL;
//     Co\run(function () {
//         //go开启一个协程
//         go(function () {
//             co::sleep(.2);  //协程挂起
//             echo 'co_1'.PHP_EOL;
//             $start = microtime(true);
//             $db = new Swoole\Coroutine\MySQL();
//             $db->connect([
//                 'host' => '127.0.0.1',
//                 'port' => 3306,
//                 'user' => 'root',
//                 'password' => '123456',
//                 'database' => 'test',
//                 'timeout' => 6
//             ]);
//             $ret = $db->query("select * from user limit 1");
//             print_r($ret);
//             $use = microtime(true) - $start;
//             echo "协程mysql输出用时：" . $use . PHP_EOL;
//         });
//         //非阻塞协程执行
//         go(function(){
//             echo 'co_2'.PHP_EOL;
//         });
//     });
// });
// $process1->start();
// //保持主进程一直在线，避免主进程挂掉，子进程被init进程接管
// while(1){
//     sleep(1);
// }

#demo2
// //redis协程客户端
// go(function () {
//     $redis = new Swoole\Coroutine\Redis();
//     $redis->connect('127.0.0.1', 6379);
//     $redis->auth('123');
//     $redis->setOptions(['serialize'=>true]);    //开启序列化
//     for ($i = 1000; $i > 0; $i--) {
//         $val = $redis->lpush('swoole_list', $i);
//     }
// });
// //异步消费
// go(function () {
//     $redis = new Swoole\Coroutine\Redis();
//     $redis->connect('127.0.0.1', 6379);
//     $redis->auth('123');
//     for ($i = 998; $i > 0; $i--) {
//         $val = $redis->rpop('swoole_list');  //反序列化输出
//         var_dump($val);
//     }
// });

#demo3
// //协程，同时进行
// Co\run(function(){
//     go(function(){
//         echo '111'.PHP_EOL;
//         co::sleep(4);   //立即返回，但是进程还是需要4秒才终止
//         echo 'ooo'.PHP_EOL;
//     });

//     go(function(){
//         echo '222'.PHP_EOL;
//         co::sleep(2);   //立即返回，但是进程还是需要2秒才终止
//         echo 'ttt'.PHP_EOL;
//     });

//     go(function(){
//         echo '333'.PHP_EOL;
//         co::sleep(5);   //立即返回，但是进程还是需要5秒才终止
//         echo 'xxx'.PHP_EOL;
//     });

//     go(function(){
//         echo '444'.PHP_EOL;
//         co::sleep(4);   //立即返回，但是进程还是需要4秒才终止
//         echo 'yyy'.PHP_EOL;
//     });

//     echo 'www'.PHP_EOL; //到这里还是立即输出。后面的有sleep的间隔输出
// });

// echo 'aaa'.PHP_EOL; //最后输出

// 111 222 333 444 www ttt yyy ooo xxx aaa
//由此可见：当有协程容器的时候，协程容器里的先执行，协程容器外的最后执行
//没有协程容器的时候，顺序执行，遇到sleep也可以立即返回
//sleep相同值时，倒序输出

#demo4
// go(function(){
//     $myredis = new Swoole\Coroutine\Redis();
//     $myredis->connect('127.0.0.1',6379);
//     $myredis->auth('123');
//     $myredis->setOptions(['compatibility_mode' => true]);   //重要，开启后，支持协程中使用php redis操作
//     //所谓php redis操作就是我们耳熟能详的hmGet/hGetAll/zRange/zRevRange/zRangeByScore/zRevRangeByScore各类方法　　　　
//     $myredis->hmset('sanguo',['name'=>'曹操','age'=>20]);
//     var_dump($myredis->hgetall('sanguo'));
// });

#demo5
// $cid = go(function () {
//     echo "co 1 start\n";
//     co::yield();    //手动挂起协程，如果协程没有被重新唤起，则，下面的内容不会被执行
//     echo "co 1 end\n";
// });

// go(function () use ($cid) {
//     // co::resume($cid);   //手动恢复协程
//     echo "co 2 start\n";
//     co::sleep(0.5); //阻塞切换协程
//     // co::resume($cid);   //手动恢复协程
//     echo "co 2 end\n";
// });

#demo6
// go(function () {
//     go(function () {
//         co::sleep(5.0);
//         echo Swoole\Coroutine::getCid().PHP_EOL;    //同一个协程ID 2
//         echo "co[2] end\n";
//         go(function () {
//             // co::sleep(3.0);
//             echo Swoole\Coroutine::getCid().PHP_EOL;    //协程ID 3
//             echo "co[4] end\n";
//             go(function(){
//                 // co::sleep(1.0);
//                 echo Swoole\Coroutine::getCid().PHP_EOL;    //协程ID 4
//                 echo "co[5] end\n"; //go里面最后执行，越里面的go越晚执行
//             });
//         });
//         echo Swoole\Coroutine::getCid().PHP_EOL;    //同一个协程ID  2
//         echo "co[3] end\n"; //如果没有io阻塞的情况下，go外面的后执行
//     });
//     co::sleep(2.0);
//     echo Swoole\Coroutine::getCid().PHP_EOL;    //协程ID 1
//     echo "co[1] end\n";
// });

#demo7
// for ($i = 0; $i < 3; $i++) {
//     Co\run(function () {    //协程容器里的先执行完
//         go(function () {
//             co::sleep(1);
//         });
//     });
// }
// echo '123'; //协程容器外的代码最后执行

#demo8
function task1()
{
    for ($i = 0; $i <= 300; $i++) {
        // 写入文件，大概要 3000 微秒
        usleep(3000);
        echo "写入文件{$i}\n";
        Co::sleep(0.001);// 挂起当前协程，0.001 秒后恢复 // 相当于切换协程
    }
}

function task2()
{
    for ($i = 0; $i <= 500; $i++) {
        // 发送邮件给 500 名会员，大概 3000 微秒
        usleep(3000);
        echo "发送邮件{$i}\n";
        Co::sleep(0.001);// 挂起当前协程，0.001 秒后恢复 // 相当于切换协程
    }
}

function task3()
{
    for ($i = 0; $i <= 100; $i++) {
        // 模拟插入 100 条数据，大概 3000 微秒
        usleep(3000);
        echo "插入数据{$i}\n";
        Co::sleep(0.001);// 挂起当前协程，0.001 秒后恢复 // 相当于切换协程
    }
}

$pid1 = go('task1');// go 函数是 swoole 的开启协程函数，用于开启一个协程
$pid2 = go('task2');
$pid3 = go('task3');

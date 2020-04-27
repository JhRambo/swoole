<?php

#demo1
// use Swoole\Process;
// echo '当前进程ID：'.getmypid().PHP_EOL;
// cli_set_process_title('mymain');  //设置主进程名

// # 1.1 开启协程======这里是错误的？协程里面不能开始子进程
// // go(function(){
// //     //开启一个子进程
// //     $process = new Process(function () {
// //         cli_set_process_title('mychild');  //设置子进程名
// //         $file1 = md5_file('../../test/test11.php'); //读取文件
// //         while(1){
// //             co::sleep(10);
// //             $file2 = md5_file('../../test/test11.php');
// //             if(strcmp($file1,$file2)!==0){  //比较当前文件是否发生变化
// //                 $file1 = $file2;
// //                 echo '文件被修改：'.date('Y-m-d H:i:s').PHP_EOL;
// //             }
// //         }
// //     });
// //     $process->start();
// //     // $ret = Process::wait();
// // },false,1,true);

//  # 1.2 开启一个子进程
//  $process = new Process(function () {
//     cli_set_process_title('mychild');  //设置子进程名
//     echo '当前子进程ID：'.getmypid().PHP_EOL;
//     $file1 = md5_file('../../test/test.php'); //读取文件
//     while(1){
//         sleep(10);
//         $file2 = md5_file('../../test/test.php');
//         if(strcmp($file1,$file2)!==0){  //比较当前文件是否发生变化
//             $file1 = $file2;
//             //监控文件是否被修改
//             echo '文件被修改：'.date('Y-m-d H:i:s').PHP_EOL;
//         }
//     }
// });
// print_r($process);
// $process->start();

// //父进程回收子进程，监听子进程退出信号，如果不回收子进程，由于主进程退出了，子进程将变成孤儿进程被pid=1的init主进程管理
// Process::signal(SIGCHLD, function($sig) {
//     //必须非阻塞
//     while ($ret = Process::wait(false)){
//         //执行回收后的处理逻辑，比如拉起一个新的进程
//     }   
// });
// //设置父进程不退出
// while(1){
//     sleep(1);
// }

#demo2
// use Swoole\Process;
// echo '当前进程ID：'.getmypid().PHP_EOL;
// cli_set_process_title('mymain');  //设置进程名

// //创建子进程
// $process = new Process(function () {
//     cli_set_process_title('mychild');  //设置子进程名
//     echo "我是一个子进程，我的进程ID：".getmypid().PHP_EOL; //子进程结束必须要执行wait进行回收，否则子进程会变成僵尸进程（前提是主进程未退出，否则将成为孤儿进程）
//     echo '设置了重定向，利用管道。我是子进程里面输出的内容，我将不会在这里输出，而是输出到主进程里。';
// },true);
// print_r($process);
// $process->start();

// Process::signal(SIGCHLD, function($sig) {
//     //必须为false，非阻塞模式
//     while($ret =  Process::wait(false)) {
//     }
// });

// echo $process->read().'hi，我是主进程输出'.PHP_EOL;

#demo3
// use Swoole\Process;
// echo '当前进程ID：'.getmypid().PHP_EOL;
// cli_set_process_title('mymain');  //设置进程名

// //创建子进程
// $process1 = new Process(function () {
//     echo '当前子进程ID：'.getmypid().PHP_EOL;
//     cli_set_process_title('mychild1');  //设置子进程名
//     // while(1){
//     //     sleep(1);
//     // }
// });
// $process1->start();

// //创建子进程，没有sleep，默认执行完之后，自动退出，但是需要主进程回收，不然会变成僵尸进程
// // 处理僵尸进程，有两种方式：
// //1.终止主进程
// //2.kill僵尸进程
// $process2 = new Process(function () {
//     echo '当前子进程ID：'.getmypid().PHP_EOL;
//     cli_set_process_title('mychild2');  //设置子进程名
// });
// $process2->start();

// // 回收子进程
// Process::signal(SIGCHLD, function($sig){
//     //必须非阻塞
//     while ($ret = Process::wait(false)){
//         echo '进程ID：'.$ret['pid'];
//     }   
// });
// while(1){
//     sleep(1);
// }

#demo4
// use Swoole\Process;
// echo '当前进程ID：'.getmypid().PHP_EOL;
// cli_set_process_title('mymain');  //设置进程名，如果不设置，终端显示进程名称 php process_server.php

// //创建子进程
// //进程结合httpserver
// $process1 = new Process(function () {
//     $http = new Swoole\Http\Server("0.0.0.0", 9501);
//     $http->set([
//         'worker_num' => 2
//     ]);
//     $http->on('request', function($request, $response){
//         if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
//             $response->status(404);
//             $response->end(); //writed() 的区别，write 返回 Transfer-Encoding: chunked
//             return;
//         }
//         $response->end("myhttp");   //write() 的区别
//     });
//     $http->on('start', function(){
//         echo '当前子进程ID：'.getmypid().PHP_EOL;
//         cli_set_process_title('mymaster');  //设置子进程名
//     });
//     $http->on('managerstart', function(){
//         echo '当前子进程ID：'.getmypid().PHP_EOL;
//         cli_set_process_title('mymanager');  //设置子进程名
//     });
//     $http->on('workerstart', function(){
//         echo '当前子进程ID：'.getmypid().PHP_EOL;
//         cli_set_process_title('myworker');  //设置子进程名
//     });
//     $http->start();
// });
// $process1->start();

// # 1.1 退出
// Process::wait();   //默认阻塞true
// //如果是阻塞模式，下面将不会被执行，且主进程将不会退出

// # 1.2 退出
// //回收子进程，拦截SIGCHLD信号进行处理
// Process::signal(SIGCHLD, function($sig){
//     //必须非阻塞
//     while ($ret = Process::wait(false)){
//         // echo '进程ID：'.$ret['pid'];
//     }   
// });
// while(1){
//     sleep(1);
// }

#demo5 进程间通信
// use Swoole\Process;
// echo '当前进程ID：'.getmypid().PHP_EOL;
// cli_set_process_title('mymain');  //设置进程名

// $process1 = new Process(function (Process $p1) {
//     echo '当前子进程ID：'.getmypid().PHP_EOL;
//     cli_set_process_title('mychild1');  //设置子进程名
//     $mysql = new Swoole\Coroutine\MySQL();  //协程客户端
    
//     $mysql->connect([
//         'host'     => '127.0.0.1',
//         'port'     => 3306,
//         'user'     => 'root',
//         'password' => '123456',
//         'database' => 'test',
//     ]);

//     while(1){
//         $sql = "select * from `order` where is_pay=0 and is_mail=0";
//         $res = $mysql->query($sql);
//         if($res){
//             $p1->write(json_encode($res,true));   //往主进程中写入数据
//         }
//         sleep(3);
//     }
// },false,1,true);    //true表示开启协程
// $process1->start();

// $process2 = new Process(function (Process $p2) {
//     echo '当前子进程ID：'.getmypid().PHP_EOL;
//     cli_set_process_title('mychild2');  //设置子进程名
//     while(1){
//         $data = $p2->read();    //读取主进程向子进程2写入的数据
//         if($data){
//             echo '进程1推送数据到进程2成功，值为：'.$data.PHP_EOL;
//         }
//         sleep(1);
//     }
// });
// $process2->start();

// //主进程
// while(1){
//     $data = $process1->read();  //读取子进程1向主进程写入的数据
//     if($data){
//         $process2->write($data);    //往子进程2写入数据
//     }
// }

// Process::signal(SIGCHLD, function($singo){
//     //必须非阻塞
//     while ($ret = Process::wait(false)){
//         // echo '进程ID：'.$ret['pid'];
//     }   
// });

#demo6 exec执行外部程序
// use Swoole\Process;
// echo '当前进程ID：'.getmypid().PHP_EOL;
// cli_set_process_title('mymain');  //设置进程名

// $process1 = new Process(function (Process $p1) {
//     $p1->exec('/usr/bin/php',['./child1.php']);    //执行一个外部程序
// },true,1,true);
// $process1->start();

// while(1){
//     echo "主进程输出：".$process1->read().PHP_EOL;
//     sleep(5);
// }

// Process::signal(SIGCHLD, function($sig) {
//     //必须为false，非阻塞模式
//     while($ret =  Process::wait(false)) {
//         echo "PID={$ret['pid']}\n";
//     }
// });

#demo7 exec监控子进程，并动态启动子进程
// use Swoole\Process;

// require "../conf/function.php";
// echo '当前进程ID：'.getmypid().PHP_EOL;
// cli_set_process_title('mymain');  //设置进程名

// // init();

// //让主进程一直运行，没有这个的话，子进程会变成孤儿进程被init（pid=1）进程管理
// while(1){
//     sleep(5);
//     init();
// }











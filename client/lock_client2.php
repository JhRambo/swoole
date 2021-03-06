<?php

$client = new Swoole\Client(SWOOLE_SOCK_TCP);
if (!$client->connect('127.0.0.1', 9501, -1)) {
    exit("connect failed. Error: {$client->errCode}\n");
}
 
$p_file = "../conf/test.txt";

if(!$client->send($p_file)){
    echo "发送失败，错误代码：".$client->errCode;
}
 
$o_file = fopen($p_file,'a+');
// flock()加锁方式:
// flock($o_file,LOCK_EX);
 
// swoole加锁方式:
$lock = new swoole_lock(SWOOLE_FILELOCK, $p_file);
$lock->lock();

fwrite($o_file, 'xxxx');

fclose($o_file);
 
// 两种解锁方式
// flock($o_file, LOCK_UN);
$lock->unlock();

echo $client->recv();
$client->close();
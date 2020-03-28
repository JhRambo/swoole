<?php

function init()
{
    $config = parse_ini_file('../conf/process.conf', true);
    foreach ($config['childs'] as $value) {
        $v = explode(' ', $value);
        $p = new Swoole\Process(function (Swoole\Process $p1) use ($v) {
            try {
                $p1->exec($v[0], [$v[1]]);   //执行外部程序
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }, false);
        $p->start();
    }
}
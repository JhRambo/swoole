<?php
/*
 * @Author: your name
 * @Date: 2021-03-30 13:33:07
 * @LastEditTime: 2021-03-30 13:45:15
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /swoole/server/RpcClass.php
 */

/**
 * 简单写一个RPC类，模拟RPC请求,zzz
 */
class RpcClass
{
    //实现计算方法
    public function cart($x)
    {
        if (is_array($x)) {
            return array_sum($x);
        }
        return $x;
    }
}

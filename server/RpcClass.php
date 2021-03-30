<?php

/**
 * 简单写一个RPC类，模拟RPC请求
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

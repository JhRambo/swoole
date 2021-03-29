<?php
/*
 * @Author: your name
 * @Date: 2020-09-22 16:16:05
 * @LastEditTime: 2020-09-22 16:16:06
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: /swoole/client/rpc_client.php
 */

/**
 * RPC客户端
 */
class RpcClient
{
    private $service;
    public function __call($name, $param)
    {
        print_r(func_get_args());
        //远程调用要使用的方法
        if ('service' == $name) {
            $this->service = $param[0];
            print_r($this);
            return $this;
        }
        $cli = new Swoole\Client(SWOOLE_SOCK_TCP);
        $cli->connect('127.0.0.1', 9511);
        $json_data = json_encode(
            [
                'service' => $this->service,
                'action' => $name,
                'param' => $param
            ]
        );
        $cli->send($json_data); //向服务端发送数据
        $result = $cli->recv(); //接收消息
        $cli->close();  //关闭连接
        return json_decode($result, true);
    }
}
$cli = new RpcClient();
var_dump($cli->service('RpcClass')->cart(11,12));
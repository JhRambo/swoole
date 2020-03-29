<?php

/**
 * 连接MySQL池封装
 */

/**
 * 代码调用过程详解
 *1、协程客户端方式下的调用，也是实现了之前封装好的连接池类
 *只是createDb()的抽象方法用了swoole内置的协程客户端去实现。
 *2、server启动后，初始化都和同步一样。不一样的在获取连接对象的时候，此时如果并发了10个请求，
 *同样是配置了1个worker进程在处理，但是在第一请求到达，pop出池中的一个连接对象，执行到query()方法，
 *遇上sleep阻塞时，此时，woker进程不是在等待select的完成，而是切换到另外的协程去处理下一个请求。
 *完成后同样释放对象到池中。当中有重点解释的代码段中getConnection()中。
 *利用channel通道，push和pop创建连接和使用连接
 */

use Swoole\Coroutine\Channel;

abstract class AbstractPool
{
    private $min;   //最少连接数
    private $max;   //最大连接数
    private $count; //当前连接数
    private $connections;   //连接池组
    protected $spareTime;   //用于空闲连接回收判断

    //数据库配置
    protected $dbConfig = array(
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => '123456',
        'database' => 'test',
        'charset' => 'utf8',
        'timeout' => 3,
    );

    private $inited = false;

    /**
     * 抽象方法不实现具体函数，用于子类继承实现
     */
    protected abstract function createDb();

    /**
     * 构造函数，初始化变量
     */
    public function __construct()
    {
        $this->min = 10;
        $this->max = 100;
        $this->spareTime = 10 * 3600;
        $this->connections = new Channel($this->max + 1);
    }

    /**
     * 创建对象
     */
    protected function createObject()
    {
        $obj = null;
        $db = $this->createDb();
        if ($db) {
            $obj = [
                'last_used_time' => time(),
                'db' => $db,
            ];
        }
        return $obj;
    }

    /**
     * 初始化最小数量连接池
     * @return $this|null
     */
    public function init()
    {
        if ($this->inited) {
            return null;
        }
        for ($i = 0; $i < $this->min; $i++) {
            $obj = $this->createObject();
            $this->count++;
            $this->connections->push($obj);
        }
        return $this;
    }

    /**
     * 获得连接
     */
    public function getConnection($timeOut = 3)
    {
        $obj = null;
        if ($this->connections->isEmpty()) {
            if ($this->count < $this->max) {    //连接数没达到最大，新建连接入池
                $this->count++;
                $obj = $this->createObject();   #1
            } else {
                $obj = $this->connections->pop($timeOut);   #2
            }
        } else {
            $obj = $this->connections->pop($timeOut);   #3
        }
        return $obj;
    }

    /**
     * 连接池空闲，创建连接，往池子中扔
     */
    public function free($obj)
    {
        if ($obj) {
            $this->connections->push($obj);
        }
    }

    /**
     * 处理空闲连接
     */
    public function gcSpareObject()
    {
        //大约2分钟检测一次连接
        Swoole\timer::tick(120000, function () {
            $list = [];
            if ($this->connections->length() < intval($this->max * 0.5)) {
                echo "请求连接数还比较多，暂不回收空闲连接\n";
            }   #1
            while (true) {
                if (!$this->connections->isEmpty()) {
                    $obj = $this->connections->pop(0.001);
                    $last_used_time = $obj['last_used_time'];
                    if ($this->count > $this->min && (time() - $last_used_time > $this->spareTime)) {   //回收
                        $this->count--;
                    } else {
                        array_push($list, $obj);
                    }
                } else {
                    break;
                }
            }
            foreach ($list as $item) {
                $this->connections->push($item);
            }
            unset($list);
        });
    }
}

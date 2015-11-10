<?php
namespace Swoole;

use Swoole\Interfaces\TcpServer;
use Swoole\Base\TcpBase;
use Swoole\System\Console;
use Swoole\Dispatcher;
use Swoole\System\DataParser;

class Application extends TcpBase implements TcpServer{

    public function __construct($di){
    	parent::__construct();
    	// 获取框架配置参数
    	$this->config = $di->get('config');
        // 获取到了配置参数才开启服务
        if($this->config){
            if(isset($this->config['ssl_key_file']) && isset($this->config['ssl_cert_file'])){
                $serv = new \swoole_server("0.0.0.0", 9501 , SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);
            }else{
                $serv = new \swoole_server("0.0.0.0", 9501 , SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
            }
            global $di;
            // 注入serv服务
            $di->set('serv' , function() use ($serv){
            	return $serv;
            });

            // 设置配置
            $serv->set($this->config);

            //注册Start回调
            if($di->registered('onStart')){
            	$serv->on('Start', $di->get('onStart'));
            }else{
            	$serv->on('Start', array($this, 'onStart'));
            }

            // 注册WorkerStart回调
            if($di->registered('onWorkerStart')){
            	$serv->on('WorkerStart', $di->get('onWorkerStart'));
            }else{
            	$serv->on('WorkerStart', array($this, 'onWorkerStart'));
            }

            // 注册WorkerStop回调
            if($di->registered('onWorkerStop')){
            	$serv->on('WorkerStop', $di->get('onWorkerStop'));
            }else{
            	$serv->on('WorkerStop', array($this, 'onWorkerStop'));
            }

            // 注册Connect回调
            if($di->registered('onConnect')){
            	$serv->on('Connect', $di->get('onConnect'));
            }else{
            	$serv->on('Connect', array($this, 'onConnect'));
            }

            // 注册Receive回调
            if($di->registered('onReceive')){
            	$serv->on('Receive', $di->get('onReceive'));
            }else{
            	$serv->on('Receive', array($this, 'onReceive'));
            }

            // 注册Close回调
            if($di->registered('onClose')){
            	$serv->on('Close', $di->get('onClose'));
            }else{
            	$serv->on('Close', array($this, 'onClose'));
            }
            
            // 注册Shutdown回调
            if($di->registered('onShutdown')){
            	$serv->on('Shutdown', $di->get('onShutdown'));
            }else{
            	$serv->on('Shutdown', array($this,'onShutdown'));
            }

            // 注册Timer回调
            if($di->registered('onTimer')){
            	$serv->on('Timer', $di->get('onTimer'));
            }else{
            	$serv->on('Timer', array($this,'onTimer'));
            }

            // 注册Task回调
            if($di->registered('onTask')){
            	$serv->on('Task' , $di->get('onTask'));
            }else{
            	$serv->on('Task', array($this, 'onTask'));
            }
            
            // 注册Finish回调
            if($di->registered('onFinish')){
            	$serv->on('Finish', $di->get('onFinish'));
            }else{
            	$serv->on('Finish', array($this, 'onFinish'));
            }
         	
         	// 启动服务
            $serv->start();
        }else{
            exit('read configure error!');
        }
    }

    /**
     * 服务启动回调
     * @param  \swoole_server $serv swoole_server
     * @return null                 null
     */
    public function onStart(\swoole_server $serv){
    	$this->pid($serv);
        Console::info('Swoole 内核版本: ', $this->swoole_version, ', Swoole FrameWork 框架版本: ',$this->version);
        Console::info(str_repeat('-', 57));
        Console::info(str_pad('应用服务器「Manager进程 # '.$serv->manager_pid .'」' , 58 , ' ',STR_PAD_RIGHT) , '启动成功');
        Console::info(str_repeat('-', 57));
        Console::info(str_pad('应用服务器「Master进程  # '.$serv->master_pid .'」' , 58 , ' ' , STR_PAD_RIGHT) , '启动成功');
        Console::info(str_repeat('-', 57));
    }

    public function onWorkerStart(\swoole_server $serv, $worker_id){

    }

    public function onWorkerStop(\swoole_server $server, $worker_id){

    }

    /**
     * 连接回调
     * @param  \swoole_server $serv 	swoole_server 对象
     * @param  intval         $fd   	TCP客户端连接的文件描述符
     * @param  intval         $from_id  TCP连接所在的Reactor线程ID
     * @return null 
     */
    public function onConnect(\swoole_server $serv , $fd , $from_id){
        Console::conn(str_pad('新的客户端「Fd # '.$fd .'」「Fromid # '.$from_id .'」' , 58 , ' ',STR_PAD_RIGHT) , '连接成功');
        Console::info(str_repeat('-', 57));
    }

    /**
     * 接收数据回调
     * @param  \swoole_server $serv    swoole_server 对象
     * @param  intval         $fd      TCP客户端连接的文件描述符
     * @param  intval         $from_id TCP连接所在的Reactor线程ID
     * @param  string         $data    数据包
     * @return null 
     */
    public function onReceive(\swoole_server $serv, $fd, $from_id, $data){
        global $di;
        Console::info($data);
        // 解包数据
        $data = DataParser::decode($data);
        // 注入fd
        $di->set('fd' , function () use ($fd){
            return $fd;
        });
        // 注入from_id
        $di->set('from_id' , function () use ($from_id){
            return $from_id;
        });
        // 注入request
        $di->set('request' , function () use ($data){
            return $data['data'];
        });
    	// 数据分发
        $router = \Swoole\Dispatcher::getInstance($di);
        $router->adapter($data['code']); 
    }

    /**
     * 关闭连接回调
     * @param  \swoole_server $serv    swoole_server 对象
     * @param  intval         $fd      TCP客户端连接的文件描述符
     * @param  intval         $from_id TCP连接所在的Reactor线程ID
     * @return null                  
     */
    public function onClose(\swoole_server $serv , $fd , $from_id){
        Console::close(str_pad('客户端连接「Fd # '.$fd .'」「Fromid # '.$from_id .'」' , 58 , ' ',STR_PAD_RIGHT) , '断开成功');
        Console::info(str_repeat('-', 57));
    }

    public function onShutdown(\swoole_server $serv){

    }

    /**
     * 任务回调
     * @param  \swoole_server $serv    swoole_server 对象
     * @param  intval         $task_id task任务ID
     * @param  intval         $from_id work进程ID
     * @param  string         $data    任务内容
     * @return null
     */
    public function onTask(\swoole_server $serv , $task_id, $from_id, $data){

    }

    /**
     * 定时器回调
     * @param  \swoole_server $serv     swoole_server 对象
     * @param  intval         $interval 定时器间隔时间
     * @return null
     */
    public function onTimer(\swoole_server $serv , $interval){

    }

    /**
     * 任务结束回调
     * @param  \swoole_server $serv    swoole_server 对象
     * @param  intval         $task_id task任务ID
     * @param  string         $data    任务处理的结果内容
     * @return null 
     */
    public function onFinish(\swoole_server $serv, $task_id, $data){

    }
}
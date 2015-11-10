<?php
namespace Swoole\Mvc\Controller;
use Swoole\system\DataParser;

class Controller implements \Swoole\Interfaces\Controller{

	public function __construct($di){
		$this->di = $di;
	}

	public function initialize(){

	}

	/**
	 * 单个client推送
	 * @param  [type] $data [description]
	 * @param  [type] $fd   [description]
	 * @return [type]       [description]
	 */
	public function send($data , $fd = null){
		$fd = $fd ? $fd : $this->di->fd;

		$data = DataParser::encode($data);

		$this->di->serv->send($fd , $data);
	}

	/**
	 * 全服广播
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function broadcast($data){
		// 打包数据
		$data = DataParser::encode($data);
		$start_fd = 0;
        while (true) {
            $conn_list = $this->serv->connection_list($start_fd, 10);
            if ($conn_list === false) {
                break;
            }
            $start_fd = end($conn_list);

            foreach ($conn_list as $fd) {
                $this->serv->send($fd, $data);
            }
        }
	}


	/**
	 * 投递任务
	 * @param [type] $task [description]
	 */
	public function addTask($task){
		return $this->di->serv->task(serialize($task));
	}
}
<?php
namespace Swoole\Base;

use Swoole\System\Console;

class TcpBase extends Base{
	// 配置项
    protected $set;

	public function __construct(){
		parent::__construct();
		// 初始化控制台
    	Console::initialize();
	}

	protected function init(){

	}

	// 缓存进程号
	protected function pid(\swoole_server $serv){
		// 判断run目录是否存在
		$dir = SF_PATH . 'Runtime/';
		if(!is_dir($dir)){
			// 创建目录
			@mkdir($dir);
		}
		// 写入pid文件
		$masterpid = rtrim($dir , '\/') . '/'. 'tcp_master.pid';
		$managerpid = rtrim($dir , '\/') . '/'. 'tcp_manager.pid';
		file_put_contents($masterpid , $serv->master_pid);
		file_put_contents($managerpid , $serv->master_pid);
	}
}

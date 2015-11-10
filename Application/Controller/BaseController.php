<?php
namespace Application\Controller;

class BaseController extends \Swoole\Mvc\Controller\Controller{
	public function initialize(){
		// $this->checkLogin();
	}

	// 检测是否登录
	protected function checkLogin(){
		// $redis_object = \Application\Library\Redis::getInstance();
		// $redis = $redis_object->createRedis();

		// $this->serv->send($this->fd , 'abc');
		// 验证token
		// $redis->get();


	}
}
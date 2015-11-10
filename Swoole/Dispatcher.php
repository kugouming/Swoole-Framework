<?php
namespace Swoole;
/**
 * 分发器
 */
class Dispatcher{
	// 对象
	protected static $instance;
	// di容器对象
	protected $di;
	// controller对象
	protected static $controller = array();
	// 路由协议
	protected static $protocol = array();

    final protected function __construct($di){
    	$this->di = $di;
    	$this->protocol = $di->get('router');
    }

    final protected function __clone(){

    }

    // 获取单例
    public static function getInstance($di){
        if (!(self::$instance instanceof self)) {
            self::$instance = new self($di);
        }
        return self::$instance;
    }

	// 路由分发
	public function adapter($code){
		if(isset($this->protocol[$code])){
			$protocol = $this->protocol[$code];
			// 获取控制器和方法
			$c = $protocol['c'];
			$a = $protocol['a'];
			if(isset($this->controller[$code])){
				$controller = $this->controller[$code];
			}else{
				$app_name = explode('/', APP_PATH);
				$class = '\\'.ucfirst($app_name[1]).'\\Controller\\'.$c.'Controller';
				print_r($this->di);
				$this->controller[$code] = new $class($this->di);
				$controller = $this->controller[$code];
			}
			// 组装action
			$action = $a.'Action';
			$controller->initialize();
			$controller->$action();
		}else{
			$controller = new \Swoole\Mvc\Controller\Controller($this->di);
			$controller->initialize();
			$controller->notFound();
		}
	}
}
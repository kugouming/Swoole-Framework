<?php
namespace Swoole\Base;
use Swoole\System\Console;

class CacheBase extends Base{
	// 配置必要参数
	protected $must_config = array();

	// 配置
	protected $config = array();

	// 缓存对象 Redis Mongodb 等
	private $cache_object;

	public function __construct($config){
		// 读取配置文件
		$this->config = $config;

		// 检测配置文件
        if($this->checkConfigure()){
            return Console::error(get_class($this) . ' [ ' , $k , ' ] configure must');
        }

        // 连接缓存
        $this->connect();
	}

	/**
	 * 检测配置合法性
	 * @return [type] [description]
	 */
	protected function checkConfigure(){
		$configure = array();
		foreach ($this->must_config as $k => $m) {
			if($m == 1){
				if(isset($this->config[$k]) && !empty($this->config[$k])){
					$configure[$k] = $this->config[$k];	
				}else{
					return $k;
				}
			}else if($m == 0){
				if(isset($this->config[$k])){
					$configure[$k] = $this->config[$k];
				}
			}
		}
		$this->config = $configure;
	}

	/**
	 * 获取缓存对象
	 * @return object 缓存对象
	 */
	public function getInstance(){
		return $this->cache_object;
	}
}
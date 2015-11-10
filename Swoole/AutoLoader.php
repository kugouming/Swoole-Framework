<?php
/**
 * 自动加载器
 */

// 设置框架根目录
defined('SF_PATH') || define('SF_PATH' , dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

class AutoLoader {
	public static function autoloadRegister($class_name){
		$file = SF_PATH . str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
		if(is_file($file)){
			include ($file);
		}else{
			throw new \Exception('\'' .$file . '\' is not found.');
		}
	}
}

// 注册自动加载器
spl_autoload_register(array('AutoLoader', 'autoloadRegister'));
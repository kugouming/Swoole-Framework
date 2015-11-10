<?php

// 自动加载器
include 'Swoole/AutoLoader.php';

define('APP_DEBUG' , TRUE);
define('APP_PATH' , './Application/');

// 实例化di容器
$di = new \Swoole\Factory();

// 获取服务器配置
$config = include 'Config/configure.php';
$di->set('config' , function () use ($config){
	return $config;
});

// 获取路由映射
$router = include 'Config/router.php';
$di->set('router' , function () use ($router){
	return $router;
});

// 注入redis缓存服务
$di->set('redis' , function(){
	$redis = new \Swoole\Cache\Redis(array(
		'host'		=>	'127.0.0.1',
		'port'		=>	6379,
		'lifetime' 	=>	10
		));
	return $redis;
});

// 注入mongo缓存服务
$di->set('mongo', function(){
	$mongo = new \Swoole\Cache\Mongo(array(
		'server'	=>	'mongodb://127.0.0.1',
		'db'		=>	'caches',
		'collection'=>	'image',
		'lifetime' 	=>	40
		));
	return $mongo;
});


// 注入swoole回调
// $di->set('onStart' , function (\swoole_server $serv){
// 	echo "Server is Running" . PHP_EOL;
//     //管理进程的PID，通过向管理进程发送SIGUSR1信号可实现柔性重启
//     echo $serv->manager_pid . PHP_EOL;
//     //主进程的PID，通过向主进程发送SIGTERM信号可安全关闭服务器
//     echo $serv->master_pid . PHP_EOL;
//     //将管理进程的PID写入文件方便管理进程
// });


// 第一种方式加入容器
// $di->set('onConnect', function (\swoole_server $serv , $fd){
//         echo "Client------lasjdflasjflj- {$fd} open connection". PHP_EOL;
// });

// 第二种方式加入容器
// $di->onConnect = function (\swoole_server $serv , $fd){
//         echo "Client------- {$fd} open connection". PHP_EOL;
// };


new \Swoole\Application($di);
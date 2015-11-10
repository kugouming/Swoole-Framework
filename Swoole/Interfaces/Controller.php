<?php
namespace Swoole\Interfaces;
/**
 * 控制器接口
 */
interface Controller{

	public function __construct($di);

	// 初始化
	public function initialize();

	// 发送消息
	public function send($data);

	// 全服广播
	public function broadcast($data);

	// 投递任务
	public function addTask($task);

	// 
}
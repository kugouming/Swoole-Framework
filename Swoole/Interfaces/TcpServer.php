<?php
namespace Swoole\Interfaces;
/**
 * Tcp interface
 */
interface TcpServer{

	/**
	 * 服务启动回调
	 * @param  \swoole_server $serv swoole_server对象
	 * @return [type]               [description]
	 */
	public function onStart(\swoole_server $serv);

	/**
	 * [onWorkerStart description]
	 * @param  \swoole_server $serv      [description]
	 * @param  [type]         $worker_id [description]
	 * @return [type]                    [description]
	 */
	public function onWorkerStart(\swoole_server $serv, $worker_id);

	public function onWorkerStop(\swoole_server $server, $worker_id);

	public function onConnect(\swoole_server $serv , $fd , $from_id);

	public function onReceive(\swoole_server $serv, $fd, $from_id, $data);

	public function onClose(\swoole_server $serv , $fd , $from_id);

	public function onShutdown(\swoole_server $serv);

	public function onTask(\swoole_server $serv , $task_id, $from_id, $data);

	public function onTimer(\swoole_server $serv , $interval);

	public function onFinish(\swoole_server $serv, $task_id, $data);

}

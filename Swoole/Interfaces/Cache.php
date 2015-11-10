<?php
namespace Swoole\Interfaces;
/**
 * 缓存接口
 */
interface Cache{

	public function connect();

	public function save($key , $value);

	public function get($key);

	public function delete($key);

	public function flush();

	public function exists($key);

	public function getLifetime($key);
}
<?php
namespace Swoole\Memory;
/**
 * Swoole Lock 操作
 */

class Lock{
	private $lock = null;

	public function __construct($lock_type , $lock_file = null){
		try{
			$this->lock = new \swoole_lock($lock_type , $lock_file);
		}catch(Excption $e){
			echo 222;
		}
		
	}

	public function lock(){
		return $this->lock->lock();
	}

	public function trylock(){
		return $this->lock->trylock();
	}

	public function unlock(){
		return $this->lock->unlock();
	}

	public function lock_read(){
		return $this->lock->lock_read();
	}

	public function trylock_read(){
		return $this->lock->trylock_read();
	}
}
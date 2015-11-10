<?php

namespace Swoole\Base;

use Swoole\System\SFrame;

class Base{
	// swoole 版本
	public $swoole_version;

	// 框架版本
	public $version;

	public function __construct(){
		// 获取swoole版本
		$this->swoole_version = swoole_version();
		// 获取框架版本
		$this->version = SFrame::S_VERSION;
	}
}


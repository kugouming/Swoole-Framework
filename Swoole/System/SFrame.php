<?php
namespace Swoole\System;

class SFrame {
    /**  框架版本 */
    const S_VERSION       = '1.0.0';

	// 日志级别
    /** 调试 */
    const L_DEBUG         = 1;
    /** 信息 */
    const L_INFO          = 2;
    /** 警告 */
    const L_WARNING       = 3;
    /** 错误 */
    const L_ERROR         = 4;

    // Swoole回调
    /**  连接 */
    const S_CONN          = 5;
    /**  断开 */
    const S_CLOSE         = 6;
}
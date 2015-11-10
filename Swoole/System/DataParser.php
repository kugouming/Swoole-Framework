<?php
/**
 * 数据协议打包解包
 */

namespace Swoole\System;

class DataParser{
	/**
	 * 打包数据
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
    public static function encode($data){
        // 选用json格式化数据
        $buffer = json_encode($data,JSON_UNESCAPED_UNICODE);
        //如果为debug模式则不进行打包解包
        if(APP_DEBUG) return $buffer;
        // 包的整体长度为json长度加首部四个字节(首部数据包长度存储占用空间)
        return pack('N', strlen($buffer)) . $buffer;
    }

    /**
     * 解包数据
     * @param  [type] $buffer [description]
     * @return [type]         [description]
     */
    public static function decode($buffer){
        //如果为debug模式则不进行打包解包
        if(APP_DEBUG) return json_decode($buffer, true);
        return json_decode(substr($buffer, 4), true);
    }
}
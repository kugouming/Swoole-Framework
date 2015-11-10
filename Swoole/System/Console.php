<?php
namespace Swoole\System;

use Swoole\System\SFrame;

/**
 * 控制台管理
 */
class Console{
	// 是否开启控制台显示功能
	private static $display_enable = true;

	// 控制台日志显示类型
	private static $display_levels = array();

	// 控制台显示字体颜色
	private static $font_colors = array();

	// 控制台显示背景颜色
	private static $back_colors = array();

	// 控制台初始化
	public static function initialize(){
		// 控制台日志显示类型
        self::$display_levels = array(
            1 => '[调试]',
            2 => '[信息]',
            3 => '[警告]',
            4 => '[错误]',
            5 => '[连接]',
            6 => '[断开]',
        );

		// 初始化控制台字体颜色
        self::$font_colors = array(
            'black'        => '0;30',
            'dark_gray'    => '1;30',
            'blue'         => '0;34',
            'light_blue'   => '1;34',
            'green'        => '0;32',
            'light_green'  => '1;32',
            'cyan'         => '0;36',
            'light_cyan'   => '1;36',
            'red'          => '0;31',
            'light_red'    => '1;31',
            'purple'       => '0;35',
            'light_purple' => '1;35',
            'brown'        => '0;33',
            'yellow'       => '1;33',
            'light_gray'   => '0;37',
            'white'        => '1;37'
        );

        // 初始化控制台背景颜色
        self::$back_colors = array(
            'black'      => '40',
            'red'        => '41',
            'green'      => '42',
            'yellow'     => '43',
            'blue'       => '44',
            'magenta'    => '45',
            'cyan'       => '46',
            'light_gray' => '47'
        );
	}


   	/**
   	 * 设置 Shell 控制台文本颜色
   	 * @param  string $string     需要添加颜色的文本字符串
   	 * @param  string $font_color 设置的字体颜色
   	 * @param  string $back_color 设置的背景颜色
   	 * @return string             设置好颜色的文本字符串
   	 */
    private static function color($string , $font_color = null, $back_color = null) {
        $color = '';

        // 检测前景颜色是否设定
        if (isset(self::$font_colors[$font_color])) {
            $color .= "\033[" . self::$font_colors[$font_color] . "m";
        }

        // 检测背景颜色是否设定
        if (isset(self::$back_colors[$back_color])) {
            $color .= "\033[" . self::$back_colors[$back_color] . "m";
        }

        // 给文本添加指定的颜色
        $color .= $string . "\033[0m";

        return $color;
    }

    private static function display($level, $args) {
    	// 检测是否显示
        if (!self::$display_enable) return false;

        // 组装打印显示内容
        $doc[] = date('Y-m-d H:i:s') . ' ';

        // if (0 < self::$ctx->pid) {
        //     $doc[] = '[#' . self::$ctx->pid . ']';
        // }
        
        $doc[] = self::$display_levels[$level] . ' ';

        foreach ($args as $v) {
            if (is_array($v))
                $doc[] = json_encode($v, 320);
            elseif (is_bool($v))
                $doc[] = $v ? 'True' : 'False';
            else
                $doc[] = strval($v);
        }

       	// 拼接显示内容
        $s = implode('', $doc);

        // 判断操作系统并定义颜色
        if (PHP_OS == 'Linux'){
        	switch ($level) {
        		case SFrame::L_DEBUG :
        			$font_color = null;
        			break;
        		case SFrame::L_INFO :
        			$font_color = null;
        			break;
        		case SFrame::L_WARNING :
        			$font_color = 'yellow';
        			break;
        		case SFrame::L_ERROR :
        			$font_color = 'light_red';
        			break;
                case SFrame::S_CONN :               // 连接
                    $font_color = 'green';
                    break;
                case SFrame::S_CLOSE :              // 关闭练级
                    $font_color = 'cyan';
                    break;
        		default:
        			$font_color = null;
        			break;
        	}
            echo self::color($s, $font_color), PHP_EOL;
        }else{
            echo $s, PHP_EOL;
        }
    }

    public static function dubug(){
    	$args = func_get_args();
        self::display(SFrame::L_DEBUG , $args);
	}

	public static function info(){
		$args = func_get_args();
        self::display(SFrame::L_INFO , $args);
	}

	public static function warning(){
		$args = func_get_args();
        self::display(SFrame::L_WARNING , $args);
	}

	public static function error(){
		$args = func_get_args();
        self::display(SFrame::L_ERROR , $args);
	}

    // 连接
	public static function conn(){
        $args = func_get_args();
        self::display(SFrame::S_CONN , $args);
    }

    // 关闭连接
    public static function close(){
        $args = func_get_args();
        self::display(SFrame::S_CLOSE , $args);
    }
}
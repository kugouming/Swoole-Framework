<?php
/**
 * swoole 配置文件
 * User: Kp
 * Date: 2015/10/19
 * Time: 16:34
 */
return array(
    //Worker进程数
    'worker_num'                => 4,
    //task worker进程数
    'task_worker_num'           => 8,
    //设置程序进入后台作为守护进程运行
    'daemonize'                 => false,
    //每个worker进程允许处理的最大任务数
    'max_request'               => 10000,
    'dispatch_mode'             => 2,
    // 'debug_mode'=> 1,
    //心跳检测
    'heartbeat_check_interval'  => 60,
    'heartbeat_idle_time'       => 600,
    // //开启包检测
    // 'package_max_length'        => 2000000,
    // 'open_length_check'         => true,
    // 'package_length_offset'     => 0,
    // 'package_body_offset'       => 4,
    // 'package_length_type'       => 'N',
    // //开启ssl加密
    // 'ssl_key_file'              => ROOT_PATH . '/config/ssl/ssl.key',
    // 'ssl_cert_file'             => ROOT_PATH . '/config/ssl/ssl.crt',
);
<?php
/**
 * php 管理进程
 * User: Kp
 * Date: 2015/10/20
 * Time: 17:38
 */
if(isset($argv[1])){
    switch($argv[1]){
        case 'start':
            system ( 'php bin/server.php');
            break;
        case 'stop':
            // 获取master进程号
            $pid = file_get_contents('./run/master.pid');
            system('sudo kill -SIGTERM '.$pid);
            break;
        case 'reload':
            // 获取manager进程号
            $pid = file_get_contents('./run/manager.pid');
            system('sudo kill -SIGUSR1 '.$pid);
            break;
        case 'restart':
            // 获取master进程号
            $pid = file_get_contents('./run/master.pid');
            system('sudo kill -SIGTERM '.$pid);
            sleep(1);
            system ( 'php bin/server.php');
            break;
        default:
            echo 'Usage: php socket.php (start | stop | reload | restart)'.PHP_EOL;
            break;
    }
}else{
    echo 'Usage: php socket.php (start | stop | reload | restart)'.PHP_EOL;
}

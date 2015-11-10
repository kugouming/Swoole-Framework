<?php
namespace Application\Controller;
/**
 * 验证用户信息
 * User: Kp
 * Date: 2015/10/20
 * Time: 9:31
 */
class LoginController extends BaseController {

    public function authAction(){
    	$this->di->redis->save("user","yekongmei");
    	$this->send(array("hello world"));
    	$request = $this->di->request;
    	print_r($request);
    }
}
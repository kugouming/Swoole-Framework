<?php
namespace Swoole\Cache;
use Swoole\Interfaces\Cache;
use Swoole\Base\CacheBase;
use Swoole\System\Console;

class Mongo extends CacheBase implements Cache{
	// 设置必要配置参数
    protected $must_config = array('server' => 1 , 'db' => 1 , 'collection' => 1, 'lifetime' => 1);

    /**
     * 连接
     * @return [type] [description]
     */
	public function connect(){
		// 判断是否存在redis
        if (!class_exists('MongoClient')) {
            return Console::error("Class MongoClient not exists");
        }
        // 实例化mongoclient
        try {     
            $this->cache_object = new \MongoClient($this->config['server'], array('connect'=>true)); 
            // 获取db和collection  
            $db = $this->config['db'];
			$collection = $this->config['collection'];
			$this->mongo = $this->cache_object->$db->$collection;
        }catch (MongoConnectionException $e){     
            return Console::error("Mongo Connect fail" , $e->getMessage());  
        }
	}

	/**
     * 缓存内容
     * @param  string $key   缓存的key
     * @param  string $value 缓存值
     * @return boolean       
     */
	public function save($key , $value , $lifetime = null){
		// 判断是否为数据
        $value = is_array($value) ? json_encode($value) : $value;
        // 设置缓存过期截止时间
        $lifetime = $lifetime ? time() + $lifetime : time() + $this->config['lifetime'];
        // 设置更新条件
	    $condition = array('key' => $key);
        // 生成缓存数据
        $record = array('key' => $key , 'time' => $lifetime , 'data' => base64_encode($value));
        // 检测key是否存在
        if($this->exists($key)){
        	// 设置安全操作     
	        $options = array('safe' => 1 , 'multiple' => 0); 
	        try {     
	            $this->mongo->update($condition, $record, $options);     
	            return true;     
	        }catch (MongoCursorException $e){     
	            $this->error = $e->getMessage();     
	            return false;     
	        }
        }else{
        	try {
        		// 执行插入操作     
	            $this->mongo->insert($record);     
	            return true;     
	        }catch (MongoCursorException $e){     
	            $this->error = $e->getMessage();     
	            return false;     
	        } 
        }
	}

	/**
     * 获取一个key的value
     * @param  string $key 键名
     * @return mix    [string | array]      
     */
    public function get($key){
        if($row = $this->findByKey($key)){
        	// 判断过期时间
        	$ttl = $row['time'] - time();
        	if($ttl >= 0){
        		$data = base64_decode($row['data']);
        		$content = json_decode($data , true);
            	return $content == null ? $data : $content;
        	}else{
        		return false;
        	}
        }
        return false;
    }
    
    /**
     * 删除一个key
     * @param  string $key 键名
     * @return boolean     
     */
    public function delete($key){
		// 安全删除 
        $options['safe'] = 1;     
        try {     
            $this->mongo->remove(array('key' => $key), $options);     
            return true;     
        }catch (MongoCursorException $e){     
            $this->error = $e->getMessage();     
            return false;     
        } 
    }

    /**
     * 清空当前数据库中所有的key
     * @return boolean 
     */
    public function flush(){   
        try {     
            $this->mongo->drop();     
            return true;     
        }catch (MongoCursorException $e){     
            $this->error = $e->getMessage();     
            return false;     
        } 
    }

    /**
     * 判断key是否存在
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function exists($key){
        return $this->mongo->count(array('key'=>$key));
    }


    /**
     * 获取key的生存时间
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function getLifetime($key){
        if($row = $this->findByKey($key , array('time'))){
        	$ttl = $row['time'] - time();
        	if($ttl < 0){
        		return -1;
        	}else{
        		return $ttl;
        	}
        }
        return -1;
    }

    /**
     * 通过key查询数据字段
     * @param  [type] $key   [description]
     * @param  array  $field [description]
     * @return [type]        [description]
     */
    private function findByKey($key , $field = array()){
    	// 检测查询字段
    	$field = empty($field) ? array('key','time','data') : $field;
    	return $this->mongo->findOne(array('key' => $key) , $field);
    }
}
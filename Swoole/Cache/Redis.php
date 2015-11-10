<?php
namespace Swoole\Cache;
use Swoole\Interfaces\Cache;
use Swoole\Base\CacheBase;
use Swoole\System\Console;

/**
 * redis 缓存
 */
class Redis extends CacheBase implements Cache{
    // 设置必要配置参数
    protected $must_config = array('host' => 1 , 'port' => 1 , 'auth' => 0 , 'lifetime' => 1);

    /**
     * 连接
     * @return [type] [description]
     */
    public function connect(){
        // 判断是否存在redis
        if (!class_exists('Redis')) {
            return Console::error("Class Redis not exists");
        }
        $this->cache_object = new \Redis();
        if ($this->cache_object->connect($this->config['host'], $this->config['port'])) {
            if (isset($this->config['auth']) && !empty($this->config['auth'])) {
                $this->cache_object->auth($this->config['auth']);
            }
        }else{
            return Console::error("Redis Connect fail");
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
        // 无生存时间写入
        if($lifetime && $lifetime <= 0){
            return $this->cache_object->set($key , $value);
        }else{
            $lifetime = $lifetime == null ? $this->config['lifetime'] : $lifetime;
            return $this->cache_object->setex($key , $lifetime , $value);
        }
    }

    /**
     * 获取一个key的value
     * @param  string $key 键名
     * @return mix    [string | array]      
     */
    public function get($key){
        if($value = $this->cache_object->get($key)){
            $content = json_decode($value , true);
            return $content == null ? $value : $content;
        }
        return false;
    }
    
    /**
     * 删除一个key
     * @param  string $key 键名
     * @return boolean     
     */
    public function delete($key){
        return $this->cache_object->del($key);
    }

    /**
     * 清空当前数据库中所有的key
     * @return boolean 
     */
    public function flush(){
        return $this->cache_object->flushdb();
    }

    /**
     * [exists description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function exists($key){
        return $this->cache_object->exists($key);
    }


    public function getLifetime($key){
        return $this->cache_object->ttl($key);
    }
}
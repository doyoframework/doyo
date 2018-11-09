<?php

namespace Engine;

class RedisEngine
{

    private $redis;

    function __construct()
    {

        $this->redis = new \Redis();

    }

    private $host;
    private $port;
    private $timeout;
    private $database;
    private $pconnect;
    private $password;

    public function connect($host, $port, $timeout, $database, $pconnect, $password)
    {

        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->database = $database;
        $this->pconnect = $pconnect;
        $this->password = $password;

        $this->reconnect();
    }

    public function reconnect()
    {
        if ($this->pconnect) {
            $this->redis->pconnect($this->host, $this->port, $this->timeout);
        } else {
            $this->redis->connect($this->host, $this->port, $this->timeout);
        }

        if (!empty($this->password)) {
            $this->redis->auth($this->password);
        }

        $this->redis->select($this->database);
    }

    public function ping()
    {
        return $this->redis->ping();
    }

    /**
     * 设置指定键名的数据
     *
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     * @return bool
     */
    public function set($key, $value, $expiration = 0)
    {

        if ($expiration > 0) {
            return $this->redis->setex($key, $expiration, $value);
        } else {
            return $this->redis->set($key, $value);
        }

    }

    /**
     * 设置多个键名的数据
     *
     * @param array $items
     *            <key => value>
     * @return bool
     */
    public function setMulti($items)
    {

        return $this->redis->mset($items);

    }

    /**
     * 获取指定键名的数据
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {

        return $this->redis->get($key);

    }

    /**
     * 获取指定键名序列的数据
     *
     * @param array $keys
     * @return array
     */
    public function getMulti($keys)
    {

        $values = $this->redis->getMultiple($keys);
        return array_combine($keys, $values);

    }

    /**
     * 增加指定键名的值并返回结果
     *
     * @param $key
     * @param int $step
     * @return int
     */
    public function increase($key, $step = 1)
    {

        return $this->redis->incrBy($key, $step);

    }

    /**
     * 减少指定键名的值并返回结果
     *
     * @param string $key
     * @param int $step
     * @return int
     */
    public function decrease($key, $step = 1)
    {

        return $this->redis->decrBy($key, $step);

    }

    /**
     * 设置指定键名的数据并返回原数据
     *
     * @param string $key
     * @param mixed $value
     * @return int
     */
    public function getSet($key, $value)
    {

        return $this->redis->getSet($key, $value);

    }

    /**
     * 查询所有的keys
     *
     * @param $pattern
     * @return array
     */
    public function keys($pattern)
    {

        return $this->redis->keys($pattern);

    }

    /**
     * 删除指定键名的数据
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {

        return $this->redis->delete($key);

    }

    /**
     * 判断指定键名是否存在
     *
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {

        return $this->redis->exists($key);

    }

    /**
     * 设置指定哈希指定属性的数据
     *
     * @param string $key
     * @param string $prop
     * @param mixed $value
     * @return bool
     */
    public function hashSet($key, $prop, $value)
    {

        return $this->redis->hSet($key, $prop, $value);

    }

    /**
     * 设置指定哈希多个属性的数据
     *
     * @param string $key
     * @param array $props
     *            <$prop => $value>
     * @return bool
     */
    public function hashSetMulti($key, $props)
    {

        return $this->redis->hMset($key, $props);

    }

    /**
     * 获取指定哈希指定属性的数据
     *
     * @param string $key
     * @param string $prop
     * @return mixed
     */
    public function hashGet($key, $prop)
    {

        return $this->redis->hGet($key, $prop);

    }

    /**
     * 获取指定哈希多个属性的数据
     *
     * @param string $key
     * @param array $props
     * @return array <$prop => $value>
     */
    public function hashGetMulti($key, $props)
    {

        return $this->redis->hMGet($key, $props);

    }

    /**
     * 删除指定哈希指定属性的数据
     *
     * @param string $key
     * @param string $prop
     * @return bool
     */
    public function hashDel($key, $prop)
    {

        return $this->redis->hDel($key, $prop);

    }

    /**
     * 获取指定哈希的长度
     *
     * @param string $key
     * @return int
     */
    public function hashLength($key)
    {

        return $this->redis->hLen($key);

    }

    /**
     * 获取指定哈希的所有属性
     *
     * @param string $key
     * @return array
     */
    public function hashProps($key)
    {

        return $this->redis->hKeys($key);

    }

    /**
     * 获取指定哈希的所有属性的值
     *
     * @param string $key
     * @return array
     */
    public function hashValues($key)
    {

        return $this->redis->hVals($key);

    }

    /**
     * 获取指定哈希的所有属性和值
     *
     * @param string $key
     * @return array
     */
    public function hashGetAll($key)
    {

        return $this->redis->hGetAll($key);

    }

    public function lpush($key, $val)
    {

        return $this->redis->lpush($key, $val);

    }

    public function lpop($key)
    {

        return $this->redis->lpop($key);

    }

    public function rpush($key, $val)
    {

        return $this->redis->rpush($key, $val);

    }

    public function rpop($key)
    {

        return $this->redis->rpop($key);

    }

    public function brpop($key, $timeout = 25)
    {

        return $this->redis->brPop($key, $timeout);

    }

    /**
     * 清空当前数据库
     *
     * @return bool
     */
    public function flushDB()
    {

        return $this->redis->flushDB();

    }

    /**
     * 获取服务器统计信息
     *
     * @return string
     */
    public function info()
    {

        return $this->redis->info();

    }

    /**
     * 获取服务器统计信息
     *
     * @return int
     */
    public function dbSize()
    {

        return $this->redis->dbSize();

    }

    /**
     * 设置过期时间（TTL）
     *
     * @param string $key
     * @param int $seconds
     * @return bool
     */
    public function expire($key, $seconds)
    {

        return $this->redis->expire($key, $seconds);

    }

    /**
     * 设置过期时间（TIMESTAMP）
     *
     * @param string $key
     * @param int $ts
     * @return bool
     */
    public function expireAt($key, $ts)
    {

        return $this->redis->expireAt($key, $ts);

    }

    /**
     * 排序集合：取某范围数据
     *
     * @param string $key
     * @param int $start
     *            [1, ~]
     * @param int $count
     * @param bool $withScores
     * @param bool $desc
     * @return array(id) array(id score)
     */
    public function sortRange($key, $start, $count, $withScores = false, $desc = true)
    {

        $start--;
        $end = $start + $count - 1;
        return ($desc ? $this->redis->zRevRange($key, $start, $end, $withScores) : $this->redis->zRange($key, $start, $end, $withScores));

    }

    /**
     * 排序集合：取指定成员的分数
     *
     * @param string $key
     * @param string $member
     * @return int
     */
    public function zScore($key, $member)
    {

        return $this->redis->zScore($key, $member);

    }

    /**
     * 排序集合：取指定成员的排名
     *
     * @param string $key
     * @param string $member
     * @return int [1, ~]
     */
    public function zRevRank($key, $member)
    {

        $rank = $this->redis->zRevRank($key, $member);
        return (is_numeric($rank) ? $rank + 1 : $rank);

    }

    /**
     * 排序集合：插入、更新指定成员的排名
     *
     * @param string $key
     * @param number $score
     * @param string $member
     * @return bool 是否新成员
     */
    public function zAdd($key, $score, $member)
    {

        return $this->redis->zAdd($key, $score, $member);

    }

    /**
     * 排序集合：取index从start到end的所以元素
     *
     * @param string $key
     * @param int $start
     * @param int $end
     * @param boolean $widthScores
     *
     * @return array
     */
    public function zRange($key, $start, $end, $widthScores = false)
    {

        return $this->redis->zRange($key, $start, $end, $widthScores);

    }

    /**
     * 排序集合：取index从start到end所有元素倒序
     *
     * @param string $key
     * @param int $start
     * @param int $end
     * @param boolean $widthScores
     *
     * @return array
     */
    public function zRevRange($key, $start, $end, $widthScores = false)
    {

        return $this->redis->zRevRange($key, $start, $end, $widthScores);

    }

    /**
     * 排序集合：删除元素
     *
     * @param $key
     * @param $member
     * @return int
     */
    public function zRem($key, $member)
    {

        return $this->redis->zRem($key, $member);

    }

    /**
     * 排序集合：集合大小
     *
     * @param $key
     */
    public function zSize($key)
    {

        $this->redis->zCard($key);

    }

    public function zCard($key)
    {

        return $this->redis->zCard($key);

    }

    public function zIncrBy($tags, $score, $key)
    {

        return $this->redis->zIncrBy($tags, $score, $key);

    }

    public function zRevRangeByScore($tags, $min, $max)
    {

        return $this->redis->zRevRangeByScore($tags, $min, $max);

    }

    public function zRangeByScore($tags, $min, $max)
    {

        return $this->redis->zRangeByScore($tags, $min, $max);

    }

    /**
     * 集合：add
     *
     * @param string $key
     * @param mixed $member
     * @return bool 是否新成员
     */
    public function setAdd($key, $member)
    {

        return $this->redis->sAdd($key, $member);

    }

    /**
     * 集合：remove
     *
     * @param string $key
     * @param mixed $member
     * @return bool 是否存在
     */
    public function setRem($key, $member)
    {

        return $this->redis->sRem($key, $member);

    }

    /**
     * 集合：random member
     *
     * @param string $key
     * @return string
     */
    public function setRandMember($key)
    {

        $member = $this->redis->sRandMember($key);
        return ($member ? $member : '');

    }

    /**
     * 集合：members
     *
     * @param string $key
     * @return array(string)
     */
    public function setMembers($key)
    {

        return $this->redis->sMembers($key);

    }

    /**
     * 集合：copy
     *
     * @param $keyDst
     * @param $key1
     * @param $key2
     * @return int
     */
    public function setCopy($keyDst, $key1, $key2)
    {

        return (int)$this->redis->sUnionStore($keyDst, $key1, $key2);

    }

    /**
     * 集合：元素是否存在
     *
     * @param $key
     * @param $value
     * @return bool
     */
    public function setIsMember($key, $value)
    {

        return $this->redis->sIsMember($key, $value);

    }

    /**
     * 集合：集合大小
     *
     * @param string $key
     *
     * @return int
     */
    public function setSize($key)
    {

        return $this->redis->sCard($key);

    }

}

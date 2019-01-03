<?php

namespace Core;

class BaseModel
{

    /**
     * 数据库
     *
     * @var \Engine\MySQLi
     *
     */
    private $mysql;

    /**
     * Redis
     *
     * @var \Engine\RedisEngine
     *
     */
    private $redis;

    /**
     * Entity
     *
     * @var BaseEntity
     */
    private $entity;

    /**
     * 表名称
     *
     * @var
     */
    private $ENTITY_NAME;

    /**
     * 初始参数
     *
     * @var
     */
    protected $PRIMARY_VAL;

    /**
     * 数据是否存在
     *
     * @var bool
     */
    public $exists = false;

    /**
     * 缓存时间
     * @var int
     */
    protected $cache = 0;

    /**
     * 临时赋值的变量集合
     *
     * @var array
     */
    private $__setter = array();

    /**
     * 最近一次查询结果集合
     *
     * @var array
     */
    private $__result = array();

    /**
     * 最近一次查询结果集合的副本
     *
     * @var array
     */
    private $__result_clone = array();


    /**
     * 继承的子类要用到的觉醒函数
     */
    public function __awake()
    {

    }

    /**
     * 继承的子类要用到的构造函数
     */
    public function __initialize()
    {

    }

    /**
     * BaseModel constructor.
     *
     * @param $entity_name
     * @param $primary_val
     * @throws \Exception\HTTPException
     */
    public final function __construct($entity_name, $primary_val)
    {

        $this->__awake();

        $this->ENTITY_NAME = $entity_name;

        $this->PRIMARY_VAL = $primary_val;

        if ($this->cache > 0 && !isset($GLOBALS['REDIS']['cache'])) {
            throw Util::HTTPException('cache need redis cache config.');
        }

        if (isset($GLOBALS['REDIS']['cache'])) {
            $this->redis = Util::loadRedis('cache');
        }

        $entity = APP_PATH . '/Entity/' . $this->ENTITY_NAME . '.php';

        if (file_exists($entity)) {
            $this->entity = Util::loadCls('Entity\\' . $this->ENTITY_NAME, $this->PRIMARY_VAL);

            $this->mysql = Util::loadCls('Engine\MySQLi');

            $this->mysql->connect($this->entity->DB_CONFIG);

            if ($this->PRIMARY_VAL) {
                $this->read($this->PRIMARY_VAL, $this->cache);
            }
        }

        $this->__initialize();

    }


    /**
     * 查询entity的key
     *
     * @param $key
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public final function __get($key)
    {
        // 必须是用array_key_exists，因为这个变量肯定没有被赋值，只是判断是否有这个变量
        if (array_key_exists($key, $this->entity)) {

            if (isset($this->__setter[$key])) {
                return $this->__setter[$key];
            }

            return $this->entity->$key;
        } else {
            throw Util::HTTPException('not get key: ' . $key);
        }
    }


    /**
     * 设置值
     *
     * @param $key
     * @param $val
     * @throws \Exception\HTTPException
     */
    public final function __set($key, $val)
    {

        // 必须是用array_key_exists，因为这个变量有可能没有被赋值，只是判断是否有这个变量
        if (array_key_exists($key, $this->entity)) {
            $type = '__' . $key . '__type';

            $ret = settype($val, $this->entity->$type);

            if (!$ret) {
                throw Util::HTTPException('set type error: ' . $this->entity->$type);
            }

            $this->__setter[$key] = $val;
            $this->entity->$key = $val;
        }

    }

    /**
     * 给实例赋值
     *
     * @param $data
     * @throws \Exception\HTTPException
     */
    public final function __setData($data)
    {

        foreach ($this->entity as $key => $value) {
            if (array_key_exists($key, $data)) {
                $type = '__' . $key . '__type';

                $ret = settype($data[$key], $this->entity->$type);

                if (!$ret) {
                    throw Util::HTTPException('set type error: ' . $this->entity->$type);
                }

                $this->__setter[$key] = $data[$key];
                $this->entity->$key = $data[$key];
            }
        }

    }


    /**
     * 返回实例
     */
    public final function __toData()
    {

        $data = array();

        foreach ($this->entity as $key => $value) {
            if (!in_array($key, ['DB_CONFIG', 'TABLE_PREFIX', 'PRIMARY_KEY', 'PRIMARY_VAL'])) {
                if ($key[0] == '_' && $key[1] == '_') {
                    continue;
                }
                $type = '__' . $key . '__type';

                settype($value, $this->entity->$type);

                $data[$key] = $value;
            }
        }

        return $data;

    }

    /**
     * 判断key是否在entity里面
     *
     * @param $key
     * @return bool
     */
    public final function key_exists($key)
    {
        return array_key_exists($key, $this->entity);
    }

    /**
     * 重置Entity
     *
     */
    public final function reset()
    {
        $this->__setter = [];
        $this->__result = [];
        $this->__result_clone = [];
        $this->entity = Util::loadCls('Entity\\' . $this->ENTITY_NAME, '', [], false);
    }

    /**
     * @param $primary_val
     * @param int $expires
     * @return array|mixed
     * @throws \Exception\HTTPException
     */
    public final function read($primary_val, $expires = 0)
    {

        //优先查询缓存
        $exists = $this->__load($primary_val);

        //缓存不存在，继续查询数据库
        if (!$exists) {
            $node = $this->node("where `{$this->entity->PRIMARY_KEY}` = '{$primary_val}'", '*', $expires);
            $this->__save();
        } else {
            $node = $this->__toData();
        }

        $this->PRIMARY_VAL = $primary_val;

        return $node;

    }


    /**
     * 根据索引删除一条数据
     *
     * @throws \Exception\HTTPException
     */
    public final function remove()
    {

        if ($this->entity->PRIMARY_VAL <= 0) {
            throw Util::HTTPException('primary_val not exists.');
        }

        $status = $this->delete("where `{$this->entity->PRIMARY_KEY}` = '{$this->entity->PRIMARY_VAL}'");

        //清空缓存
        if (isset($GLOBALS['DATABASE']) && isset($GLOBALS['DATABASE'][$this->entity->DB_CONFIG])) {
            $config = $GLOBALS['DATABASE'][$this->entity->DB_CONFIG];
            $entry = strtolower($this->ENTITY_NAME);
            if (isset($config['cache']) && isset($config['cache'][$entry])) {
                $key = $entry . '_' . $this->entity->PRIMARY_KEY . '_' . $this->entity->PRIMARY_VAL;
                $redis = Util::loadRedis('cache' . $entry, $config['cache'][$entry]);
                $redis->delete($key);
            }
        }

        $this->reset();

        return $status;

    }

    /**
     * 根据索引更新一条数据
     *
     * @throws \Exception\HTTPException
     */
    public final function alter()
    {

        if ($this->entity->PRIMARY_VAL <= 0) {
            throw Util::HTTPException('primary_val not exists.');
        }

        if (empty($this->__setter)) {
            throw Util::HTTPException('data is null.');
        }

        $status = $this->update("where `{$this->entity->PRIMARY_KEY}` = '{$this->entity->PRIMARY_VAL}'", $this->__setter);

        $this->__save();

        $this->__setter = array();

        return $status;

    }

    /**
     * 返回查询的结果行数
     *
     * @return int
     */
    public final function rows()
    {
        return count($this->__result_clone);
    }

    /**
     * 循环读取数据
     *
     * @return bool
     */
    public final function next()
    {

        $node = array_shift($this->__result);

        if (empty($node)) {
            $this->__result = $this->__result_clone;
            return false;
        }

        foreach ($this->entity as $key => $val) {
            if (isset($node[$key])) {
                $this->entity->$key = $node[$key];
            } else if ($key == 'PRIMARY_KEY' && isset($node[$this->entity->PRIMARY_KEY])) {
                $this->entity->PRIMARY_VAL = $node[$this->entity->PRIMARY_KEY];
            }
        }

        return true;

    }

    /**
     * @throws \Exception\HTTPException
     */
    public final function begin()
    {
        $this->mysql->begin();
    }

    /**
     * @throws \Exception\HTTPException
     */
    public final function commit()
    {
        $this->mysql->commit();
    }

    /**
     * @throws \Exception\HTTPException
     */
    public final function rollback()
    {
        $this->mysql->rollback();
    }

    /**
     * @param bool $setter
     * @throws \Exception\HTTPException
     */
    public final function __save($setter = true)
    {
        if (!isset($GLOBALS['DATABASE']) || !isset($GLOBALS['DATABASE'][$this->entity->DB_CONFIG])) {
            return;
        }

        $config = $GLOBALS['DATABASE'][$this->entity->DB_CONFIG];
        $entry = strtolower($this->ENTITY_NAME);

        if (!isset($config['cache']) || !isset($config['cache'][$entry])) {
            return;
        }

        if (!$this->entity->PRIMARY_VAL) {
            return;
        }

        $key = $entry . '_' . $this->entity->PRIMARY_KEY . '_' . $this->entity->PRIMARY_VAL;

        $redis = Util::loadRedis('cache' . $entry, $config['cache'][$entry]);

        if ($setter) {
            $redis->hashSetMulti($key, $this->__setter);
        } else {
            $redis->hashSetMulti($key, $this->__toData());
        }
    }

    /**
     * @param $primary_val
     * @return bool
     * @throws \Exception\HTTPException
     */
    public final function __load($primary_val)
    {
        if (!isset($GLOBALS['DATABASE']) || !isset($GLOBALS['DATABASE'][$this->entity->DB_CONFIG])) {
            return false;
        }

        $config = $GLOBALS['DATABASE'][$this->entity->DB_CONFIG];
        $entry = strtolower($this->ENTITY_NAME);
        if (!isset($config['cache']) || !isset($config['cache'][$entry])) {
            return false;
        }

        if (!$primary_val) {
            return false;
        }

        $key = $entry . '_' . $this->entity->PRIMARY_KEY . '_' . $primary_val;

        $redis = Util::loadRedis('cache' . $entry, $config['cache'][$entry]);

        $this->exists = $redis->exists($key);

        if (!$this->exists) {
            return false;
        }

        $data = $redis->hashGetAll($key);

        foreach ($this->entity as $k => $v) {
            if (isset($data[$k])) {
                $this->entity->$k = $data[$k];
            }
        }

        $this->entity->PRIMARY_VAL = $primary_val;

        return true;
    }

    /**
     * 清空表（慎用）
     *
     * @param bool $truncate
     * @throws \Exception\HTTPException
     */
    public final function truncate($truncate = true)
    {

        $table = strtolower($this->entity->TABLE_PREFIX . $this->ENTITY_NAME);

        if ($truncate) {
            $this->mysql->query("truncate {$table};");
        } else {
            $this->delete();
        }

    }

    /**
     * @param $sql
     * @param int $mode
     * @return array|bool|\mysqli_result|null
     * @throws \Exception\HTTPException
     */
    public final function query($sql, $mode = MYSQL_QUERY_FETCH)
    {

        $sql = trim($sql);

        $operate = substr($sql, 0, 6);

        if (strtolower($operate) != 'select') {
            throw Util::HTTPException('only use select statement.');
        }

        if (stristr($sql, 'delete ')) {
            throw Util::HTTPException('don`t use delete statement.');
        }

        if (stristr($sql, 'update ')) {
            throw Util::HTTPException('don`t use update statement.');
        }

        if (stristr($sql, 'drop ')) {
            throw Util::HTTPException('don`t use drop statement.');
        }

        if (stristr($sql, 'replace ')) {
            throw Util::HTTPException('don`t use replace statement.');
        }

        $res = $this->mysql->query($sql);

        if ($mode == MYSQL_QUERY_FETCH) {
            $data = array();

            $num = $res->num_rows;
            for ($i = 0; $i < $num; $i++) {
                array_push($data, $res->fetch_assoc());
            }
            $res->free_result();

            return $data;

        } else if ($mode == MYSQL_QUERY_RESULT) {

            return $res;
        }

        return null;

    }

    /**
     * 增加一条数据
     *
     * @param array $array
     * @return array|int
     * @throws \Exception\HTTPException
     */
    public final function insert($array = array())
    {

        $table = strtolower($this->entity->TABLE_PREFIX . $this->ENTITY_NAME);

        if (empty($array) && $this->__setter) {
            $array = $this->__setter;
        }

        if (empty($array)) {
            throw Util::HTTPException('data is null.');
        }

        $insert_id = $this->mysql->insert($table, $array);

        if ($insert_id > 0) {
            $key = $this->entity->PRIMARY_KEY;
            $this->entity->$key = $insert_id;
            $this->entity->PRIMARY_VAL = $insert_id;
            $this->__save();
        }

        $this->__setter = array();

        //新增数据，清空缓存
        $this->flushDB();

        return $insert_id;
    }

    /**
     * @param $where
     * @param $array
     * @return int
     * @throws \Exception\HTTPException
     */
    public final function update($where, $array)
    {
        if (is_array($where)) {
            $where = implode(' ', $where);
        }

        $table = strtolower($this->entity->TABLE_PREFIX . $this->ENTITY_NAME);

        $ret = $this->mysql->update($table, $array, $where);

        //更新数据，清空缓存
        $this->flushDB();

        return $ret;
    }

    /**
     * 清空缓存
     */
    public final function flushDB()
    {
        if ($this->redis && $this->cache > 0) {
            $hash_key = strtoupper($this->ENTITY_NAME) . '_SQL';
            $list = $this->redis->hashGetAll($hash_key);
            foreach ($list as $k => $v) {
                $this->redis->delete($k);
            }
            $list = null;
            $this->redis->delete($hash_key);
        }
    }

    /**
     * @param string $where
     * @return int
     * @throws \Exception\HTTPException
     */
    public final function delete($where = 'where 1 = 1')
    {

        if (is_array($where)) {
            $where = implode(' ', $where);
        }

        $table = strtolower($this->entity->TABLE_PREFIX . $this->ENTITY_NAME);

        $ret = $this->mysql->delete($table, $where);

        //删除数据，清空缓存
        $this->flushDB();

        return $ret;
    }

    /**
     * @param $where
     * @param $field
     * @param $expires
     * @return mixed
     * @throws \Exception\HTTPException
     */
    public final function field($where, $field, $expires = 0)
    {

        if (is_array($where)) {
            $where = implode(' ', $where);
        }

        $node = $this->node($where, $field, $expires);

        if ($node) {
            return array_pop($node);
        }

        return false;
    }

    /**
     * @param $where
     * @return mixed
     * @throws \Exception\HTTPException
     */
    public final function count($where = "where 1 = 1")
    {
        if (is_array($where)) {
            $where = implode(' ', $where);
        }

        $node = $this->node($where, "count(*) as total", $this->cache);

        return $node['total'];
    }

    /**
     * @param string $where
     * @param $field
     * @return mixed
     * @throws \Exception\HTTPException
     */
    public final function sum($where = "where 1 = 1", $field)
    {
        if (is_array($where)) {
            $where = implode(' ', $where);
        }

        $node = $this->node($where, "sum(`{$field}`) as count", $this->cache);

        if (!isset($node['count'])) {
            return 0;
        }

        return $node['count'];
    }

    /**
     * @param $where
     * @param string $field
     * @param int $expires
     * @return array|mixed
     * @throws \Exception\HTTPException
     */
    public final function node($where, $field = '*', $expires = 0)
    {

        if (is_array($where)) {
            $where = implode(' ', $where);
        }

        $table = strtolower($this->entity->TABLE_PREFIX . $this->ENTITY_NAME);

        $cache_key = md5(md5($table) . md5($where) . md5($field));

        if ($expires == 0 && $this->cache > 0) {
            $expires = $this->cache;
        }

        $exists = $this->redis && $expires > 0 && $this->redis->exists($cache_key);

        if ($exists) {
            $node = json_decode($this->redis->get($cache_key), true);
        } else {
            $node = $this->mysql->node($table, $where, $field);
        }

        if (!empty($node)) {

            if ($this->redis && $expires > 0) {
                $this->redis->zIncrBy(strtoupper($this->ENTITY_NAME) . '_SQL_COUNT', 1, $cache_key);
                if (!$exists) {
                    $this->redis->hashSet(strtoupper($this->ENTITY_NAME) . '_SQL', $cache_key, $this->mysql->last_query_sql());
                    $this->redis->set($cache_key, json_encode($node, JSON_UNESCAPED_UNICODE), $expires);
                }
            }

            foreach ($this->entity as $key => $val) {
                if (isset($node[$key])) {
                    $this->entity->$key = $node[$key];
                } else if ($key == 'PRIMARY_KEY' && isset($node[$this->entity->PRIMARY_KEY])) {
                    $this->entity->PRIMARY_VAL = $node[$this->entity->PRIMARY_KEY];
                }
            }

            $this->exists = true;

        } else {

            $this->exists = false;

            $this->reset();

        }

        return $node;

    }

    /**
     * @param $where
     * @param $field
     * @param $limit
     * @param $page
     * @param bool $offset
     * @param string $order
     * @return array
     * @throws \Exception\HTTPException
     */
    public final function publish($where, $field, $limit, $page, $offset = false, $order = '')
    {

        if (is_array($where)) {
            $where = implode(' ', $where);
        }

        $table = strtolower($this->entity->TABLE_PREFIX . $this->ENTITY_NAME);

        $where = trim($where);

        if (strtolower(substr($where, 0, 5)) == 'where') {
            $where = substr($where, 5);
        }

        if ($order != '') {
            $order = $order . ',';
        }

        if (strtolower(substr($where, 0, 5)) == 'inner') {
            $where = substr($where, 5);

            $time = time();

            $where = "where `status` >= 1 and ({$time} >= `s_dateline` and {$time} <= `e_dateline`) and {$where} order by `location` desc, {$order} `s_dateline` desc, `id` desc";
        } else {
            $where = "where {$where} order by {$order} `id` desc";
        }

        if (!is_numeric($page)) {
            return array();
        }

        if (strpos(strtolower($where), ' group by ')) {

            $sql = "select count(*) as `rcount` from (select count(*) as rcoun from `{$table}` {$where}) as _tmp_count_table_;";
        } else {

            $sql = "select count(*) as `rcount` from `{$table}` {$where};";
        }

        $res = $this->mysql->query($sql);
        $row = $res->fetch_assoc();
        $rcount = $row['rcount'];
        $res->free_result();

        // pcount
        $pcount = ceil($rcount / $limit);

        if ($page <= 1) {
            $page = 1;
        } else if ($page >= $pcount) {
            $page = $pcount;
        }

        $next = $page + 1;
        $prev = $page - 1;

        if ($next >= $pcount) {
            $next = $pcount;
        }

        if ($prev <= 1) {
            $prev = 1;
        }

        $_offset = (($page - 1) * $limit) + $offset;

        $_limit = $_offset . ', ' . $limit;

        $sql = "select {$field} from `{$table}` {$where} limit {$_limit};";

        $res = $this->mysql->query($sql);

        $data = array();

        $num = $res->num_rows;
        for ($i = 0; $i < $num; $i++) {
            array_push($data, $res->fetch_assoc());
        }
        $res->free_result();

        $array = array();
        $array['data'] = $data;
        $array['limit'] = $limit;
        $array['page'] = $page;
        $array['rcount'] = $rcount;
        $array['pcount'] = $pcount;
        $array['next'] = $next;
        $array['prev'] = $prev;

        $this->__result = $data;
        $this->__result_clone = $data;

        return $array;

    }

    /**
     * @param string $where
     * @param string $field
     * @param bool $limit
     * @param bool $page
     * @param int $offset
     * @return array
     * @throws \Exception\HTTPException
     */
    public final function select($where = 'where 1 = 1', $field = '*', $limit = false, $page = false, $offset = 0)
    {

        if (is_array($where)) {
            $where = implode(' ', $where);
        }

        $table = strtolower($this->entity->TABLE_PREFIX . $this->ENTITY_NAME);

        $cache_key = md5($table . $where . $field . $limit . $page . $offset);

        $exists = $this->redis && $this->cache > 0 && $this->redis->exists($cache_key);

        if ($exists) {
            $data = json_decode($this->redis->get($cache_key), true);
        } else {
            $data = $this->mysql->select($table, $where, $field, $limit, $page, $offset);
        }

        if ($this->redis && $this->cache > 0) {
            $this->redis->zIncrBy(strtoupper($this->ENTITY_NAME) . '_SQL_COUNT', 1, $cache_key);
            if (!$exists) {
                $this->redis->hashSet(strtoupper($this->ENTITY_NAME) . '_SQL', $cache_key, $this->mysql->last_query_sql());
                $this->redis->set($cache_key, json_encode($data, JSON_UNESCAPED_UNICODE), $this->cache);
            }
        }

        if ($page) {
            $this->__result = $data['data'];
            $this->__result_clone = $data['data'];
        } else {
            $this->__result = $data;
            $this->__result_clone = $data;
        }

        return $data;

    }

    /**
     * @param $tab
     * @param $on
     * @param bool $where
     * @param string $field
     * @param bool $limit
     * @param bool $page
     * @param int $offset
     * @return array
     * @throws \Exception\HTTPException
     */
    public final function right($tab, $on, $where = false, $field = '*', $limit = false, $page = false, $offset = 0)
    {

        if (is_array($where)) {
            $where = implode(' ', $where);
        }

        $tableA = strtolower($this->entity->TABLE_PREFIX . $this->ENTITY_NAME);

        $entry = Util::loadCls('Entity\\' . ucfirst($tab));

        if ($entry->DB_CONFIG != $this->entity->DB_CONFIG) {
            throw Util::HTTPException('right database not same.');
        }

        $tableB = strtolower($entry->TABLE_PREFIX . trim($tab));

        $data = $this->mysql->unite($tableA, $tableB, 'right', $on, $where, $field, $limit, $page, $offset);

        if ($page) {
            $this->__result = $data['data'];
            $this->__result_clone = $data['data'];
        } else {
            $this->__result = $data;
            $this->__result_clone = $data;
        }

        return $data;

    }

    /**
     * @param $tab
     * @param $on
     * @param bool $where
     * @param string $field
     * @param bool $limit
     * @param bool $page
     * @param int $offset
     * @return array
     * @throws \Exception\HTTPException
     */
    public final function left($tab, $on, $where = false, $field = '*', $limit = false, $page = false, $offset = 0)
    {

        if (is_array($where)) {
            $where = implode(' ', $where);
        }

        $tableA = strtolower($this->entity->TABLE_PREFIX . $this->ENTITY_NAME);

        $entry = Util::loadCls('Entity\\' . ucfirst($tab));

        if ($entry->DB_CONFIG != $this->entity->DB_CONFIG) {
            throw Util::HTTPException('left database not same.');
        }

        $tableB = strtolower($entry->TABLE_PREFIX . trim($tab));

        $data = $this->mysql->unite($tableA, $tableB, 'left', $on, $where, $field, $limit, $page, $offset);

        if ($page) {
            $this->__result = $data['data'];
            $this->__result_clone = $data['data'];
        } else {
            $this->__result = $data;
            $this->__result_clone = $data;
        }

        return $data;

    }


    /**
     * @param $tab
     * @param $on
     * @param bool $where
     * @param string $field
     * @param bool $limit
     * @param bool $page
     * @param int $offset
     * @return array
     * @throws \Exception\HTTPException
     */
    public final function inner($tab, $on, $where = false, $field = '*', $limit = false, $page = false, $offset = 0)
    {

        if (is_array($where)) {
            $where = implode(' ', $where);
        }

        $tableA = strtolower($this->entity->TABLE_PREFIX . $this->ENTITY_NAME);

        $entry = Util::loadCls('Entity\\' . ucfirst($tab));

        if ($entry->DB_CONFIG != $this->entity->DB_CONFIG) {
            throw Util::HTTPException('inner database not same.');
        }

        $tableB = strtolower($entry->TABLE_PREFIX . trim($tab));

        $data = $this->mysql->unite($tableA, $tableB, 'inner', $on, $where, $field, $limit, $page, $offset);

        if ($page) {
            $this->__result = $data['data'];
            $this->__result_clone = $data['data'];
        } else {
            $this->__result = $data;
            $this->__result_clone = $data;
        }

        return $data;

    }

    /**
     * @param string $field
     * @return array
     * @throws \Exception\HTTPException
     */
    public final function show_fields($field = '*')
    {

        $table = strtolower($this->entity->TABLE_PREFIX . $this->ENTITY_NAME);
        return $this->mysql->show_fields($table, $field);

    }

    /**
     * @param string $field
     * @return array
     * @throws \Exception\HTTPException
     */
    public final function show_tables($field = '*')
    {

        return $this->mysql->show_tables($field);

    }

    /**
     * @return mixed
     * @throws \Exception\HTTPException
     */
    public final function show_primary_key()
    {

        $table = strtolower($this->entity->TABLE_PREFIX . $this->ENTITY_NAME);

        return $this->mysql->show_primary_key($table);

    }

}
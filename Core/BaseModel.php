<?php
namespace Core;

use Core\Util;

class BaseModel {

    /**
     * id
     *
     * @var int
     */
    private $id = 0;

    /**
     * 数据库
     *
     * @var \Engine\MySQLi
     *
     */
    private $db;

    /**
     * Redis
     *
     * @var \Engine\RedisEngine
     *
     */
    private $cache;

    /**
     * Entity
     *
     * @var Object
     */
    private $entity;

    /**
     * 表名称
     *
     * @var
     */
    private $ENTITY_NAME;

    /**
     * 数据是否存在
     *
     * @var bool
     */
    public $exists = false;

    /**
     * 临时赋值的变量集合
     *
     * @var array
     */
    private $__setter = array ();

    /**
     * 最近一次查询结果集合
     *
     * @var array
     */
    private $__result = array ();

    /**
     * 最近一次查询结果集合的副本
     *
     * @var array
     */
    private $__result_clone = array ();

    /**
     * BaseModel constructor.
     * @param $entity_name
     * @param $id
     * @throws \Exception\HTTPException
     */
    public final function __construct($entity_name, $id) {

        $this->ENTITY_NAME = $entity_name;
        
        $this->db = Util::loadCls('Engine\MySQLi');
        $this->db->connect(DB_HOST, DB_USER, DB_PSWD, DB_NAME, DB_PORT, CHARSET, 'false');
        
        if (isset($GLOBALS['REDIS']['cache'])) {
            $this->cache = Util::loadRedis('cache');
        }
        
        $entity = APP_PATH . '/Entity/' . $this->ENTITY_NAME . '.php';
        
        if (file_exists($entity)) {
            $this->entity = Util::loadCls('Entity\\' . $this->ENTITY_NAME, $id);
        }
        
        if ($id) {
            $this->read($id);
        }
        
        $this->__initialize();
    
    }

    /**
     * 继承的子类要用到的构造函数
     */
    public function __initialize() {

    }

    /**
     * 查询entity的key
     *
     * @param $key
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public final function __get($key) {
        
        // 必须是用array_key_exists，因为这个变量肯定没有被赋值，只是判断是否有这个变量
        if (array_key_exists($key, $this->entity)) {
            
            if ($this->id <= 0) {
                return false;
            }
            
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
     */
    public final function __set($key, $val) {
        
        // 必须是用array_key_exists，因为这个变量肯定没有被赋值，只是判断是否有这个变量
        if (array_key_exists($key, $this->entity)) {
            $this->__setter[$key] = $val;
        }
    
    }

    /**
     * 返回实例
     */
    public final function __toData() {

        return (array) $this->entity;
    
    }

    /**
     * 根据索引查询一条数据
     *
     * @param $primary_val
     * @param string $field
     * @return \Entity
     */
    public final function read($primary_val, $field = '*') {

        $this->id = $primary_val;

        $primary_key = $this->entity->PRIMARY_KEY;
        
        $node = $this->node("where `{$primary_key}` = '{$primary_val}'", $field);
        
        return $node;
    
    }

    /**
     * 根据索引删除一条数据
     *
     * @param bool $primary_val
     * @throws \Exception\HTTPException
     */
    public final function remove($primary_val = false) {

        $primary_key = $this->entity->PRIMARY_KEY;
        
        if (!$primary_val) {
            $primary_val = $this->entity->$primary_key;
        }
        
        if (empty($primary_val)) {
            throw Util::HTTPException('primary_val not exists.');
        }
        
        $status = $this->delete("where `{$primary_key}` = '{$primary_val}'");
        
        $this->__setter = array ();
        
        return $status;
    
    }

    /**
     * 根据索引更新一条数据
     *
     * @param bool $primary_val
     * @param bool $array
     * @throws \Exception\HTTPException
     */
    public final function alter($primary_val = false, $array = false) {

        $primary_key = $this->entity->PRIMARY_KEY;
        
        if (!$primary_val) {
            $primary_val = $this->entity->$primary_key;
        }
        
        if (empty($primary_val)) {
            throw Util::HTTPException('primary_val not exists.');
        }
        
        if (!$array && $this->__setter) {
            $array = $this->__setter;
        }
        
        if (!$array || !is_array($array)) {
            throw Util::HTTPException('array is null.');
        }
        
        $status = $this->update("where `{$primary_key}` = '{$primary_val}'", $array);
        
        foreach ( $array as $k => $v ) {
            $this->entity->$k = $v;
        }
        
        $this->__setter = array ();
        
        return $status;
    
    }

    /**
     * 循环读取数据
     *
     * @return bool
     */
    public final function next() {

        $node = array_shift($this->__result);
        
        if (empty($node)) {
            $this->__result = $this->__result_clone;
            return false;
        }
        
        foreach ( $this->entity as $key => $val ) {
            if ($key != 'PRIMARY_KEY' && isset($node[$key])) {
                $this->entity->$key = $node[$key];
            }
        }
        return true;
    
    }

    /**
     * 清空表（慎用）
     */
    public final function truncate() {

        $table = strtolower(DB_DATA_PREFIX . $this->ENTITY_NAME);
        
        $this->db->query("truncate {$table};");
    
    }

    /**
     * 直接执行一个sql
     *
     * @param $sql
     * @param int $mode
     * @return array|\mysqli_result|null
     */
    public final function query($sql, $mode = MYSQL_QUERY_FETCH) {

        $res = $this->db->query($sql);
        
        if ($mode == MYSQL_QUERY_FETCH) {
            $data = array ();
            
            $num = $res->num_rows;
            for($i = 0; $i < $num; $i++) {
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
     * @param bool $array
     * @return array
     * @throws \Exception\HTTPException
     */
    public final function insert($array = false) {

        $table = strtolower(DB_DATA_PREFIX . $this->ENTITY_NAME);
        
        if (!$array && $this->__setter) {
            $array = $this->__setter;
        }
        
        if (!$array) {
            throw Util::HTTPException('array is null.');
        }
        
        $this->__setter = array ();
        
        return $this->db->insert($table, $array);
    
    }

    /**
     * 更新数据
     *
     * @param $where
     * @param $array
     */
    public final function update($where, $array) {

        $table = strtolower(DB_DATA_PREFIX . $this->ENTITY_NAME);
        
        return $this->db->update($table, $array, $where);
    
    }

    /**
     * 删除数据
     *
     * @param $where
     */
    public final function delete($where) {

        $table = strtolower(DB_DATA_PREFIX . $this->ENTITY_NAME);
        
        return $this->db->delete($table, $where);
    
    }

    /**
     * 根据条件查询一个字段
     *
     * @param $where
     * @param $field
     * @return mixed
     */
    public final function field($where, $field) {

        $node = $this->node($where);
        
        return $node[$field];
    
    }

    /**
     * 根据条件查询一条数据
     *
     * @param $where
     * @param string $field
     * @param bool $cache
     * @return array|mixed
     */
    public final function node($where, $field = '*', $cache = true) {

        $table = strtolower(DB_DATA_PREFIX . $this->ENTITY_NAME);
        
        $ckey = md5($table . '-' . $where . '-' . $field);
        
        if (isset($GLOBALS['REDIS']['cache']) && $cache) {
            if ($this->cache->exists($ckey)) {
                $node = json_decode($this->cache->get($ckey), true);
            } else {
                $node = $this->db->node($table, $where, $field);
                $this->cache->set($ckey, json_encode($node, true), 5);
            }
        } else {
            $node = $this->db->node($table, $where, $field);
        }
        
        if ($node && isset($node[$this->entity->PRIMARY_KEY])) {
            $this->id = $node[$this->entity->PRIMARY_KEY];
            foreach ( $this->entity as $key => $val ) {
                if ($key != 'PRIMARY_KEY' && isset($node[$key])) {
                    $this->entity->$key = $node[$key];
                }
            }
            $this->exists = true;
        } else {
            $this->exists = false;
        }
        
        return $node;
    
    }

    /**
     * 查询发布的内容
     *
     * @param $where
     * @param $field
     * @param $limit
     * @param $page
     * @param bool $offset
     * @param string $order
     * @return array
     */
    public final function publish($where, $field, $limit, $page, $offset = false, $order = '') {

        $table = strtolower(DB_DATA_PREFIX . $this->ENTITY_NAME);
        $this->__result = array ();
        $this->__result_clone = array ();
        
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
        
        $res = $this->db->query($sql);
        $row = $res->fetch_assoc();
        $rcount = $row['rcount'];
        $res->free_result();
        
        // pcount
        $pcount = ceil($rcount / $limit);
        
        $_PAGE = $page;
        
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
        
        $res = $this->db->query($sql);
        
        $data = array ();
        
        $num = $res->num_rows;
        for($i = 0; $i < $num; $i++) {
            array_push($data, $res->fetch_assoc());
        }
        $res->free_result();
        
        $array = array ();
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
     * 查询
     *
     * @param string $where
     * @param string $field
     * @param bool $limit
     * @param bool $page
     * @param int $offset
     * @return array
     */
    public final function select($where = 'where 1 = 1', $field = '*', $limit = false, $page = false, $offset = 0) {

        $table = strtolower(DB_DATA_PREFIX . $this->ENTITY_NAME);
        $this->__result = array ();
        $this->__result_clone = array ();
        
        $data = $this->db->select($table, $where, $field, $limit, $page, $offset);
        
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
     * 查询两个表
     *
     * @param $tab
     * @param $where
     * @param string $field
     * @param bool $limit
     * @return array
     */
    public final function with($tab, $where, $field = '*', $limit = false) {

        $tableA = strtolower(DB_DATA_PREFIX . $this->ENTITY_NAME);
        $tableB = strtolower(DB_DATA_PREFIX . $tab);
        
        if ($limit) {
            $limit = "limit {$limit}";
        }
        
        $sql = "select {$field} from `{$tableA}` a, `{$tableB}` b {$where} {$limit};";
        
        $res = $this->db->query($sql);
        
        $data = array ();
        
        $len = $res->num_rows;
        
        for($i = 0; $i < $len; $i++) {
            $data[] = $res->fetch_assoc();
        }
        
        $res->free_result();
        
        $this->__result = $data;
        $this->__result_clone = $data;
        
        return $data;
    
    }

    /**
     * @param $tab
     * @param $on
     * @param bool $where
     * @param string $field
     * @param bool $limit
     * @return array
     */
    public final function right($tab, $on, $where = false, $field = '*', $limit = false) {

        $tableA = strtolower(DB_DATA_PREFIX . $this->ENTITY_NAME);
        $tableB = strtolower(DB_DATA_PREFIX . $tab);
        
        if ($limit) {
            $limit = "limit {$limit}";
        }
        if ($where) {
            $where = "where {$where}";
        }
        
        $sql = "select {$field} from `{$tableA}` a right join `{$tableB}` b on {$on} {$where} {$limit};";
        
        $res = $this->db->query($sql);
        
        $data = array ();
        
        $len = $res->num_rows;
        
        for($i = 0; $i < $len; $i++) {
            $data[] = $res->fetch_assoc();
        }
        
        $res->free_result();
        
        $this->__result = $data;
        $this->__result_clone = $data;
        
        return $data;
    
    }

    /**
     * @param $tab
     * @param $on
     * @param bool $where
     * @param string $field
     * @param bool $limit
     * @return array
     */
    public final function left($tab, $on, $where = false, $field = '*', $limit = false) {

        $tableA = strtolower(DB_DATA_PREFIX . $this->ENTITY_NAME);
        $tableB = strtolower(DB_DATA_PREFIX . $tab);
        
        if ($limit) {
            $limit = "limit {$limit}";
        }
        
        if ($where) {
            $where = "where {$where}";
        }
        
        $sql = "select {$field} from `{$tableA}` a left join `{$tableB}` b on {$on} {$where} {$limit};";
        
        $res = $this->db->query($sql);
        
        $data = array ();
        
        $len = $res->num_rows;
        
        for($i = 0; $i < $len; $i++) {
            $data[] = $res->fetch_assoc();
        }
        
        $res->free_result();
        
        $this->__result = $data;
        $this->__result_clone = $data;
        
        return $data;
    
    }

    /**
     * 查询当前表内的字段
     *
     * @param string $field
     * @return array
     */
    public final function show_fields($field = '*') {

        $table = strtolower(DB_DATA_PREFIX . $this->ENTITY_NAME);
        return $this->db->show_fields($table, $field);
    
    }

    /**
     * 查询所有的表
     *
     * @param string $field
     * @return array
     */
    public final function show_tables($field = '*') {

        return $this->db->show_tables($field);
    
    }

    /**
     * 查询当前表的索引
     * @return mixed
     */
    public final function show_primary_key() {

        $table = strtolower(DB_DATA_PREFIX . $this->ENTITY_NAME);
        
        return $this->db->show_primary_key($table);
    
    }

}
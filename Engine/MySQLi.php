<?php

namespace Engine;

use Core\Util;

define('MYSQL_QUERY_RESULT', 1);

define('MYSQL_QUERY_FETCH', 2);

class MySQLi
{

    private $_host;

    private $_user;

    private $_password;

    private $_database;

    private $_port;

    private $_charset;

    private $_pconnect;

    private $_sql;

    private $_config;

    /**
     *
     * @var \mysqli
     *
     *
     */
    private $mysql;

    /**
     * @param $db_config
     *
     * @return bool
     */
    public function connect($db_config)
    {
        $this->_config = $db_config;

        $conf = $GLOBALS['DATABASE'][$db_config];

        $this->_host = $conf['host'];
        $this->_user = $conf['user'];
        $this->_password = $conf['password'];
        $this->_database = $conf['database'];
        $this->_port = $conf['port'];
        $this->_charset = $conf['charset'];
        $this->_pconnect = $conf['pconnect'];

        if ($this->_pconnect) {
            $this->_host = 'p:' . $this->_host;
        }

        $this->mysql = new \mysqli($this->_host, $this->_user, $this->_password, $this->_database, $this->_port);

        if (mysqli_connect_errno()) {
            trigger_error('Mysqli connect failed: ' . mysqli_connect_error());
            return false;
        }

        if ($this->_charset) {
            $this->mysql->set_charset($this->_charset);
            $charset = str_replace('-', '', $this->_charset);
            $this->mysql->query("set names `{$charset}`");
        }

        return true;
    }

    /**
     * 检查数据库连接,是否有效，无效则重新建立
     *
     * @return bool
     */
    public function checkConnection()
    {

        if (!@$this->mysql->ping()) {
            $this->mysql->close();
            return $this->connect($this->_config);
        }

        return true;

    }

    /**
     * @param $sql
     * @return bool|\mysqli_result
     * @throws \Exception\HTTPException
     */
    public function query($sql)
    {

        $this->_sql = $sql;

        file_put_contents(SQL_LOG_PATH, $sql . "\n", FILE_APPEND);

        $res = $this->mysql->query($sql);
        if ($this->mysql->more_results()) {
            $this->mysql->next_result();
        }

        if ($this->mysql->errno) {
            throw Util::HTTPException("网络错误，请联系管理员。", -1, [$this->mysql->error, $this->_sql]);
        }

        return $res;

    }

    public function last_query_sql()
    {
        return $this->_sql;
    }

    /**
     * @param $table
     * @param $where
     * @param string $field
     * @param bool $limit
     * @param bool $page
     * @param int $offset
     * @return array
     * @throws \Exception\HTTPException
     */
    public function select($table, $where, $field = '*', $limit = false, $page = false, $offset = 0)
    {

        if (empty($page)) {
            $data = array();

            if ($limit) {
                $limit = "limit {$limit}";
            }

            $sql = "select {$field} from `{$table}` a {$where} {$limit};";

            $res = $this->query($sql);

            $len = $res->num_rows;

            for ($i = 0; $i < $len; $i++) {
                $data[] = $res->fetch_assoc();
            }

            $res->free_result();

            return $data;
        } else {

            $group_where = strtolower($where);

            if (strpos($group_where, ' group by ')) {
                $sql = "select count(*) as `rcount` from (select count(*) as rcount from `{$table}` a {$where}) as _tmp_count_table_;";
            } else {
                $sql = "select count(*) as `rcount` from `{$table}` a {$where};";
            }

            $res = $this->query($sql);

            $row = $res->fetch_assoc();

            $rcount = $row['rcount'];

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

            $_limit = $_offset . ", " . $limit;

            $dataArray = $this->select($table, $where, $field, $_limit);

            $array = array();
            $array['data'] = $dataArray;
            $array['limit'] = $limit;
            $array['page'] = $page;
            $array['rcount'] = intval($rcount);
            $array['pcount'] = $pcount;
            $array['next'] = $next;
            $array['prev'] = $prev;

            return $array;
        }

    }

    /**
     * @param $tableA
     * @param $tableB
     * @param $method
     * @param $on
     * @param $where
     * @param $field
     * @param $limit
     * @param $page
     * @param $offset
     * @return array
     * @throws \Exception\HTTPException
     */
    public function unite($tableA, $tableB, $method, $on, $where, $field = '*', $limit = false, $page = false, $offset = 0)
    {

        $data = array();
        if (empty($page)) {
            if ($limit) {
                $limit = "limit {$limit}";
            }

            if ($where) {
                $where = "where {$where}";
            }

            $sql = "select {$field} from `{$tableA}` a {$method} join `{$tableB}` b on {$on} {$where} {$limit};";

            $res = $this->query($sql);

            $len = $res->num_rows;

            for ($i = 0; $i < $len; $i++) {
                $data[] = $res->fetch_assoc();
            }

            $res->free_result();

            return $data;
        } else {

            $group_where = strtolower($where);

            if (strpos($group_where, ' group by ')) {
                $sql = "select count(*) as `rcount` from (select count(*) as rcount from `{$tableA}` a {$method} join `{$tableB}` b on {$on} where {$where}) as _tmp_count_table_;";
            } else {
                $sql = "select count(*) as `rcount` from `{$tableA}` a {$method} join `{$tableB}` b on {$on} where {$where};";
            }

            $res = $this->query($sql);

            $row = $res->fetch_assoc();

            $rcount = $row['rcount'];

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

            $_limit = $_offset . ", " . $limit;

            $dataArray = $this->unite($tableA, $tableB, $method, $on, $where, $field, $_limit);

            $array = array();
            $array['data'] = $dataArray;
            $array['limit'] = $limit;
            $array['page'] = $page;
            $array['rcount'] = $rcount;
            $array['pcount'] = $pcount;
            $array['next'] = $next;
            $array['prev'] = $prev;

            return $array;

        }

    }

    /**
     * @param $table
     * @param $array
     * @return int|string
     * @throws \Exception\HTTPException
     */
    public function insert($table, $array)
    {

        $key = array();
        $val = array();
        $va2 = array();

        foreach ($array as $k => $v) {
            if (isset($v)) {
                $key[] = "`{$k}`";
                $val[] = "?";
                $va2[] = "{$v}";
            }
        }

        $filedKey = implode(",", $key);
        $filedVal = implode(",", $val);

        $sql = "insert into `{$table}` ({$filedKey}) values ({$filedVal});";

        file_put_contents(SQL_LOG_PATH, $sql . "\n", FILE_APPEND);

        $stmt = $this->mysql->prepare($sql);

        $stype = str_repeat('s', count($va2));

        array_unshift($va2, $stype);

        $refs = array();
        foreach ($va2 as $o => $value) {
            $refs[$o] = &$va2[$o];
        }

        call_user_func_array(array(
            $stmt,
            "bind_param"
        ), $refs);

        if ($stmt->execute()) {
            $insert_id = $stmt->insert_id;
            if ($insert_id === 0) {
                $insert_id = $stmt->id;
            }
        } else {
            $insert_id = -10002;
        }

        if ($this->mysql->errno) {
            throw Util::HTTPException($this->mysql->error, $this->_sql);
        }

        $stmt->close();

        return $insert_id;

    }

    /**
     * @param $table
     * @param $where
     * @return int
     * @throws \Exception\HTTPException
     */
    public function delete($table, $where)
    {

        $sql = "delete from `{$table}` {$where};";

        $this->query($sql);

        return $this->mysql->affected_rows;

    }

    /**
     * @param $table
     * @param $array
     * @param $where
     * @return int
     * @throws \Exception\HTTPException
     */
    public function update($table, $array, $where)
    {

        if (is_array($array)) {

            $set = array();
            $val = array();

            foreach ($array as $key => $value) {
                if (isset($value)) {
                    array_push($set, "`{$key}` = ? ");
                    $val[] = $value;
                }
            }

            $set = implode(',', $set);

            $sql = "update `{$table}` set {$set} {$where};";

            $stmt = $this->mysql->prepare($sql);

            $type = str_repeat('s', count($val));

            array_unshift($val, $type);

            $refs = array();
            foreach ($val as $o => $value) {
                $refs[$o] = &$val[$o];
            }

            call_user_func_array(array(
                $stmt,
                "bind_param"
            ), $refs);

            $stmt->execute();

            if ($this->mysql->errno) {
                throw Util::HTTPException($this->mysql->error, $this->_sql);
            }

        } else {
            $sql = "update `{$table}` set {$array} {$where};";

            $this->query($sql);
        }

        return $this->mysql->affected_rows;

    }

    /**
     * @param $table
     * @param $where
     * @param string $field
     * @return array
     * @throws \Exception\HTTPException
     */
    public function node($table, $where, $field = '*')
    {

        $sql = "select {$field} from `{$table}` {$where} limit 1;";

        $res = $this->query($sql);

        $data = $res->fetch_assoc();

        $res->free_result();

        return $data;

    }

    /**
     * @param string $filed
     * @return array
     * @throws \Exception\HTTPException
     */
    public function show_tables($filed = 'TABLE_NAME')
    {

        $sql = "SELECT {$filed} FROM INFORMATION_SCHEMA.TABLES WHERE `table_schema` = '{$this->_database}';";

        $res = $this->query($sql);

        $data = array();

        $len = $res->num_rows;

        for ($i = 0; $i < $len; $i++) {
            $data[] = $res->fetch_assoc();
        }

        $res->free_result();

        return $data;

    }

    /**
     * @param $table
     * @param string $field
     * @return array
     * @throws \Exception\HTTPException
     */
    public function show_fields($table, $field = 'COLUMN_NAME')
    {

        $sql = "SELECT {$field} FROM INFORMATION_SCHEMA.COLUMNS WHERE `table_schema` = '{$this->_database}' and `table_name` = '{$table}';";

        $res = $this->query($sql);

        $data = array();

        $len = $res->num_rows;

        for ($i = 0; $i < $len; $i++) {
            $data[] = $res->fetch_assoc();
        }

        $res->free_result();

        return $data;

    }

    /**
     * @param $table
     * @return mixed
     * @throws \Exception\HTTPException
     */
    public function show_primary_key($table)
    {

        $sql = "SELECT `COLUMN_NAME` FROM INFORMATION_SCHEMA.COLUMNS WHERE `table_schema` = '{$this->_database}' and `table_name` = '{$table}' and `column_key` = 'PRI';";

        $res = $this->query($sql);

        $row = $res->fetch_assoc();

        $res->free_result();

        return $row['COLUMN_NAME'];

    }

}

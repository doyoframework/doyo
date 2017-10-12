<?php
namespace Engine;

define('MYSQL_QUERY_RESULT', 1);

define('MYSQL_QUERY_FETCH', 2);

class MySQLi {

    private $_host;

    private $_user;

    private $_password;

    private $_database;

    private $_port;

    private $_charset;

    private $_pconnect;

    /**
     *
     * @var \mysqli
     *
     *
     */
    private $mysql;

    public function connect($host, $user, $password, $database, $port, $charset = false, $pconnect = false) {

        $this->_host = $host;
        $this->_user = $user;
        $this->_password = $password;
        $this->_database = $database;
        $this->_port = $port;
        $this->_charset = $charset;
        $this->_pconnect = $pconnect;
        
        if ($this->_pconnect) {
            $host = 'p:' . $host;
        }
        
        $this->mysql = new \mysqli($host, $this->_user, $this->_password, $this->_database, $this->_port);
        
        if (mysqli_connect_errno()) {
            trigger_error('Mysqli connect failed: ' . mysqli_connect_error());
            return false;
        }
        
        if ($this->_charset) {
            $this->mysql->set_charset($this->_charset);
            $charset = str_replace('-', '', $this->_charset);
            $this->mysql->query('set names `{$charset}`');
        }
    
    }

    /**
     * 检查数据库连接,是否有效，无效则重新建立
     */
    private function checkConnection() {

        if (!@$this->mysql->ping()) {
            $this->mysql->close();
            return $this->connect($this->_host, $this->_user, $this->_password, $this->_database, $this->_port, $this->_charset, $this->_pconnect);
        }
        
        return true;
    
    }

    /**
     *
     * @return \mysqli_result
     *
     */
    public function query($sql) {

        file_put_contents(SQL_LOG_PATH, $sql . "\n", FILE_APPEND);
        
        $res = $this->mysql->query($sql);
        if ($this->mysql->more_results()) {
            $this->mysql->next_result();
        }
        return $res;
    
    }

    /**
     *
     * @return array
     *
     */
    public function select($table, $where, $field = '*', $limit = false, $page = false, $offset = 0) {

        $data = array ();
        
        if (empty($page)) {
            
            if ($limit) {
                $limit = "limit {$limit}";
            }
            
            $sql = "select {$field} from `{$table}` {$where} {$limit};";
            
            $res = $this->query($sql);
            
            $len = $res->num_rows;
            
            for($i = 0; $i < $len; $i++) {
                $data[] = $res->fetch_assoc();
            }
            
            $res->free_result();
            
            return $data;
        } else {
            
            $group_where = strtolower($where);
            
            if (strpos($group_where, ' group by ')) {
                
                $sql = "select count(*) as `rcount` from (select count(*) as rcoun from `{$table}` {$where}) as _tmp_count_table_;";
            } else {
                
                $sql = "select count(*) as `rcount` from `{$table}` {$where};";
            }
            
            $res = $this->query($sql);
            
            $row = $res->fetch_assoc();
            
            $rcount = $row['rcount'];
            
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
            
            $_limit = $_offset . ", " . $limit;
            
            $dataArray = $this->select($table, $where, $field, $_limit);
            
            $array = array ();
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
     *
     * @return array
     *
     */
    public function insert($table, $array) {

        $key = array ();
        $val = array ();
        $va2 = array ();
        
        foreach ( $array as $k => $v ) {
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
        
        $refs = array ();
        foreach ( $va2 as $o => $value ) {
            $refs[$o] = &$va2[$o];
        }
        
        call_user_func_array(array (
            $stmt, 
            "bind_param" 
        ), $refs);
        
        $insert_id = -10001;
        
        if ($stmt->execute()) {
            $insert_id = $stmt->insert_id;
            if ($insert_id === 0) {
                $insert_id = $stmt->id;
            }
        } else {
            $insert_id = -10002;
        }
        
        $stmt->close();
        
        return $insert_id;
    
    }

    /**
     *
     * @return void
     *
     */
    public function delete($table, $where) {

        $sql = "delete from `{$table}` {$where};";
        
        $this->query($sql);
        
        return $this->mysql->affected_rows;
    
    }

    /**
     *
     * @return void
     *
     */
    public function update($table, $array, $where) {

        if (is_array($array)) {
            
            $set = array ();
            $val = array ();
            
            foreach ( $array as $key => $value ) {
                if (isset($value)) {
                    array_push($set, "`{$key}` = ? ");
                    $val[] = "{$value}";
                }
            }
            
            $set = implode(',', $set);
            
            $sql = "update `{$table}` set {$set} {$where};";
            
            $stmt = $this->mysql->prepare($sql);
            
            $stype = str_repeat('s', count($val));
            
            array_unshift($val, $stype);
            
            $refs = array ();
            foreach ( $val as $o => $value ) {
                $refs[$o] = &$val[$o];
            }
            
            call_user_func_array(array (
                $stmt, 
                "bind_param" 
            ), $refs);
            
            $stmt->execute();
        } else {
            $sql = "update `{$table}` set {$array} {$where};";
            
            $this->query($sql);
        }
        
        return $this->mysql->affected_rows;
    
    }

    /**
     *
     * @return array
     *
     */
    public function node($table, $where, $field = '*') {

        $sql = "select {$field} from `{$table}` {$where} limit 1;";
        
        $res = $this->query($sql);
        
        $data = $res->fetch_assoc();
        
        $res->free_result();
        
        return $data;
    
    }

    /**
     * 查询数据库表
     *
     * @param string $table
     *            数据表名称
     *            
     * @return array
     * @access public
     *        
     */
    public function show_tables($filed = 'TABLE_NAME') {

        $sql = "SELECT {$filed} FROM INFORMATION_SCHEMA.TABLES  WHERE `table_schema` = '" . $this->_database . "';";
        
        $res = $this->query($sql);
        
        $data = array ();
        
        $len = $res->num_rows;
        
        for($i = 0; $i < $len; $i++) {
            $data[] = $res->fetch_assoc();
        }
        
        $res->free_result();
        
        return $data;
    
    }

    /**
     * 查询数据库表字段
     *
     * @param string $table            
     *
     * @return array
     * @access public
     *        
     */
    public function show_fields($table, $field = 'COLUMN_NAME') {

        $sql = "SELECT {$field} FROM INFORMATION_SCHEMA.COLUMNS WHERE `table_schema` = '" . $this->_database . "' and `table_name` = '{$table}';";
        
        $res = $this->query($sql);
        
        $data = array ();
        
        $len = $res->num_rows;
        
        for($i = 0; $i < $len; $i++) {
            $data[] = $res->fetch_assoc();
        }
        
        $res->free_result();
        
        return $data;
    
    }

    /**
     * 查询主索引
     */
    public function show_primary_key($table) {

        $sql = "SELECT `COLUMN_NAME` FROM INFORMATION_SCHEMA.COLUMNS WHERE `table_schema` = '" . $this->_database . "' and `table_name` = '{$table}' and `column_key` = 'PRI';";
        
        $res = $this->query($sql);
        
        $row = $res->fetch_assoc();
        
        $res->free_result();
        
        return $row['COLUMN_NAME'];
    
    }

}

?>
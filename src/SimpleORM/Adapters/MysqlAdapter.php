<?php

namespace SimpleORM\Adapters;

class MysqlAdapter extends Adapter
{
    protected $_link;
    protected $_result;    

    public function connect()
    {
        // connect only once
        if ($this->_link === null) {
            if (empty($this->host)) throw new \InvalidArgumentException("Database host not specified.");
            if (empty($this->user)) throw new \InvalidArgumentException("Database user not specified.");
            if (empty($this->database)) throw new \InvalidArgumentException("Database name not specified.");
            
            if (!$this->_link = @mysqli_connect($this->host, $this->user, $this->password, $this->database)) {
                throw new RuntimeException('Error connecting to the server : ' . mysqli_connect_error());
            }
            $this->query('SET NAMES '.$this->charset);
        }
        return $this->_link;
    }

    static public function getInstance(array $config){
        if(!self::$instance){
            self::$instance = new MysqlAdapter($config);
        }
        return self::$instance;
    }

    public function query($query)
    {
        if (!is_string($query) || empty($query)) {
            throw new InvalidArgumentException('The specified query is not valid.');
        }

        // lazy connect to MySQL
        $this->connect();
        if (!$this->_result = mysqli_query($this->_link, $query)) {
            throw new \RuntimeException('Error executing the specified query ' . $query . mysqli_error($this->_link));
        }
        return $this->_result;
    }

    public function select($table, $where = '', $fields = '*', $order = '', $limit = null, $offset = null, $count_all = false)
    {
        $query = 'SELECT '. ($count_all ? 'SQL_CALC_FOUND_ROWS ' : '') . $fields . ' FROM ' . $table
               . (($where) ? ' WHERE ' . $where : '')
               . (($order) ? ' ORDER BY ' . $order : '')
               . (($limit) ? ' LIMIT ' . $limit : '')
               . (($offset && $limit) ? ' OFFSET ' . $offset : '');

        $this->query($query);
        return $this->countRows();
    }

    public function countAll($table, $where = '')
    {
        $query = 'SELECT count(*) FROM ' . $table
               . (($where) ? ' WHERE ' . $where : '');
               
        $this->query($query);
        return array_pop($this->fetch());  
    }

    public function insert($table, array $data)
    {
        $fields = implode(',', array_keys($data));
        $values = implode(',', array_map(array($this, 'quoteValue'), array_values($data)));
        $query = 'INSERT INTO ' . $table . ' (' . $fields . ') ' . ' VALUES (' . $values . ')';
        $this->query($query);
        return $this->getInsertId();
    }

    public function update($table, array $data, $where = '')
    {
        $set = array();
        foreach ($data as $field => $value) {
            $set[] = $field . '=' . $this->quoteValue($value);
        }
        $set = implode(',', $set);
        $query = 'UPDATE ' . $table . ' SET ' . $set
               . (($where) ? ' WHERE ' . $where : '');
        $this->query($query);
        return $this->getAffectedRows();
    }

    public function insertUpdate($table, array $data, array $keys)
    {
        $fields = implode(',', array_keys($data));
        $values = implode(',', array_map(array($this, 'quoteValue'), array_values($data)));
        foreach ($data as $field => $value) {
            if (!in_array($field, $keys))
                $set[] = $field . '=' . $this->quoteValue($value);
        }
        $set = implode(',', $set);

        $query = 'INSERT INTO ' . $table . ' (' . $fields . ') ' . ' VALUES (' . $values . ')
                    ON DUPLICATE KEY UPDATE '.$set;
        $ok = $this->query($query);
        return ($this->getInsertId() ? $this->getInsertId() : $ok);
    }

    public function delete($table, $where = '')
    {
        $query = 'DELETE FROM ' . $table
               . (($where) ? ' WHERE ' . $where : '');
        $this->query($query);
        return $this->getAffectedRows();
    }

    public function quoteValue($value)
    {
        $this->connect();
        if ($value === null) {
            $value = 'NULL';
        }
        // else if (!is_numeric($value)) {
            $value = "'" . $this->escape($value) . "'";
        // }
        return $value;
    }

    public function escape($value)
    {
        return mysqli_real_escape_string($this->_link, $value);
    }

    public function foundRows()
    {
        $this->query('SELECT FOUND_ROWS()');
        return array_pop($this->fetch());
    }

    /**
     * Fetch a single row from the current result set (as an associative array)
     */
    public function fetch()
    {
        if ($this->_result !== null) {
            if (($row = mysqli_fetch_array($this->_result, MYSQLI_ASSOC)) === false) {
                $this->freeResult();
            }
            return $row;
        }
        return false;
    }

    public function fetchAll()
    {
        if ($numRows = $this->countRows()){
            for ($i = 0; $i<$numRows; $i++){
                $data[] = $this->fetch();
            }
            return $data;
        }
        return [];
    }

    /**
     * Get the insertion ID
     */
    public function getInsertId()
    {
        return $this->_link !== null
            ? mysqli_insert_id($this->_link) : null;
    }

    /**
     * Get the number of rows returned by the current result set
     */
    public function countRows()
    {
        return $this->_result !== null
            ? mysqli_num_rows($this->_result) : 0;
    }

    /**
     * Get the number of affected rows
     */
    public function getAffectedRows()
    {
        return $this->_link !== null
            ? mysqli_affected_rows($this->_link) : 0;
    }

    /**
     * Free up the current result set
     */
    public function freeResult()
    {
        if ($this->_result === null) {
            return false;
        }
        mysqli_free_result($this->_result);
        return true;
    }

    /**
     * Close explicitly the database connection
     */
    public function disconnect()
    {
        if ($this->_link === null) {
            return false;
        }
        mysqli_close($this->_link);
        $this->_link = null;
        return true;
    }

    /**
     * Close automatically the database connection when the instance of the class is destroyed
     */
    public function __destruct()
    {
        $this->disconnect();
    }
}

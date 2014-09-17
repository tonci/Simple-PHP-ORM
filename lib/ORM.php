<?php
class ORM {
    private $_adapter;
    protected $_entityTable;
    private $_fieldsDetails;
    public function __construct()
    {
        $this->_adapter = new MysqlAdapter(array('localhost', 'root', '', 'chaos'));
        $this->explinTable();
    }

    public function __get($name)
    {
        if (isset($this->fields->{$name})) {
            return $this->fields->{$name};
        }
        return null;
    }

    public function __set($name, $value)
    {
        if (isset($this->fields->{$name})) {
            $this->fields->{$name} = $value;
        }else{
            $this->{$name} = $value;
        }
    }

    public function findById($id)
    {
        $this->_adapter->select($this->_entityTable, "id = $id");
        if ($data = $this->_adapter->fetch()) {
            return $data;
        }
        return null;
    }

    public function find($query='',$values='')
    {   
        $this->_adapter->select($this->_entityTable, $this->matchValues($query,$values));
        if ($data = $this->_adapter->fetch()) {
            return $data;
        }
        return null;
    }

    public function findAll($query='', $values='')
    {
        $this->_adapter->select($this->_entityTable, $this->matchValues($query,$values));
        if ($numRows = $this->_adapter->countRows()){
            for ($i = 0; $i<$numRows; $i++)
                $data[] = $this->_adapter->fetch();
            return $data;
        }
                    
        return null;
        
    }

    public function matchValues($query, $values)
    {
        foreach ((array)$values as $key => $value) {
            $query = str_replace($key, $this->_adapter->quoteValue($value), $query);
        }
        return $query;
    }

    public function explinTable()
    {
        $this->_adapter->query('EXPLAIN '.$this->_entityTable);
        if ($numRows = $this->_adapter->countRows()){
            $this->fields = new stdClass;
            for ($i = 0; $i<$numRows; $i++){
                $row = $this->_adapter->fetch();
                $this->_fieldsDetails[$row['Field']] = $row;
                $this->fields->$row['Field'] = '';
            }
        }
    }
}
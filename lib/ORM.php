<?php
class ORM {
    private $_adapter;
    protected $_entityTable;
    public function __construct()
    {
        $this->_adapter = new MysqlAdapter(array('localhost', 'root', '', 'chaos'));
    }
    public function findById($id)
    {
        $this->_adapter->select($this->_entityTable, "id = $id");
        if ($data = $this->_adapter->fetch()) {
            return $data;
        }
        return null;
    }

    public function find($query,$values='')
    {   
        $this->_adapter->select($this->_entityTable, $this->matchValues($query,$values));
        if ($data = $this->_adapter->fetch()) {
            return $data;
        }
        return null;
    }

    public function matchValues($query, $values)
    {
        foreach ($values as $key => $value) {
            $query = str_replace($key, $this->_adapter->quoteValue($value), $query);
        }
        return $query;
    }
}
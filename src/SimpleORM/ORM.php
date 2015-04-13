<?php

namespace SimpleORM;

use SimpleORM\Adapters;
use SimpleORM\Validators;

class ORM {
    private $_adapter;
    protected $_entityTable;
    protected $_fieldsDetails;
    public $fields;
    public $errors;

    public function __construct()
    {
        //$this->_adapter = new MysqlAdapter(array('localhost', 'phpdev', 'phpdev@mysql', 'chaos_tasks'));
        // $this->_adapter = MysqlAdapter::getInstance(array('localhost', 'phpdev', 'phpdev@mysql', 'chaos_tasks')); //Chaos DB
        $this->_adapter = Adapters\MysqlAdapter::getInstance(array('localhost', 'root', '', 'chaos')); //local DB
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

    public function __call($name, $arguments)
    {
        $search_fields = explode('and', strtolower(str_replace('findBy', '', $name)));
        for ($i=0; $i < count($search_fields); $i++) {
            $criterias[] = $this->_adapter->escape($search_fields[$i]).'='.$this->_adapter->quoteValue($arguments[$i]);
        }

        if (!empty($criterias)) {
            return $this->findAll(implode(' AND ', $criterias));
        }
        return null;
    }

    public function find($query='',$values='', $order = '', $limit = null, $offset = null)
    {
        if ($limit) $limit = (int)$limit;
        if ($offset) $offset = (int)$offset;
        $this->_adapter->select($this->_entityTable, $this->matchValues($query,$values), '*', $this->_adapter->escape($order), $limit, $offset);
        if ($data = $this->_adapter->fetch()) {
            $this->mapValues($data);
            return $data;
        }
        return null;
    }

    public function findAll($query='', $values='', $order = '', $limit = null, $offset = null)
    {
        if ($limit) $limit = (int)$limit;
        if ($offset) $offset = (int)$offset;
        $this->_adapter->select($this->_entityTable, $this->matchValues($query,$values), '*', $this->_adapter->escape($order), $limit, $offset);
        $results = $this->_adapter->fetchAll();

        if ($results) {
            $index = 0;
            $class_name = get_class($this);
            foreach ($results as $result) {
                $data[$index] = new $class_name;
                $data[$index]->mapValues($result);
                $index++;
            }
            return $data;
        }

        return [];
    }

    public function delete($where='',$values='')
    {
        $PK = $this->getPK();
        if (empty($where)) {
            foreach ($PK as $pki) {
                if (!$this->{$pki}) return false;
                $where[] = $pki.'='.$this->{$pki};
            }
            return $this->_adapter->delete($this->_entityTable,implode(' AND ', $where));
        }

        if (is_int($where)) {
            return $this->_adapter->delete($this->_entityTable,$PK[0].'='.$where);
        }elseif (is_string($where)){
            return $this->_adapter->delete($this->_entityTable,$this->matchValues($where,$values));
        }
    }

    public function save()
    {
        if ($this->validate()) {
            return $this->_adapter->insertUpdate($this->_entityTable, (array)$this->fields, (array)$this->getPK());
        }

        return false;
    }

    public function validate()
    {

        $Validator = new DBValidator($this, $this->_fieldsDetails);
        $Validator->validate();
        return empty($this->errors);
    }

    // TODO custome rules that should applly to validation
    public function rules()
    {
        return array();
    }

    public function getPK()
    {
        return array_keys(array_filter($this->_fieldsDetails, array($this, 'filterPK')));
    }

    public function filterPK($row)
    {
        return $row['Key'] == 'PRI';
    }

    public function mapValues($values)
    {
        foreach ($values as $key => $value) {
            if (isset($this->fields->{$key})){
                $this->fields->{$key} = $value;
            }
        }
    }

    public function matchValues($query, $values)
    {
        foreach ((array)$values as $key => $value) {
            if(preg_match('/^:.+$/', $key))
                $query = str_replace($key, $this->_adapter->quoteValue($value), $query);
        }

        return $query;
    }

    public function addError($attribute, $errors)
    {    
        foreach ((array)$errors as $error_message) {
            $this->errors[$attribute][] = $error_message;
        }
    
    }

    public function removeErrors($attribute=null)
    {
        if ($attribute){
            unset($this->errors[$attribute]);
        }else{
            $this->errors = array();
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function explinTable()
    {
        $this->_adapter->query('EXPLAIN '.$this->_entityTable);
        if ($numRows = $this->_adapter->countRows()){
            $this->fields = new \stdClass;
            for ($i = 0; $i<$numRows; $i++){
                $row = $this->_adapter->fetch();
                $this->_fieldsDetails[$row['Field']] = $row;
                $this->fields->$row['Field'] = '';
            }
        }
    }
}

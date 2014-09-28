<?php 
class Component {
    public $errors;

    public function addError($object, $attribute, $error)
    {
        $object->errors[$attribute][md5($error)] = $error;
    }
}
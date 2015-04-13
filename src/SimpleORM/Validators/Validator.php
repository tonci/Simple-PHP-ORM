<?php

namespace SimpleORM\Validators;

abstract class Validator{

    public $errors;

    public static $builtInValidators=array(
        'date' => 'DateValidator',
        'email'=>'EmailValidator',
        'string'=>'StringValidator',
        'numerical'=>'NumberValidator',
    );

    abstract protected function validateAttribute($value);

    public function createValidator($value='')
    {
        // Default create validator function (for non db related validation)
    }
    public function addError($error_string)
    {
        $this->errors[] = $error_string;
    }

    public function removeErrors()
    {
        $this->errors = array();
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
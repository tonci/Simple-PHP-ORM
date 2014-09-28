<?php
abstract class Validator{

    public static $builtInValidators=array(
        'required'=>'RequiredValidator',
        'email'=>'EmailValidator',
        'length'=>'StringValidator',
        'numerical'=>'NumberValidator',
    );

    abstract protected function validateAttribute($value, $options);

    public function createValidator($value='')
    {
        
    }    
}
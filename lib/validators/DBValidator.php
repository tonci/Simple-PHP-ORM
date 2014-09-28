<?php
class DBValidator extends Validator {

    public static $DBBuiltInValidators=array(
        'smallint'=>'NumberValidator',
        'int'=>'NumberValidator',
        'bigint'=>'NumberValidator',
        'decimal'=>'NumberValidator',

        'char'=>'StringValidator',
        'varchar'=>'StringValidator',
        'text'=>'StringValidator',
    );

    public function __construct($attributesDetails = null)
    {
        if ($attributesDetails) {
            $this->createValidators($attributesDetails);
        }
    }

    public function validateAttribute($value, $options){

    }

    public function createValidators($attributesDetails)
    {
        $validators = array();
        foreach ($attributesDetails as $name => $attributes) {
            preg_match('/^(?P<type>\w+)\(?(?P<limit>\d+)?,?(\d+)?\)?/', $attributes['Type'], $type_properties);
            if (!empty($type_properties)) {
                $validators[$name][] = new self::$DBBuiltInValidators[$type_properties['type']];
            }
        }
        print_r($validators);
    }
}
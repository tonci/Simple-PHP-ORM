<?php
class DBValidator extends Validator {

    private $object;
    private $attributesDetails;

    public static $DBBuiltInValidators=array(
        'tinyint'=>'NumberValidator',
        'smallint'=>'NumberValidator',
        'mediumint'=>'NumberValidator',
        'int'=>'NumberValidator',
        'bigint'=>'NumberValidator',
        'decimal'=>'NumberValidator',
        'flaot'=>'NumberValidator',
        'double'=>'NumberValidator',
        'real'=>'NumberValidator',

        'char'=>'StringValidator',
        'varchar'=>'StringValidator',
        'tinytext'=>'StringValidator',
        'text'=>'StringValidator',
        'mediumtext'=>'StringValidator',
        'longtext'=>'StringValidator',

        'date' => 'DateValidator',
    );

    private static $textLimits = array(
        'tinytext' => 255,
        'text' => 65535,
        'mediumtext' => 16777215,
        'longtext' => 4294967295,
    );

    public function __construct($object, $attributesDetails)
    {
        $this->object = $object;
        $this->attributesDetails = $attributesDetails;
    }

    public function validateAttribute($value){

    }

    public function validate()
    {
        $validators = $this->createValidators();
        foreach ($validators as $attribute_name => $attribute_validators) {
            foreach ($attribute_validators as $validator) {
                if (!$validator->validateAttribute($this->object->$attribute_name)) {
                    $this->object->addError($attribute_name,$validator->getErrors());
                }
            }
        }   
    }

    public function createValidators()
    {
        $validators = array();
        foreach ($this->attributesDetails as $name => $attributes) {
            preg_match('/^(?P<type>\w+)\(?(?P<limit>\d+)?,?(\d+)?\)?/', $attributes['Type'], $type_properties);
            if (!empty($type_properties)) {
                $validator = new self::$DBBuiltInValidators[strtolower($type_properties['type'])];
                
                //Empty restriction
                if (strtolower($attributes['Null']) == 'no' && strpos($attributes['Extra'], 'auto_increment') === false) {
                    $validator->allowEmpty = false;
                }

                //Max restriction for char types
                if (in_array(strtolower($type_properties['type']), array('char', 'varchar')) && is_numeric($type_properties['limit'])) {
                    $validator->max = $type_properties['limit'];
                }

                // Integer only validation
                if (in_array(strtolower($type_properties['type']), array('tinyint', 'smallint', 'mediumint', 'int', 'bigint'))) {
                    $validator->integerOnly = true;
                    //TODO add min/max restrictions (depends on type and "unsigned")
                }

                //just an example for using email validator
                if (strpos($attributes['Field'], 'email') !== false) {
                    $validators[$name][] = new EmailValidator;
                }

                $validators[$name][] = $validator;
            }
        }

        return $validators;
    }
}
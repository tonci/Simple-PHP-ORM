<?php
class StringValidator extends Validator {

    public $allowEmpty=true;

    public $max;

    public function validateAttribute($attribute)
    {

        if ($this->allowEmpty && empty($attribute)) {
            return true;
        }elseif (!$this->allowEmpty && empty($attribute)){
            $this->addError("Attribute should not be empty");
            return false;
        }

        $length = strlen(utf8_decode($attribute));

        if($this->max!==null && $length>$this->max){
            $this->addError("Too long (maximum is {$this->max} characters).");    
            return false;
        }

        if (!is_string($attribute)) {
            $this->addError('Not a string');
            return false;
        }
        return true;
    }
}
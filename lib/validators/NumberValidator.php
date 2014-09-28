<?php
class NumberValidator extends Validator {

    public $allowEmpty=true;

    public $integerOnly=false;

    public $max;

    public $integerPattern='/^\s*[+-]?\d+\s*$/';

    public $numberPattern='/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/';

    public function validateAttribute($attribute)
    {
        if ($this->allowEmpty && empty($attribute)) {
            return true;
        }

        if($this->integerOnly)
        {
            if(!preg_match($this->integerPattern,"$attribute"))
            {
                $this->addError('Not an Integer value');
                return false;
            }
        }
        else
        {
            if(!preg_match($this->numberPattern,"$attribute"))
            {
                $this->addError('Not a number');
                return false;
            }
        }

        return true;
    }
}
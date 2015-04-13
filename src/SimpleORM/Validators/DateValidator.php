<?php

namespace SimpleORM\Validators;

class DateValidator extends Validator {

    public $allowEmpty=true;

    public function validateAttribute($attribute)
    {

        if ($this->allowEmpty && empty($attribute)) {
            return true;
        }

        if (!preg_match('/^(19|20)\d{2}([-\/.])(0[1-9]|1[012])\2(0[1-9]|[12][0-9]|3[01])$/', $attribute)) {
            $this->addError('Not a valid date');
            return false;
        }
        return true;
    }
}
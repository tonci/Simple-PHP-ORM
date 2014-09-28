<?php
class EmailValidator extends Validator {

    public function validateAttribute($attribute)
    {

        //regex taken from http://www.w3.org/TR/html5/forms.html#valid-e-mail-address
        if (!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/", $attribute)) {
            $this->addError('Not a valid Email address');
            return false;
        }
        return true;
    }
}
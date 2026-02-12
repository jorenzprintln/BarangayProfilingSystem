<?php

class Validator
{
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function required($field)
    {
        return !empty(trim($field));
    }

    public static function minLength($field, $length)
    {
        return strlen($field) > $length;
    }

    public static function matches($field1, $field2)
    {
        return $field1 === $field2;
    }
}

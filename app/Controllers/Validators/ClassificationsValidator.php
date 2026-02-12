<?php

class ClassificationsValidator
{
    public function createValidate($data)
    {
        $errors = [];

        if (empty($data['code'])) {
            $errors[] = 'Code is required.';
        }

        if (empty($data['name'])) {
            $errors[] = 'Name is required.';
        }

        return $errors;
    }
}

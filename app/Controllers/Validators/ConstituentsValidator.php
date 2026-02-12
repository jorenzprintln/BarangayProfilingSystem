<?php

class ConstituentsValidator
{
    public function createValidate($data)
    {
        $errors = [];

        if (empty($data['last_name'])) {
            $errors[] = 'Last name is required.';
        }

        if (empty($data['first_name'])) {
            $errors[] = 'First name is required.';
        }

        if (empty($data['birthdate'])) {
            $errors[] = 'Birthdate is required.';
        }

        if (empty($data['education_attainment'])) {
            $errors[] = 'Education attainment is required.';
        }

        return $errors;
    }

    public function updateValidate($data)
    {
        $errors = [];

        if (empty($data['psn'])) {
            $errors[] = 'PSN is required.';
        }

        if (empty($data['last_name'])) {
            $errors[] = 'Last name is required.';
        }

        if (empty($data['first_name'])) {
            $errors[] = 'First name is required.';
        }
        
        // add more validation rules as needed

        return $errors;
    }
}

<?php

class ConstituentsValidator
{
    public function createValidate($data)
    {
        $errors = [];

        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Last name is required.';
        }

        if (empty($data['first_name'])) {
            $errors['first_name'] = 'First name is required.';
        }

        if (empty($data['birthdate'])) {
            $errors['birthdate'] = 'Birthdate is required.';
        }

        if (empty($data['education_attainment'])) {
            $errors['education_attainment'] = 'Education attainment is required.';
        }

        // PSN uniqueness check (only if PSN is provided)
        if (!empty($data['psn'])) {
            $constituentsModel = new Constituents();
            if ($constituentsModel->psnExists($data['psn'])) {
                $errors['psn'] = 'This PhilSys Number is already registered.';
            }
        }

        return $errors;
    }

    public function updateValidate($data)
    {
        $errors = [];

        // PSN is optional, but if provided, check uniqueness
        if (!empty($data['psn'])) {
            $constituentsModel = new Constituents();
            if ($constituentsModel->psnExists($data['psn'], $data['id'] ?? null)) {
                $errors['psn'] = 'This PhilSys Number is already registered.';
            }
        }

        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Last name is required.';
        }

        if (empty($data['first_name'])) {
            $errors['first_name'] = 'First name is required.';
        }

        if (empty($data['sex'])) {
            $errors['sex'] = 'Sex is required.';
        }

        if (empty($data['birthdate'])) {
            $errors['birthdate'] = 'Birthdate is required.';
        }

        if (empty($data['birthplace'])) {
            $errors['birthplace'] = 'Birthplace is required.';
        }

        if (empty($data['civil_status'])) {
            $errors['civil_status'] = 'Civil status is required.';
        }

        if (empty($data['religion'])) {
            $errors['religion'] = 'Religion is required.';
        }

        if (empty($data['citizenship'])) {
            $errors['citizenship'] = 'Citizenship is required.';
        }

        if (empty($data['education_attainment'])) {
            $errors['education_attainment'] = 'Education attainment is required.';
        }

        if (empty($data['is_graduate'])) {
            $errors['is_graduate'] = 'Graduate status is required.';
        }

        if (empty($data['registered_voter'])) {
            $errors['registered_voter'] = 'Registered voter status is required.';
        }

        return $errors;
    }
}
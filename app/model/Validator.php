<?php

class Validator {
    
    /**
     * Validates user registration data.
     * @param string $username
     * @param string $email
     * @param string $password
     * @return array|null Returns an array of errors, or null if validation passes.
     */
    public static function validateRegistration(string $username, string $email, string $password): ?array {
        $errors = [];

        // 1. Check for required fields
        if (empty($username) || empty($email) || empty($password)) {
            $errors[] = 'All fields are required.';
            return $errors;
        }

        // 2. Username validation (3-20 characters, alphanumeric + underscore)
        if (!preg_match('/^[A-Za-z0-9_]{2,20}$/', $username)) {
            $errors[] = 'Username must contain only letters, numbers or underscores.';
        } elseif (strlen($username) < 2 || strlen($username) > 20) {
            $errors[] = 'Username must be between 2 and 20 characters.';
        }

        // 3. Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        } elseif (strlen($email) > 255) {
            $errors[] = 'Email too long.';
        }

        // 4. Password validation (min 8 characters)
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        } elseif (strlen($password) > 127) {
            $errors[] = 'Password too long.';
        }

        return empty($errors) ? null : $errors;
    }
}
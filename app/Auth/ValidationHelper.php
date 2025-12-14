<?php

namespace App\Auth;

/**
 * Validation Helper untuk customer registration dan admin management
 */
class ValidationHelper
{
    /**
     * Validate email format
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate phone number (Indonesia format)
     */
    public static function isValidPhoneNumber(string $phone): bool
    {
        $phone = preg_replace('/\D/', '', $phone);
        return strlen($phone) >= 10 && strlen($phone) <= 15;
    }

    /**
     * Validate password strength
     * Minimum 8 characters
     */
    public static function isValidPassword(string $password): bool
    {
        return strlen($password) >= 8;
    }

    /**
     * Validate password requirements
     * Returns array of validation errors
     */
    public static function validatePasswordStrength(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password harus mengandung minimal 1 huruf besar';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password harus mengandung minimal 1 huruf kecil';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password harus mengandung minimal 1 angka';
        }

        return $errors;
    }

    /**
     * Validate customer registration data
     */
    public static function validateCustomerRegistration(array $data): array
    {
        $errors = [];

        // Validate name
        if (empty($data['nama_lengkap'])) {
            $errors[] = 'Nama lengkap harus diisi';
        } elseif (strlen($data['nama_lengkap']) < 3) {
            $errors[] = 'Nama lengkap minimal 3 karakter';
        }

        // Validate email
        if (empty($data['email'])) {
            $errors[] = 'Email harus diisi';
        } elseif (!self::isValidEmail($data['email'])) {
            $errors[] = 'Format email tidak valid';
        }

        // Validate phone
        if (empty($data['no_telp'])) {
            $errors[] = 'Nomor telepon harus diisi';
        } elseif (!self::isValidPhoneNumber($data['no_telp'])) {
            $errors[] = 'Nomor telepon tidak valid (10-15 digit)';
        }

        // Validate password
        if (empty($data['password'])) {
            $errors[] = 'Password harus diisi';
        } elseif (!self::isValidPassword($data['password'])) {
            $errors[] = 'Password minimal 8 karakter';
        }

        // Validate password confirmation
        if (empty($data['confirm_password'])) {
            $errors[] = 'Konfirmasi password harus diisi';
        } elseif ($data['password'] !== $data['confirm_password']) {
            $errors[] = 'Password dan konfirmasi password tidak cocok';
        }

        return $errors;
    }

    /**
     * Sanitize input
     */
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize array of inputs
     */
    public static function sanitizeArray(array $data): array
    {
        return array_map(function ($value) {
            return self::sanitize($value);
        }, $data);
    }
}

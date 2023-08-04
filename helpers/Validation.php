<?php
class Validation
{
    public static $errors = [
        'amount' => [
            'field' => 'amount',
            'message' => 'Invalid amount.'
        ],
        'nickname' => [
            'field' => 'nickname',
            'message' => 'Wrong, nickname must be 4 or more symbols, less then 20.'
        ],
        'wallet' => [
            'field' => 'wallet',
            'message' => 'Wrong wallet number'
        ],
        'second_wallet' => [
            'field' => 'second_wallet',
            'message' => 'Wrong wallet number'
        ],
        'phone' => [
            'field' => 'phone',
            'message' => 'Wrong phone number'
        ],
        'email' => [
            'field' => 'email',
            'message' => 'Wrong email'
        ],
        'login' => [
            'field' => 'login',
            'message' => 'Wrong login'
        ],
        'password' => [
            'field' => 'password',
            'message' => 'Wrong, password must be 6 or more symbols.'
        ],
        'firstname' => [
            'field' => 'firstname',
            'message' => 'Wrong name'
        ],
        'surname' => [
            'field' => 'surname',
            'message' => 'Wrong surname'
        ],
        'unique_nickname' => [
            'field' => 'nickname',
            'message' => 'This nickname already registered'
        ],
        'unique_email' => [
            'field' => 'email',
            'message' => 'This email already registered'
        ],
        'unique_phone' => [
            'field' => 'phone',
            'message' => 'This phone already registered'
        ],
        'email_verification' => [
            'field' => 'email',
            'message' => 'This email already verified'
        ],
        'password_recognition' => [
            'field' => 'current_password',
            'message' => 'Wrong current password'
        ],
        'privacy_policy' => [
            'field' => 'privacy_policy',
            'message' => 'You must agree to the privacy policy'
        ],
        'full_age' => [
            'field' => 'full_age',
            'message' => 'You must be of legal age'
        ],
        'password_confirmation' => [
            'field' => 'confirm_password',
            'message' => 'Incorrect password confirmation'
        ],
        'birth' => [
            'field' => 'birth',
            'message' => 'Incorrect date format'
        ],
        'db_error' => [
            'field' => 'nickname',
            'message' => 'An error occurred during registration'
        ],
        'iban' => [
            'field' => 'iban',
            'message' => 'Wrong IBAN'
        ],
        'country' => [
            'field' => 'country',
            'message' => 'Wrong country'
        ],
        'city' => [
            'field' => 'city',
            'message' => 'Wrong city'
        ],
        'street' => [
            'field' => 'street',
            'message' => 'Wrong street'
        ],
        'house' => [
            'field' => 'house',
            'message' => 'Wrong house'
        ],
        'apartment' => [
            'field' => 'apartment',
            'message' => 'Wrong apartment'
        ],
        'login_error' => [
            'field' => 'all',
            'message' => 'Wrong password or email'
        ],
        'not_image' => [
            'field' => 'all',
            'message' => 'file is not image'
        ],
        'large_file' => [
            'field' => 'all',
            'message' => 'file is too large'
        ],
        'not_image_type' => [
            'field' => 'all',
            'message' => 'Only JPG, JPEG, PNG files are allowed'
        ],
        'file_upload_error' => [
            'field' => 'all',
            'message' => 'There was an error uploading file'
        ]
    ];
    public static function validate_field($param, $type)
    {
        switch ($type) {
            case 'email':
                if(!Validation::check_out_of_range_string($param)) {
                    return false;
                }
                if (filter_var($param, FILTER_VALIDATE_EMAIL)) {
                    return true;
                } else {
                    $_SESSION['error_type'] = "email";
                    return false;
                }
                break;
            case 'phone':
                if(!Validation::check_out_of_range_string($param)) {
                    return false;
                }
                if ((strlen($param) <= 16) && (strlen($param) >= 8) && (preg_match('/^[+][0-9]/', $param) == 1)) {
                    return true;
                } else {
                    $_SESSION['error_type'] = "phone";
                    return false;
                }
                break;
            case 'nickname':
                if ((strlen($param) < 20) && (strlen($param) > 3) && (preg_match('/^[a-zA-ZА-Яа-яёЁЇїІіЄєҐґ0-9]/', $param) == 1)) {
                    return true;
                } else {
                    $_SESSION['error_type'] = "nickname";
                    return false;
                }
                break;
            case 'wallet':
                if(!Validation::check_out_of_range_string($param)) {
                    $_SESSION['error_type'] = "wallet";
                    return false;
                }
                break;
            case 'second_wallet':
                if(!Validation::check_out_of_range_string($param)) {
                    return false;
                }
                break;
            case 'password':
                if(!Validation::check_out_of_range_string($param)) {
                    return false;
                }
                if (strlen($param) > 5) {
                    return true;
                } else {
                    $_SESSION['error_type'] = "password";
                    return false;
                }
                break;
            case 'firstname':
                if(!Validation::check_out_of_range_string($param)) {
                    return false;
                }
                if (strlen($param) > 0 && (preg_match('/^[a-zA-ZА-Яа-яёЁЇїІіЄєҐґ]/', $param) == 1)) {
                    return true;
                } else {
                    $_SESSION['error_type'] = "firstname";
                    return false;
                }
            case 'surname':
                if(!Validation::check_out_of_range_string($param)) {
                    return false;
                }
                if (strlen($param) > 0 && (preg_match('/^[a-zA-ZА-Яа-яёЁЇїІіЄєҐґ]/', $param) == 1)) {
                    return true;
                } else {
                    $_SESSION['error_type'] = "surname";
                    return false;
                }
            case 'login':
                if(!Validation::check_out_of_range_string($param)) {
                    return false;
                }
                if (filter_var($param, FILTER_VALIDATE_EMAIL)) {
                    return true;
                } else {
                    $_SESSION['error_type'] = "login";
                    return false;
                }
                break;
            case 'birth':
                if(!Validation::check_out_of_range_string($param)) {
                    return false;
                }
                if (!$param) {
                    $_SESSION['error_type'] = "birth";
                    return false;
                }
                $date = date_parse($param);
                $today = date("Y-m-d");
                if ($param < $today) {
                    return true;
                } else {
                    $_SESSION['error_type'] = "birth";
                    return false;
                }
                break;
            case 'iban':
                if($param === '') {
                    return true;
                }
                if(Validation::checkIBAN($param)) {
                    return true;
                } else {
                }
                break;
            case 'country':
                if(!Validation::check_out_of_range_string($param)) {
                    $_SESSION['error_type'] = "country";
                    return false;
                }
                break;
            case 'city':
                if(!Validation::check_out_of_range_string($param)) {
                    $_SESSION['error_type'] = "city";
                    return false;
                }
                break;
            case 'street':
                if(!Validation::check_out_of_range_string($param)) {
                    $_SESSION['error_type'] = "street";
                    return false;
                }
                break;
            case 'house':
                if(!Validation::check_out_of_range_string($param)) {
                    $_SESSION['error_type'] = "house";
                    return false;
                }
                break;
            case 'apartment':
                if(!Validation::check_out_of_range_string($param)) {
                    $_SESSION['error_type'] = "apartment";
                    return false;
                }
                break;
            case 'amount':
                if(!Validation::check_out_of_range_number($param) || (float) $param <= 0) {
                    $_SESSION['error_type'] = "amount";
                    return false;
                }
                break;
        }
        return true;
    }

    public static function validate_fields($fields)
    {
        foreach ($fields as $key => $value) {
            if (!Validation::validate_field($value, $key))
                return false;
        }
        return true;
    }

    public static function validate_fields_type($fields, $type)
    {
        foreach ($fields as $key => $value) {
            if (!Validation::validate_field($value, $type))
                return false;
        }
        return true;
    }

    public static function check_out_of_range_string($param)
    {
        if (strlen($param) > 200) {
            return false;
        } else {
            return true;
        }
    }

    public static function check_out_of_range_number($param)
    {
        if (abs((float) $param) > 999999999) {
            return false;
        } else {
            return true;
        }
    }

    public static function checkIBAN(string $iban): bool {
        // Normalize input (remove spaces and make uppercase)
        $iban = strtoupper(str_replace(' ', '', $iban));
    
        if (!preg_match('/^([A-Z]{2})(\d{2})([A-Z\d]{1,30})$/', $iban, $segments)) {
            return false;
        }
        [, $country, $check, $account] = $segments;
    
        $digits = str_split(strtr($account . $country, array_combine(range('A', 'Z'), range(10, 35))) . '00');
        $first = array_shift($digits);
    
        $checksum = array_reduce(
            $digits,
            function ($carry, $int) {
                $carry = ($carry * 10 + (int)$int) % 97;
                return $carry;
            },
            (int)$first
        );
    
        return (98 - $checksum) == $check;
    }

}
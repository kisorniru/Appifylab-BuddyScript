<?php

namespace App\Managers;

use App\Constants\GenderType;

class TextManager
{
    public static function makeEncryption(string $text, string $salt): string
    {
        if ($salt == null) {
            return '';
        }

        return openssl_encrypt($text, 'AES-128-ECB', $salt);
    }

    public static function makeComposite(string $text, string $salt): string
    {
        if ($salt == null) {
            return '';
        }

        return openssl_decrypt($text, 'AES-128-ECB', $salt);
    }

    public static function makeRandomString(int $intLength, int $intType = 0): string
    {
        $str = '';
        switch ($intType) {
            case 0:
                $str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
                break;
            case 1:
                $str = array_merge(range('a', 'z'), range('0', '9'));
                break;
            case 2:
                $str = array_merge(range('0', '9'));
                break;
        }

        $random = '';
        for ($i = 0; $i < $intLength; $i++) {
            $random .= $str[rand(0, count($str) - 1)];
        }

        return $random;
    }

    public static function getGenderText(int $gender): ?string
    {
        switch ($gender) {
            case GenderType::MALE:
                return 'Male';
            case GenderType::FEMALE:
                return 'Female';
            case GenderType::OTHER:
                return 'Other';
            default:
                return null;
        }
    }
}

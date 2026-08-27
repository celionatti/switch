<?php

declare(strict_types=1);

namespace App\Utils;

class FormatUtil
{
    /**
     * Truncate text to a given length with an ellipsis.
     */
    public static function truncate(string $text, int $limit = 100, string $end = '...'): string
    {
        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $limit)) . $end;
    }

    /**
     * Format a phone number into readable chunks.
     */
    public static function phone(string $phone): string
    {
        $digits = preg_replace('/[^\d]/', '', $phone) ?? '';
        if (strlen($digits) === 10) {
            return '(' . substr($digits, 0, 3) . ') ' . substr($digits, 3, 3) . '-' . substr($digits, 6);
        }
        return $phone;
    }
}

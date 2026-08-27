<?php

declare(strict_types=1);

/**
 * Developer Custom Helpers & Utilities.
 *
 * Any files placed inside `app/Utils/` or `app/Helpers/` are automatically discovered
 * and loaded by Switch Framework on boot. Both procedural functions and class files
 * (under `App\Utils\*`) are immediately accessible throughout the application.
 */

if (!function_exists('currency')) {
    /**
     * Format a numeric amount into a formatted currency string.
     */
    function currency(float|int $amount, string $symbol = '$', int $decimals = 2): string
    {
        return $symbol . number_format((float) $amount, $decimals, '.', ',');
    }
}

if (!function_exists('slugify')) {
    /**
     * Convert any string into a URL-friendly slug.
     */
    function slugify(string $text, string $divider = '-'): string
    {
        // Replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
        // Transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', (string) $text);
        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', (string) $text);
        // Trim
        $text = trim((string) $text, $divider);
        // Remove duplicate divider
        $text = preg_replace('~-+~', $divider, (string) $text);
        // Lowercase
        $text = strtolower((string) $text);

        return empty($text) ? 'n-a' : (string) $text;
    }
}

if (!function_exists('mask_email')) {
    /**
     * Mask an email address for privacy display (e.g. j***n@example.com).
     */
    function mask_email(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        [$name, $domain] = explode('@', $email);
        $len = strlen($name);
        if ($len <= 2) {
            $maskedName = substr($name, 0, 1) . '***';
        } else {
            $maskedName = substr($name, 0, 1) . '***' . substr($name, -1);
        }

        return $maskedName . '@' . $domain;
    }
}

if (!function_exists('human_filesize')) {
    /**
     * Format byte sizes into human-readable strings (KB, MB, GB).
     */
    function human_filesize(int|float $bytes, int $decimals = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $factor = floor((strlen((string) (int) $bytes) - 1) / 3);
        $factor = min($factor, count($units) - 1);

        return sprintf("%.{$decimals}f %s", $bytes / (1024 ** $factor), $units[$factor]);
    }
}

if (!function_exists('str_initials')) {
    /**
     * Extract uppercase initials from a full name (e.g. "Sarah Connor" -> "SC").
     */
    function str_initials(string $name, int $max = 2): string
    {
        $words = preg_split('/\s+/', trim($name)) ?: [];
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1));
                if (mb_strlen($initials) >= $max) {
                    break;
                }
            }
        }
        return $initials ?: 'SW';
    }
}

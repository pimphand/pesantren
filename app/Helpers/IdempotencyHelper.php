<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class IdempotencyHelper
{
    /**
     * Generate a unique idempotency key
     * Format: {timestamp}-{random_string}-{user_id}
     *
     * @param int $userId
     * @return string
     */
    public static function generateKey(int $userId): string
    {
        $timestamp = now()->timestamp;
        $random = Str::random(8);

        return "{$timestamp}-{$random}-{$userId}";
    }

    /**
     * Validate if an idempotency key is in the correct format
     *
     * @param string $key
     * @return bool
     */
    public static function isValidFormat(string $key): bool
    {
        return preg_match('/^\d{10}-[a-zA-Z0-9]{8}-\d+$/', $key) === 1;
    }
}

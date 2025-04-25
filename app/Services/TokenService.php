<?php

namespace App\Services;

use Illuminate\Support\Str;

class TokenService
{
    /**
     * Generate a unique transaction token
     *
     * @return string
     */
    public static function generateTransactionToken(): string
    {
        return Str::uuid()->toString();
    }
}

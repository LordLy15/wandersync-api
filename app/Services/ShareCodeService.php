<?php

namespace App\Services;

use App\Models\Trip;

class ShareCodeService
{
    public function generateUniqueCode(): string
    {
        do {
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Trip::where('share_code', $code)->exists());

        return $code;
    }
}

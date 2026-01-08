<?php

namespace Kishan\QueryIntel\Support;

use Illuminate\Support\Str;

class RequestContext
{
    protected static ?string $id = null;

    public static function id(): string
    {
        if (!static::$id) {
            static::$id = (string) Str::uuid();
        }

        return static::$id;
    }
}

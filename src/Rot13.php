<?php
declare(strict_types=1);

namespace JDZ\EmailObfuscator;

class Rot13
{
    public static function convert(string $s): string
    {
        return \str_rot13($s);
    }
}

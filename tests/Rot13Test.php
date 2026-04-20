<?php
declare(strict_types=1);

namespace JDZ\EmailObfuscator\Tests;

use JDZ\EmailObfuscator\Rot13;
use PHPUnit\Framework\TestCase;

class Rot13Test extends TestCase
{
    public function testConvertIsItsOwnInverse(): void
    {
        $s = 'Hello, World! joe@example.com';
        $this->assertSame($s, Rot13::convert(Rot13::convert($s)));
    }

    public function testNonLettersPassThrough(): void
    {
        $input = '0123456789 @.-_<>"\'/&';
        $this->assertSame($input, Rot13::convert($input));
    }

    public function testLettersAreRotatedByThirteen(): void
    {
        $this->assertSame('nopqrstuvwxyzabcdefghijklm', Rot13::convert('abcdefghijklmnopqrstuvwxyz'));
        $this->assertSame('NOPQRSTUVWXYZABCDEFGHIJKLM', Rot13::convert('ABCDEFGHIJKLMNOPQRSTUVWXYZ'));
    }

    public function testMatchesJsImplementationOnMailtoMarkup(): void
    {
        $html = '<a href="mailto:joe@example.com">joe@example.com</a>';
        $expected = '<n uers="znvygb:wbr@rknzcyr.pbz">wbr@rknzcyr.pbz</n>';
        $this->assertSame($expected, Rot13::convert($html));
    }
}

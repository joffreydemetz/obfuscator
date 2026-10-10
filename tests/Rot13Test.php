<?php
declare(strict_types=1);

namespace JDZ\Obfuscator\Tests;

use JDZ\Obfuscator\Rot13;
use PHPUnit\Framework\TestCase;

class Rot13Test extends TestCase
{
    public function testMatchesJsImplementationOnMailtoMarkup(): void
    {
        $html = '<a href="mailto:joe@example.com">joe@example.com</a>';
        $expected = '<n uers="znvygb:wbr@rknzcyr.pbz">wbr@rknzcyr.pbz</n>';
        $this->assertSame($expected, Rot13::convert($html));
    }
}

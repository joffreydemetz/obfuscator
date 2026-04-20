<?php
declare(strict_types=1);

namespace JDZ\Obfuscator\Tests;

use JDZ\Obfuscator\Obfuscator;
use PHPUnit\Framework\TestCase;

class ObfuscatorTest extends TestCase
{
    public function testFirstCallReturnsIndexZeroPlaceholder(): void
    {
        $o = new Obfuscator();
        $this->assertSame('<span id="obfuscated-0"></span>', $o->obfuscate('joe@example.com'));
    }

    public function testIndexIncrementsAcrossCalls(): void
    {
        $o = new Obfuscator();
        $o->obfuscate('a@example.com');
        $o->obfuscate('b@example.com');
        $this->assertSame('<span id="obfuscated-2"></span>', $o->obfuscate('c@example.com'));
    }

    public function testGetPayloadsPreservesOrderAndEncodesMailtoAnchor(): void
    {
        $o = new Obfuscator();
        $o->obfuscate('joe@example.com');
        $o->obfuscate('support@example.com');

        $payloads = $o->getPayloads();
        $this->assertCount(2, $payloads);
        $this->assertSame('<a href="mailto:joe@example.com">joe@example.com</a>', \str_rot13($payloads[0]));
        $this->assertSame('<a href="mailto:support@example.com">support@example.com</a>', \str_rot13($payloads[1]));
    }

    public function testCustomLabelAndAttributesAreRendered(): void
    {
        $o = new Obfuscator();
        $o->obfuscate('joe@example.com', 'Write to Joe', ['class' => 'email']);

        $decoded = \str_rot13($o->getPayloads()[0]);
        $this->assertSame('<a class="email" href="mailto:joe@example.com">Write to Joe</a>', $decoded);
    }

    public function testLabelIsEscaped(): void
    {
        $o = new Obfuscator();
        $o->obfuscate('joe@example.com', '"Joe" <joe>');

        $decoded = \str_rot13($o->getPayloads()[0]);
        $this->assertStringContainsString('&quot;Joe&quot; &lt;joe&gt;', $decoded);
        $this->assertStringNotContainsString('"Joe"', $decoded);
    }

    public function testObfuscateHtmlRegistersRawFragment(): void
    {
        $o = new Obfuscator();
        $placeholder = $o->obfuscateHtml('<strong>hello</strong>');

        $this->assertSame('<span id="obfuscated-0"></span>', $placeholder);
        $this->assertSame('<strong>hello</strong>', \str_rot13($o->getPayloads()[0]));
    }

    public function testRenderScriptIsEmptyWhenNoPayloads(): void
    {
        $this->assertSame('', (new Obfuscator())->renderScript());
    }

    public function testRenderScriptProducesJsonArrayPayload(): void
    {
        $o = new Obfuscator();
        $o->obfuscate('joe@example.com');
        $o->obfuscate('support@example.com');

        $script = $o->renderScript();
        $this->assertStringStartsWith('<script>window.Obfuscator.decode(', $script);
        $this->assertStringEndsWith(');</script>', $script);

        \preg_match('/decode\((.*)\);<\/script>$/', $script, $m);
        $decoded = \json_decode($m[1], true);
        $this->assertSame($o->getPayloads(), $decoded);
    }

    public function testResetClearsPayloadsAndIndex(): void
    {
        $o = new Obfuscator();
        $o->obfuscate('joe@example.com');
        $o->reset();

        $this->assertSame([], $o->getPayloads());
        $this->assertSame('<span id="obfuscated-0"></span>', $o->obfuscate('other@example.com'));
    }
}

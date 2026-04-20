<?php
declare(strict_types=1);

namespace JDZ\EmailObfuscator\Tests;

use JDZ\EmailObfuscator\Extension\EmailObfuscatorTwigExtension;
use JDZ\EmailObfuscator\Obfuscator;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class ExtensionTest extends TestCase
{
    private Obfuscator $obfuscator;
    private Environment $twig;

    protected function setUp(): void
    {
        $this->obfuscator = new Obfuscator();

        $this->twig = new Environment(new ArrayLoader([
            'filter.twig' => "{{ 'joe@example.com'|obfuscate }}",
            'filter-attrs.twig' => "{{ 'joe@example.com'|obfuscate({ class: 'email' }) }}",
            'function.twig' => "{{ obfuscate_email('joe@example.com', 'Write', { class: 'x' }) }}",
            'function-html.twig' => "{{ obfuscate_html('<em>hi</em>') }}",
            'script.twig' => "{{ obfuscate_script() }}",
            'full.twig' => "{{ 'a@example.com'|obfuscate }}|{{ obfuscate_email('b@example.com') }}|{{ obfuscate_script() }}",
        ]), ['autoescape' => 'html']);

        $this->twig->addExtension(new EmailObfuscatorTwigExtension($this->obfuscator));
    }

    public function testFilterRendersPlaceholderAndRegistersPayload(): void
    {
        $out = $this->twig->render('filter.twig');
        $this->assertSame('<span id="obfuscated-0"></span>', $out);
        $this->assertSame('<a href="mailto:joe@example.com">joe@example.com</a>', \str_rot13($this->obfuscator->getPayloads()[0]));
    }

    public function testFilterForwardsAttributes(): void
    {
        $this->twig->render('filter-attrs.twig');
        $decoded = \str_rot13($this->obfuscator->getPayloads()[0]);
        $this->assertStringContainsString('class="email"', $decoded);
    }

    public function testFunctionAcceptsLabelAndAttrs(): void
    {
        $this->twig->render('function.twig');
        $decoded = \str_rot13($this->obfuscator->getPayloads()[0]);
        $this->assertSame('<a class="x" href="mailto:joe@example.com">Write</a>', $decoded);
    }

    public function testObfuscateHtmlFunctionPassesRawHtml(): void
    {
        $this->twig->render('function-html.twig');
        $this->assertSame('<em>hi</em>', \str_rot13($this->obfuscator->getPayloads()[0]));
    }

    public function testScriptFunctionIsSafeHtml(): void
    {
        $this->obfuscator->obfuscate('joe@example.com');
        $out = $this->twig->render('script.twig');
        $this->assertStringStartsWith('<script>', $out);
        $this->assertStringNotContainsString('&lt;script', $out);
    }

    public function testFullRenderIndexesInOrder(): void
    {
        $out = $this->twig->render('full.twig');
        $this->assertStringContainsString('<span id="obfuscated-0"></span>', $out);
        $this->assertStringContainsString('<span id="obfuscated-1"></span>', $out);

        $payloads = $this->obfuscator->getPayloads();
        $this->assertSame('<a href="mailto:a@example.com">a@example.com</a>', \str_rot13($payloads[0]));
        $this->assertSame('<a href="mailto:b@example.com">b@example.com</a>', \str_rot13($payloads[1]));
    }
}

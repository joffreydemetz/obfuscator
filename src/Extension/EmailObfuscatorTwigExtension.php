<?php
declare(strict_types=1);

namespace JDZ\Obfuscator\Extension;

use JDZ\Obfuscator\Obfuscator;
use Twig\Extension\AbstractExtension as TwigAbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class EmailObfuscatorTwigExtension extends TwigAbstractExtension
{
    public function __construct(protected Obfuscator $obfuscator)
    {
    }

    public function getName(): string
    {
        return 'jdz.emailObfuscator';
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'obfuscate',
                fn (string $email, array $attrs = []): string => $this->obfuscator->obfuscate($email, null, $attrs),
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'obfuscate_email',
                fn (string $email, ?string $label = null, array $attrs = []): string => $this->obfuscator->obfuscate($email, $label, $attrs),
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'obfuscate_html',
                fn (string $html): string => $this->obfuscator->obfuscateHtml($html),
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'obfuscate_script',
                fn (): string => $this->obfuscator->renderScript(),
                ['is_safe' => ['html']]
            ),
        ];
    }
}

<?php
declare(strict_types=1);

namespace JDZ\EmailObfuscator;

class Obfuscator
{
    /** @var string[] */
    protected array $payloads = [];

    public function obfuscate(string $email, ?string $label = null, array $attrs = []): string
    {
        $label ??= $email;

        $attrs['href'] = 'mailto:' . $email;

        $html = '<a' . $this->renderAttributes($attrs) . '>' . $this->escape($label) . '</a>';

        return $this->registerHtml($html);
    }

    public function obfuscateHtml(string $html): string
    {
        return $this->registerHtml($html);
    }

    /** @return string[] */
    public function getPayloads(): array
    {
        return $this->payloads;
    }

    public function renderScript(): string
    {
        if ([] === $this->payloads) {
            return '';
        }

        $json = \json_encode($this->payloads, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);

        return '<script>window.Obfuscator.decode(' . $json . ');</script>';
    }

    public function reset(): void
    {
        $this->payloads = [];
    }

    protected function registerHtml(string $html): string
    {
        $index = \count($this->payloads);
        $this->payloads[] = Rot13::convert($html);

        return '<span id="obfuscated-' . $index . '"></span>';
    }

    protected function renderAttributes(array $attrs): string
    {
        $out = '';
        foreach ($attrs as $key => $value) {
            if (false === $value || null === $value) {
                continue;
            }
            if (true === $value) {
                $out .= ' ' . $key;
                continue;
            }
            $out .= ' ' . $key . '="' . $this->escape((string) $value) . '"';
        }
        return $out;
    }

    protected function escape(string $s): string
    {
        return \htmlspecialchars($s, \ENT_QUOTES | \ENT_HTML5, 'UTF-8');
    }
}

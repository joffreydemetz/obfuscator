# jdz/emailobfuscator

Server-side email obfuscation paired with the [jizy-obfuscator](https://github.com/joffreydemetz/jizy-obfuscator) JS client.

Emits indexed `<span id="obfuscated-N">` placeholders and ROT13-encoded HTML payloads; the JS client reveals the real markup in the DOM on page load.

## Installation

```bash
composer require jdz/emailobfuscator
```

## Usage

### PHP

```php
use JDZ\EmailObfuscator\Obfuscator;

$obfuscator = new Obfuscator();

echo $obfuscator->obfuscate('joe@example.com');
// <span id="obfuscated-0"></span>

echo $obfuscator->obfuscate('support@example.com', 'Write to support', ['class' => 'email']);
// <span id="obfuscated-1"></span>

echo $obfuscator->renderScript();
// <script>window.Obfuscator.decode(["...","..."]);</script>
```

### Twig

```php
use JDZ\EmailObfuscator\Extension\EmailObfuscatorTwigExtension;

$twig->addExtension(new EmailObfuscatorTwigExtension($obfuscator));
```

```twig
{{ 'joe@example.com'|obfuscate }}
{{ 'joe@example.com'|obfuscate({ class: 'email' }) }}
{{ obfuscate_email('joe@example.com', 'Write to Joe', { class: 'email' }) }}
{{ obfuscate_html('<strong>Hidden</strong>') }}

{# near </body> #}
{{ obfuscate_script() }}
```

## API

### `Obfuscator`

| Method | Returns |
|---|---|
| `obfuscate(string $email, ?string $label = null, array $attrs = [])` | placeholder `<span>` |
| `obfuscateHtml(string $html)` | placeholder `<span>` |
| `getPayloads(): string[]` | ROT13-encoded payloads, ordered by index |
| `renderScript(): string` | `<script>window.Obfuscator.decode([...])</script>` — empty when nothing registered |
| `reset(): void` | clears collected payloads |

### Twig extension

- Filter `obfuscate(attrs = {})`
- Function `obfuscate_email(email, label = null, attrs = {})`
- Function `obfuscate_html(html)`
- Function `obfuscate_script()`

All filter/function outputs are marked `is_safe: html`.

## Client-side

Install and load [jizy-obfuscator](https://www.npmjs.com/package/jizy-obfuscator). Placeholder ids (`#obfuscated-0`, `#obfuscated-1`, ...) match the order of the payloads array passed to `Obfuscator.decode`, which is exactly the order returned by `getPayloads()` here.

## Testing

```bash
composer install
composer test
```

## License

MIT - see [LICENSE](LICENSE)

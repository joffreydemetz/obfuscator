<?php

/**
 * Example: Twig integration.
 *
 * Run: php examples/02-twig-integration.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use JDZ\Obfuscator\Extension\EmailObfuscatorTwigExtension;
use JDZ\Obfuscator\Obfuscator;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

$obfuscator = new Obfuscator();

$loader = new ArrayLoader([
    'page.twig' => <<<'TWIG'
    <p>Contact: {{ 'joe@example.com'|obfuscate }}</p>
    <p>Support: {{ obfuscate_email('support@example.com', 'Write to support', { class: 'email' }) }}</p>
    <p>Custom: {{ obfuscate_html('<strong>hello</strong>') }}</p>
    {{ obfuscate_script() }}
    TWIG,
]);

$twig = new Environment($loader, ['autoescape' => 'html']);
$twig->addExtension(new EmailObfuscatorTwigExtension($obfuscator));

echo $twig->render('page.twig') . "\n";

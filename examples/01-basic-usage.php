<?php

/**
 * Example: programmatic usage of the Obfuscator.
 *
 * Run: php examples/01-basic-usage.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use JDZ\EmailObfuscator\Obfuscator;

$obfuscator = new Obfuscator();

echo "Contact: "  . $obfuscator->obfuscate('joe@example.com') . "\n";
echo "Support: " . $obfuscator->obfuscate('support@example.com', 'Write to support', ['class' => 'email']) . "\n";
echo "Custom:  " . $obfuscator->obfuscateHtml('<strong>hello</strong>') . "\n";

echo "\n-- payloads (ROT13-encoded, one per index) --\n";
foreach ($obfuscator->getPayloads() as $i => $payload) {
    echo "[{$i}] {$payload}\n";
}

echo "\n-- script tag (drop this near </body>, after loading jizy-obfuscator) --\n";
echo $obfuscator->renderScript() . "\n";

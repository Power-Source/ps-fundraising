<?php

// Run in separate PHP processes for each order: <other vendor/autoload.php> [reverse].
$other = isset($argv[1]) ? $argv[1] : '';
if (!is_file($other)) {
    fwrite(STDERR, "Usage: php tests/composer-coexistence.php <other vendor/autoload.php> [reverse]\n");
    exit(1);
}

$own = dirname(__DIR__) . '/lib/gateways/vendor/autoload.php';
$paths = isset($argv[2]) && $argv[2] === 'reverse' ? array($own, $other) : array($other, $own);
foreach ($paths as $path) {
    $loader = require $path;
    if (!$loader instanceof \Composer\Autoload\ClassLoader) {
        throw new RuntimeException('Composer did not return a class loader: ' . $path);
    }
}

$options = new \chillerlan\QRCode\QROptions(array(
    'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_MARKUP_SVG,
    'imageBase64' => true,
));
$qr = (new \chillerlan\QRCode\QRCode($options))->render('PS Fundraising coexistence test');
if (strpos($qr, 'data:image/svg+xml;base64,') !== 0 || !class_exists('Stripe\\Stripe')) {
    throw new RuntimeException('QR or Stripe classes unavailable after loading both plugins.');
}

$first_loader = require $own;
$second_loader = require $own;
if ($first_loader !== $second_loader) {
    throw new RuntimeException('Repeated loads must return the same loader.');
}

echo "Composer coexistence OK (" . (isset($argv[2]) ? 'Fundraising first' : 'other plugin first') . ")\n";

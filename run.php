<?php
/**
 * User: Maciej Łebkowski
 * Date: 2012-14-12 14:26
 */

require __DIR__ . '/vendor/autoload.php';

$compressor = new ZL\AssetsLoader\AssetsCompressor();
$loader = new ZL\AssetsLoader\AssetsLoader(
	$compressor,
	__DIR__ . '/examples/sources',
	__DIR__ . '/examples/assets',
	'platform.docplanner.com'
);

$loader->loadModule('user');
$loader->loadModule('doctor', true);
$loader->assets->push("//maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&sensor=SET_TO_TRUE_OR_FALSE", 'js');

var_dump($loader->assets->getAssetLinks());
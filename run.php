<?php
/**
 * User: Maciej Łebkowski
 * Date: 2012-14-12 14:26
 */

use ZL\AssetsLoader\AssetsLoader;
use ZL\AssetsLoader\Builder\Builder;
use ZL\AssetsLoader\FileProcessor\ImageRevisionProcessor;
use ZL\AssetsLoader\FileProcessor\LessParserProcessor;
use ZL\AssetsLoader\Indexer\DirectoryIndexer;
use ZL\AssetsLoader\Indexer\LessIndexer;
use ZL\AssetsLoader\SourceProcessor\BlessProcessor;
use ZL\AssetsLoader\SourceProcessor\StylesheetMinifierProcessor;

require __DIR__ . '/vendor/autoload.php';

$builder = new Builder(new LessIndexer(new DirectoryIndexer), AssetsLoader::TYPE_CSS);
$builder->addFileProcessor(new LessParserProcessor(new \lessc, __DIR__ . '/examples/cache'));
$builder->addFileProcessor(new ImageRevisionProcessor("/Volumes/Dev/Znany Lekarz/web", __DIR__ . '/examples/cache'));
$builder->addSourceProcessor(new BlessProcessor);
$builder->addSourceProcessor(new StylesheetMinifierProcessor);

$assetsLoader = new AssetsLoader(__DIR__  . '/examples/assets', '//platform.docplanner.com');
$assetsLoader->addBuilder(__DIR__ . '/examples/sources/css', $builder);

$assetsLoader->buildAssets('rebranding');
$links = $assetsLoader->getPublicLinksToAssets();

//$modules = ['admin', 'user'];
$modules = [ ];
$isIe = 1;
$compress = 1;
$re = sprintf('/ %s (?:-(%s)){%d} (\.blessed(-\d)?){0,%d} (\.min){%d}\.%s/x',
	'rebranding',
	implode('|', $modules), (int) (sizeof($modules) > 0),
	(int) $isIe,
	$compress,
	'css'
);

var_dump($re, array_filter($links, function ($url) use ($re)
{
	return preg_match($re, $url);
}));

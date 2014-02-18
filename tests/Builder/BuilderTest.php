<?php

namespace ZL\AssetsLoader\Builder;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use ZL\AssetsLoader\AssetsLoader;
use ZL\AssetsLoader\Indexer\DirectoryIndexer;
use ZL\AssetsLoader\Indexer\Index;
use ZL\AssetsLoader\Indexer\LessIndexer;
use ZL\AssetsLoader\Structure\Source;

class LessBuilderTest extends \PHPUnit_Framework_TestCase
{
	private $basePath;
	/** @var vfsStreamDirectory */
	private $root;

	protected function setUp()
	{
		$this->root = vfsStream::setup('assets', null, [
			'input' => [
				'layout' => [
					'common.less' => '/** Contents of user.less */',
					'authorized.less' => '/** Contents of authorized.less */',
					'index.less' => '/** Some index.less content */',
				],
			],
			'output' => [],
		]);
		$this->basePath = $this->root->url() . '/';
	}

	public function testModulesArePresent()
	{
		$indexer = new LessIndexer(new DirectoryIndexer);
		$builder = new Builder($indexer, AssetsLoader::TYPE_CSS);
		$assets = $builder->buildLayout($this->basePath . 'input/layout', $this->basePath . 'output/layout');

		$this->assertArrayHasKey($this->basePath . 'output/layout.css', $assets);
		$this->assertArrayHasKey($this->basePath . 'output/layout-authorized.css', $assets);
		$this->assertArrayHasKey($this->basePath . 'output/layout-common.css', $assets);
	}

	public function testSourceFiltersCanReturnMultipleVariants()
	{
		$filter = $this->getMock('\\ZL\\AssetsLoader\\SourceProcessor\\SourceProcessorInterface', ['processAsset', 'isHandling']);
		$filter->expects($this->any())->method('isHandling')->will($this->returnValue(true));
		$filter->expects($this->any())->method('processAsset')->will($this->returnCallback(function (Source $source)
		{
			return [$source, new Source("", $source->getType(), $source->getName(). '.min')];
		}));

		$indexer = new LessIndexer(new DirectoryIndexer);
		$builder = new Builder($indexer, AssetsLoader::TYPE_CSS);
		$builder->addSourceProcessor($filter);
		$assets = $builder->buildLayout($this->basePath . 'input/layout', $this->basePath . 'output/layout');
		$this->assertArrayHasKey($this->basePath . 'output/layout.css', $assets);
		$this->assertArrayHasKey($this->basePath . 'output/layout-authorized.css', $assets);
		$this->assertArrayHasKey($this->basePath . 'output/layout-common.css', $assets);
		$this->assertArrayHasKey($this->basePath . 'output/layout.min.css', $assets);
		$this->assertArrayHasKey($this->basePath . 'output/layout-authorized.min.css', $assets);
		$this->assertArrayHasKey($this->basePath . 'output/layout-common.min.css', $assets);

	}

}



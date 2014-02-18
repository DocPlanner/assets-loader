<?php

namespace ZL\AssetsLoader\Indexer;

class LessIndexerTest extends \PHPUnit_Framework_TestCase
{
	public function testIndexFileIsUsed()
	{
		$path = '/';
		$indexPath = $path . LessIndexer::INDEX_NAME . '.' . LessIndexer::LESS_EXTENSION;

		$index = new Index;
		$index->addPath($indexPath);

		/** @var \PHPUnit_Framework_MockObject_MockObject|DirectoryIndexer $directoryInderer */
		$directoryInderer = $this->getMock(__NAMESPACE__ . '\\DirectoryIndexer', ['indexDirectory']);

		/** @var \PHPUnit_Framework_MockObject_Builder_InvocationMocker $i */
		$i = $directoryInderer->expects($this->once());
		$i->method('indexDirectory')->with($this->equalTo($path))->will($this->returnValue($index));

		$lessIndexer = new LessIndexer($directoryInderer);

		$result = $lessIndexer->indexDirectory($path);

		$this->assertEquals(1, $result->getFilePaths()->count());
		$this->assertEquals(0, sizeof($result->getSubModules()));
		$this->assertEquals($indexPath, $result->getIndexPath());
	}

	public function testFilesAreRecognizedAsSubmodules()
	{
		$path = '/';
		$submodulePath = $path . 'module-1.' . LessIndexer::LESS_EXTENSION;

		$index = new Index;
		$index->addPath($path . LessIndexer::INDEX_NAME . '.' . LessIndexer::LESS_EXTENSION);
		$index->addPath($submodulePath);

		/** @var \PHPUnit_Framework_MockObject_MockObject|DirectoryIndexer $directoryInderer */
		$directoryInderer = $this->getMock(__NAMESPACE__ . '\\DirectoryIndexer', ['indexDirectory']);

		/** @var \PHPUnit_Framework_MockObject_Builder_InvocationMocker $i */
		$i = $directoryInderer->expects($this->once());
		$i->method('indexDirectory')->with($this->equalTo($path))->will($this->returnValue($index));

		$lessIndexer = new LessIndexer($directoryInderer);

		$result = $lessIndexer->indexDirectory($path);

		$this->assertEquals(1, $result->getFilePaths()->count());
		$this->assertEquals(1, sizeof($result->getSubModules()));
		$this->assertEquals($submodulePath, $result->getSubModules()['module-1']->getIndexPath());
	}

	public function testIndexFilesIsRequiredForSubmodules()
	{
		$path = '/';
		$submodulePath = $path . 'module-1.' . LessIndexer::LESS_EXTENSION;

		$index = new Index;
		// hint: there is no index file
		$index->addPath($submodulePath);

		/** @var \PHPUnit_Framework_MockObject_MockObject|DirectoryIndexer $directoryInderer */
		$directoryInderer = $this->getMock(__NAMESPACE__ . '\\DirectoryIndexer', ['indexDirectory']);

		/** @var \PHPUnit_Framework_MockObject_Builder_InvocationMocker $i */
		$i = $directoryInderer->expects($this->once());
		$i->method('indexDirectory')->with($this->equalTo($path))->will($this->returnValue($index));

		$lessIndexer = new LessIndexer($directoryInderer);

		$result = $lessIndexer->indexDirectory($path);

		$this->assertEquals(1, $result->getFilePaths()->count());
		$this->assertEquals(0, sizeof($result->getSubModules()));
		$this->assertEquals([$submodulePath], $result->getFilePaths()->getArrayCopy());
	}
}

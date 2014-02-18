<?php


namespace ZL\AssetsLoader\Indexer;


class LessIndexer extends DirectoryIndexer
{
	const INDEX_NAME = "index";

	const LESS_EXTENSION = 'less';

	/**
	 * @var DirectoryIndexer
	 */
	private $directoryIndexer;

	public function __construct(DirectoryIndexer $directoryIndexer)
	{
		$this->directoryIndexer = $directoryIndexer;
	}

	public function indexDirectory($directory)
	{
		$directoryIndex = $this->directoryIndexer->indexDirectory($directory);

		$indexFile = rtrim($directory, '/') . '/' . self::INDEX_NAME . '.'.self::LESS_EXTENSION;

		if (false === in_array($indexFile, $directoryIndex->getFilePaths()->getArrayCopy()))
		{
			return $directoryIndex;
		}

		$index = new Index($indexFile);
		foreach ($directoryIndex->getFilePaths() as $subModule)
		{
			if (self::LESS_EXTENSION !== pathinfo($subModule, PATHINFO_EXTENSION))
			{
				continue;
			}

			$name = basename($subModule, '.'.self::LESS_EXTENSION);
			if (self::INDEX_NAME !== $name)
			{
				$index->addSubModule($name, new Index($subModule));
			}
		}

		return $index;
	}
}
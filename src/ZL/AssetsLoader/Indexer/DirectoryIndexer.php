<?php


namespace ZL\AssetsLoader\Indexer;


class DirectoryIndexer implements IndexerInterface
{
	public function indexDirectory($directory)
	{
		$index = new Index;
		if (false === is_dir($directory))
		{
			return $index;
		}

		$iterator = new \DirectoryIterator($directory);
		/** @var \SplFileInfo $file */
		foreach ($iterator as $file)
		{
			if ("." == substr($file->getFilename(), 0, 1))
			{
				continue;
			}
			if (is_dir($file->isDir()))
			{
				$index->addSubModule($file->getFilename(), $this->indexDirectory($file->getPathname()));
			}
			else
			{
				$index->addPath($file->getPathname());
			}
		}
		return $index;
	}
}
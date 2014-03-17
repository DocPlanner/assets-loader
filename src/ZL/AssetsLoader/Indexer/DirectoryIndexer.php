<?php


namespace ZL\AssetsLoader\Indexer;


class DirectoryIndexer implements IndexerInterface
{
	public function indexDirectory($directory, $recursive = true)
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
			if ($file->isDir())
			{
				$subModule = $this->indexDirectory($file->getPathname());
				$index->addSubModule($file->getFilename(), $subModule);
				if ($recursive)
				{
					foreach ($subModule->getFilePaths() as $filePath)
					{
						$index->addPath($filePath);
					}
				}
			}
			else
			{
				$index->addPath($file->getPathname());
			}
		}
		return $index;
	}
}
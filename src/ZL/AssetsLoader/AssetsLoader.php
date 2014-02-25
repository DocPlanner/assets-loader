<?php
/**
 * User: Maciej Łebkowski
 * Date: 2012-14-12 12:56
 */
namespace ZL\AssetsLoader;

use ZL\AssetsLoader\Builder\Builder;

/**
 * Assets Loader loads a pack of css/js files from one directory, mangles them, combines,
 * makes his magic and outputs
 */
class AssetsLoader
{
	const TYPE_JS = 'js';
	const TYPE_CSS = 'css';

	const MODE_MODULE = 'module';
	const MODE_MAIN = 'main';

	/**
	 * @var Builder[]
	 */
	private $builders;
	private $targetPath;
	private $staticHostPath;

	public function __construct($targetPath, $staticHostPath)
	{
		$this->targetPath = $targetPath;
		$this->staticHostPath = $staticHostPath;
	}

	/**
	 * @param string  $path
	 * @param Builder $builder
	 */
	public function addBuilder($path, Builder $builder)
	{
		$this->builders[$path] = $builder;
	}

	/**
	 * @param string $name
	 *
	 * @throws \RuntimeException
	 */
	public function buildAssets($name)
	{
		foreach ($this->builders as $path => $builder)
		{
			if (!realpath($path))
			{
				throw new \RuntimeException('Cannot find source path: ' . $path);
			}
			$path = sprintf('%s/%s', $path, $name);

			$target = sprintf('%s/%s/%s', $this->targetPath, $builder->getType(), $name);
			if (false === is_dir(dirname($target)))
			{
				mkdir(dirname($target), 0755, true);
			}

			foreach ($builder->buildLayout($path, $target) as $filePath => $fileContents)
			{
				file_put_contents($filePath, $fileContents);
			}
		}
	}

	public function getPathsToAssets()
	{
		$files = [];
		foreach ($this->builders as $builder)
		{
			$targetPath = $this->targetPath . '/' . $builder->getType();
			$iterator = new \DirectoryIterator($targetPath);
			/** @var \SplFileinfo $file */
			foreach ($iterator as $file)
			{
				if (false === $file->isDir() && "." !== substr($file->getFilename(), 0, 1))
				{
					$files[] = $file->getPathname();
				}
			}
		}
		return $files;
	}

	public function getPublicLinksToAssets()
	{
		return array_filter(array_map(function ($path)
		{
			return $this->staticHostPath . substr($path, strlen($this->targetPath)) . '?' . crc32(file_get_contents($path));
		},
		$this->getPathsToAssets()));
	}

}

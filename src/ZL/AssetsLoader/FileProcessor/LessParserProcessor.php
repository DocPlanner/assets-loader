<?php
/**
 * User: Maciej Łebkowski
 * Date: 2013-09-01 12:53
 */

namespace ZL\AssetsLoader\FileProcessor;

use ZL\AssetsLoader\Structure\File;

class LessParserProcessor implements FileProcessorInterface
{
	protected $cachePath;
	protected $lessc;

	public function __construct(\lessc $lessc, $cachePath)
	{
		$this->lessc = $lessc;
		$this->setCachePath($cachePath);
	}

	private function setCachePath($cachePath)
	{
		if (false === is_dir($cachePath))
		{
			mkdir($cachePath, 0770, true);
		}
		$this->cachePath = realpath($cachePath);
	}

	public function processAsset(File $file, $type)
	{
		$path = $file->getFilePath();
		$output = sprintf('%s/%s-%s', $this->cachePath, basename(dirname($path)), basename($path, 'less') . 'css');
		$cache = $output . '.cache';
		if (file_exists($cache))
		{
			$root = json_decode(file_get_contents($cache), true);
		}
		else
		{
			$root = $path;
		}

		$root = $this->lessc->cachedCompile($root);
		if (isset($root['compiled']))
		{
			file_put_contents($output, $root['compiled']);
			unset($root['compiled']);
			file_put_contents($cache, json_encode($root));
		}

		return [
			new File($output, $file->getType())
		];
	}

	public function isHandling(File $file, $mode)
	{
		return $file->isStylesheet() && $file->isLess();
	}
}

<?php


namespace ZL\AssetsLoader;


use ZL\AssetsLoader\Structure\File;
use ZL\AssetsLoader\Structure\Source;

class SourceFactory
{
	private $sourceClass;
	private $fileClass;

	public function __construct($sourceClass = null, $fileClass = null)
	{
		$this->sourceClass = $sourceClass ?: __NAMESPACE__ . '\\Structure\\Source';
		$this->fileClass = $fileClass ?: __NAMESPACE__ . '\\Structure\\File';
	}

	/**
	 * @param string $filePath
	 * @param string $type
	 *
	 * @return File
	 */
	public function createFile($filePath, $type)
	{
		$fileClass = $this->fileClass;
		return new $fileClass($filePath, $type);
	}

	/**
	 * @param string $source
	 * @param string $type
	 * @param string $name
	 *
	 * @return Source
	 */
	public function createSource($source, $type, $name)
	{
		$sourceClass = $this->sourceClass;
		return new $sourceClass($source, $type, $name);
	}
}
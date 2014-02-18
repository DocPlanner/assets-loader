<?php


namespace ZL\AssetsLoader\Structure;

class File extends Source
{
	private $filePath;

	public function __construct($filePath, $type)
	{
		$this->filePath = $filePath;
		$source = file_exists($filePath) ? file_get_contents($filePath) : "";
		parent::__construct($source, $type, pathinfo($filePath, PATHINFO_BASENAME));
	}

	/**
	 * @return mixed
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}

	public function isLess()
	{
		return 'less' === pathinfo($this->filePath, PATHINFO_EXTENSION);
	}

}
<?php


namespace ZL\AssetsLoader\Indexer;


class Index
{
	const INDEX = "index";

	/**
	 * @var \ArrayObject
	 */
	private $filePaths;

	/**
	 * @var Index[]
	 */
	private $subModules = [];

	public function __construct($filePath = null)
	{
		$this->filePaths = new \ArrayObject(array_filter([self::INDEX => $filePath]));
	}

	public function addPath($path)
	{
		$this->filePaths->append($path);
	}

	public function getIndexPath()
	{
		return $this->filePaths->offsetExists(self::INDEX) ? $this->filePaths->offsetGet(self::INDEX) : null;
	}

	/**
	 * @param       $name
	 * @param Index $subModule
	 */
	public function addSubModule($name, Index $subModule)
	{
		$this->subModules[$name] = $subModule;
	}

	/**
	 * @return Index[]
	 */
	public function getSubModules()
	{
		return $this->subModules;
	}

	/**
	 * @return \ArrayObject
	 */
	public function getFilePaths()
	{
		return $this->filePaths;
	}

	public function getFilePathsWithSubModules()
	{
		return call_user_func_array('array_merge', array_merge([$this->filePaths], array_map(function (Index $index)
		{
			return $index->getFilePathsWithSubModules();
		}, $this->getSubModules())));
	}


}
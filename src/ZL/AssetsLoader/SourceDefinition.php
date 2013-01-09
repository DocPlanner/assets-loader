<?php
/**
 * User: Maciej Łebkowski
 * Date: 2013-09-01 10:32
 */

namespace ZL\AssetsLoader;

abstract class SourceDefinition
{
	const TYPE_FILES = 'files';
	const TYPE_MODULE = 'module';
	const TYPE_LESS = 'less';

	/**
	 * @var mixed
	 */
	protected $definition;

	protected $type;

	protected $path;

	/**
	 * @var \ZL\AssetsLoader\LessParser
	 */
	protected $lessParser;

	/**
	 * @param string $path
	 * @param string $type
	 * @param mixed $definition
	 */
	public function __construct($path, $type, $definition)
	{
		$this->setDefinition($definition);
		$this->setType($type);
		$this->setPath($path);
	}

	public function setPath($path)
	{
		$this->path = $path;
	}

	public function getPath()
	{
		return rtrim($this->path, '/');
	}

	/**
	 * @param mixed $definition
	 */
	public function setDefinition($definition)
	{
		$this->definition = $definition;
	}

	/**
	 * @return mixed
	 */
	public function getDefinition()
	{
		return $this->definition;
	}

	public function setType($type)
	{
		if (false === in_array($type, array (AssetsLoader::TYPE_CSS, AssetsLoader::TYPE_JS)))
		{
			throw new \InvalidArgumentException('Invalid asset type: ' . $type);
		}
		$this->type = $type;
	}

	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param \ZL\AssetsLoader\LessParser $lessParser
	 */
	public function setLessParser($lessParser)
	{
		$this->lessParser = $lessParser;
	}

	/**
	 * @return \ZL\AssetsLoader\LessParser
	 */
	public function getLessParser()
	{
		return $this->lessParser;
	}


	public function getBasePath()
	{
		return realpath($this->getPath() . '/' . $this->getType());
	}

	protected function getDelimiter()
	{
		return AssetsLoader::TYPE_JS === $this->getType() ? ";\n" : "\n";
	}

	public function getCompiledSource()
	{
		$source = array ();
		$files = $this->getFilePaths();

		if (AssetsLoader::TYPE_CSS === $this->type && $this->lessParser)
		{
			$files = array_map(array ($this->lessParser, 'compile'), $files);
		}

		foreach ($files as $path)
		{
			$source[] = file_get_contents($path);
		}

		return implode($this->getDelimiter(), $source);

	}

	abstract public function getFilePaths();
	abstract public function getName();

}

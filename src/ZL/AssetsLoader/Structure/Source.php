<?php


namespace ZL\AssetsLoader\Structure;


use ZL\AssetsLoader\AssetsLoader;

class Source
{
	private $source;
	private $type;
	private $name;

	public function __construct($source, $type, $name)
	{
		$this->source = $source;
		$this->type = $type;
		$this->name = $name;
	}

	public function getContent()
	{
		return $this->source;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	public function isJavascript()
	{
		return AssetsLoader::TYPE_JS === $this->type;
	}

	public function isStylesheet()
	{
		return AssetsLoader::TYPE_CSS === $this->type;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	public function isSame($originalContent)
	{
		return $originalContent === $this->getContent();
	}

}
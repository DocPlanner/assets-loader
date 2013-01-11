<?php
/**
 * User: Maciej Łebkowski
 * Date: 2013-09-01 10:42
 */

namespace ZL\AssetsLoader\SourceDefinition;
use \ZL\AssetsLoader\SourceDefinition;
use \ZL\AssetsLoader\AssetsLoader;
use \DirectoryIterator;



class Module extends SourceDefinition
{
	public function getFilePaths()
	{
		$path = sprintf('%s/%s', $this->getBasePath(), $this->definition);
		if (false === is_dir($path))
		{
			return array ();
		}

		$result = array ();
		$dir = new DirectoryIterator($path);
		foreach ($dir as $file)
		{
			$file = (string) $file;

			$ext = pathinfo($file, PATHINFO_EXTENSION);
			if (false === in_array ($ext, $this->getAllowedExtensions()))
			{
				continue;
			}

			$result[] = rtrim($path, '/') . '/' . $file;
		}
		sort($result);
		return $result;

	}

	protected function getAllowedExtensions()
	{
		switch ($this->getType())
		{
			case AssetsLoader::TYPE_CSS:
				return array ('css', 'less');
			case AssetsLoader::TYPE_JS:
				return array ('js');
			default:
				return array ();
		}
	}

	public function getName()
	{
		return $this->definition;
	}

}

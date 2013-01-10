<?php
/**
 * User: Maciej Łebkowski
 * Date: 2013-09-01 13:37
 */

namespace ZL\AssetsLoader\SourceDefinition;
use \ZL\AssetsLoader\SourceDefinition;

class Files extends SourceDefinition
{
	public function getFilePaths()
	{
		return $this->definition;
	}

	public function getName()
	{
		return implode('-', array_map(function ($file) {
			return str_replace(array ('-vk.css', '-vk.js'), '', pathinfo($file, PATHINFO_BASENAME));
		}, $this->definition));
	}

}

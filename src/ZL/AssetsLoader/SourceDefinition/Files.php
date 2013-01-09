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
		return implode('-', $this->definition);
	}

}

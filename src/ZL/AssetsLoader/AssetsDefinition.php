<?php
/**
 * User: Maciej Łebkowski
 * Date: 2012-14-12 13:49
 */

namespace ZL\AssetsLoader;

class AssetsDefinition
{
	/** @var string */
	protected $hostName;

	protected $assetLinks = array (
		AssetsLoader::TYPE_CSS => array (),
		AssetsLoader::TYPE_JS => array (),
	);

	public function __construct($hostName = null)
	{
		$this->hostName = (parse_url($hostName, PHP_URL_SCHEME) ? '' : '//') . ltrim($hostName, '/');
	}

	public function push($source, $type)
	{
		if ("//" !== substr($source, 0, 2) && null === parse_url($source, PHP_URL_SCHEME))
		{
			$source = $this->hostName . '/' . ltrim($source, '/');
		}
//		var_dump(parse_url($source));
		$this->assetLinks[$type][] = $source;
	}

	public function getAssetLinks()
	{
		return $this->assetLinks;
	}
}

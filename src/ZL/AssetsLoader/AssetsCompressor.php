<?php
/**
 * User: Maciej Łebkowski
 * Date: 2012-14-12 13:10
 */

namespace ZL\AssetsLoader;

class AssetsCompressor
{
	protected $yuiPath;

	public function __construct($yuiPath = null)
	{
		if (null === $yuiPath)
		{
			$yuiPath = $this->findYuiPath();
		}
		$this->yuiPath = $yuiPath;
	}

	public function compress($source, $type)
	{
		$cmd = sprintf('java -jar %s --type %s --charset UTF-8 --line-break 1000', escapeshellarg($this->yuiPath), $type);
		$process = proc_open(
			$cmd,
			array(
				0 => array("pipe", "r"),
				1 => array("pipe", "w"),
				2 => array("pipe", "w"),
			),
			$pipes
		);

		fwrite($pipes[0], $source);
		fclose($pipes[0]);

		$compressed = stream_get_contents($pipes[1]);
		fclose($pipes[1]);

		proc_close($process);
		return $compressed;
	}

	protected function findYuiPath()
	{
		$basePath = dirname(dirname(dirname(__DIR__)));
		if (false === is_dir($basePath . '/vendor'))
		{
			$basePath = dirname(dirname(dirname($basePath)));
		}
		$vendorPath = 'vendor';
		$yuiPath = 'nervo/yuicompressor/yuicompressor.jar';

		$composerPath = $basePath . '/composer.json';
		if (file_exists($composerPath))
		{
			$composer = json_decode(file_get_contents($composerPath));
			if ($composer && isset($composer->{'vendor-dir'}))
			{
				$vendorPath = trim($composer->{'vendor-dir'}, '/');
			}
		}

		return sprintf("%s/%s/%s", $basePath, $vendorPath, $yuiPath);
	}
}

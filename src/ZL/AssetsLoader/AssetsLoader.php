<?php
/**
 * User: Maciej Łebkowski
 * Date: 2012-14-12 12:56
 */
namespace ZL\AssetsLoader;

use InvalidArgumentException, RuntimeException;
use RecursiveDirectoryIterator, RecursiveIteratorIterator;

/**
 * Assets Loader loads a pack of css/js files from one directory, mungles them, combines,
 * makes his magic and outputs
 */
class AssetsLoader
{
	const TYPE_JS = 'js';
	const TYPE_CSS = 'css';

	/**
	 * @var AssetsDefinition
	 */
	public $assets;

	/**
	 * @var string Assets will be loaded from this path
	 */
	protected $sourcePath;
	/**
	 * @var string Compiled assets will be saved here
	 */
	protected $targetPath;

	/**
	 * @var AssetsCompressor
	 */
	protected $compressor;

	/**
	 * @param AssetsCompressor $compressor
	 * @param string           $sourcePath
	 * @param string           $targetPath
	 * @param string           $staticHost
	 */
	public function __construct(AssetsCompressor $compressor, $sourcePath, $targetPath, $staticHost = null)
	{
		$this->sourcePath = realpath($sourcePath);
		$this->targetPath = realpath($targetPath);
		$this->compressor = $compressor;
		$this->assets = new AssetsDefinition($staticHost);
	}

	/**
	 * @param string $name
	 * @param bool   $compress
	 */
	public function loadModule($name, $compress = false)
	{
		foreach (array (self::TYPE_CSS, self::TYPE_JS) as $type)
		{
			$dest = sprintf('%s/%s/%s.%s', $this->targetPath, $type, $name, '%s');

			$source = $this->getAsset($type, $name, false);
			if ("" === $source)
			{
				return ;
			}

			$targetPathNormal = sprintf($dest, $type);
			$targetPathCompressed = sprintf($dest, 'min.'. $type);
			$targetContents = file_exists($targetPathNormal) ? file_get_contents($targetPathNormal) : null;

			if ($targetContents !== $source)
			{
				// regenerate both files!
				file_put_contents($targetPathNormal, $source);

				$sourceMin = $this->getAsset($type, $name, true);
				file_put_contents($targetPathCompressed, $sourceMin);
			}

			if ($compress)
			{
				$targetPathNormal = $targetPathCompressed;
			}

			{
				$this->assets->push(sprintf('/%s/%s?%s', $type, basename($targetPathNormal), substr(md5($source), 0, 10)), $type);
			}
		}
	}

	protected function getAsset($type, $name, $compress = false)
	{
		if (self::TYPE_CSS !== $type && self::TYPE_JS !== $type)
		{
			throw new InvalidArgumentException('Invalid asset type: ' . $type);
		}

		$files = $this->getModuleFiles($type, $name);
		$contents = $this->getFileContents($files, self::TYPE_CSS === $type ? "\n" : ";");

		if ($compress && 0 !== strlen($contents))
		{
			$contents = $this->compressor->compress($contents, $type);
		}
		return $contents;
	}

	protected function getModuleFiles($type, $name)
	{
		$path = sprintf('%s/%s/%s', $this->sourcePath, $type, $name);
		if (false === is_dir($path))
		{
			return array ();
		}

		$result = array();
		$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
		foreach ($dir as $file)
		{
			if ("." . $type !== substr($file, -strlen("." . $type)))
			{
				continue;
			}
			$result[] = (string) $file;

		}
		sort($result);
		return $result;
	}

	protected function getFileContents(array $files, $delimiter = "")
	{
		$source = array ();
		foreach ($files as $path)
		{
			$source[] = file_get_contents($path);
		}
		return implode($delimiter, $source);
	}
}

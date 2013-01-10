<?php
/**
 * User: Maciej Łebkowski
 * Date: 2012-14-12 12:56
 */
namespace ZL\AssetsLoader;

use InvalidArgumentException, RuntimeException;
use RecursiveDirectoryIterator, RecursiveIteratorIterator;

/**
 * Assets Loader loads a pack of css/js files from one directory, mangles them, combines,
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
	 * @var LessParser
	 */
	protected $lessParser;

	public $isLastAssetRegenerated = false;

	/**
	 * @param string $sourcePath
	 * @param string $targetPath
	 * @param string $cachePath
	 * @param string $staticHost
	 */
	public function __construct($sourcePath, $targetPath, $cachePath = null, $staticHost = null)
	{
		$this->sourcePath = realpath($sourcePath);
		$this->targetPath = realpath($targetPath);
		$this->assets = new AssetsDefinition($staticHost);
		if ($cachePath)
		{
			$this->lessParser = new LessParser($cachePath);
		}
	}

	public function setCompressor(AssetsCompressor $compressor)
	{
		$this->compressor = $compressor;
	}

	/**
	 * @param string $name
	 * @param bool   $compress
	 */
	public function loadModule($name, $compress = false)
	{
		foreach (array (self::TYPE_CSS, self::TYPE_JS) as $type)
		{
			$sd = new SourceDefinition\Module($this->sourcePath, $type, $name);
			$sd->setLessParser($this->lessParser);

			$this->buildPlatformAsset($sd, $type, $compress);
		}
	}

	public function loadFiles($files, $type, $compress = false)
	{
		$sd = new SourceDefinition\Files($this->sourcePath, $type, $files);
		$sd->setLessParser($this->lessParser);

		$this->buildPlatformAsset($sd, $type, $compress);
	}

	/**
	 * @param SourceDefinition $sd
	 * @param string           $type
	 * @param bool             $compress
	 */
	protected function buildPlatformAsset(SourceDefinition $sd, $type, $compress)
	{
		$destinationPath = $this->getAssetPathMask($type, $sd->getName());
		$targetPathNormal = sprintf($destinationPath, $type);
		$targetPathCompressed = sprintf($destinationPath, 'min.' . $type);

		$targetContents = file_exists($targetPathNormal) ? file_get_contents($targetPathNormal) : null;

		// only in dev environment:
		if (!$compress)
		{
			$source = $sd->getCompiledSource();
			if ("" === $source)
			{
				return;
			}

			$this->isLastAssetRegenerated = $targetContents !== $source;
			if ($this->isLastAssetRegenerated)
			{
				// regenerate both files!
				file_put_contents($targetPathNormal, $source);

				$sourceMin = $this->compressor ? $this->compressor->compress($source, $type) : $source;
				file_put_contents($targetPathCompressed, $sourceMin);
				$targetContents = $source;
			}
		}
		else
		{
			// or on prod, if there is nothing to load:
			if ("" === (string) $targetContents)
			{
				return;
			}
			$targetPathNormal = $targetPathCompressed;
		}

		$hash = substr(md5($targetContents), 0, 10);
		$this->assets->push(sprintf('/%s/%s?%s', $type, basename($targetPathNormal), $hash), $type);
	}

	protected function getAssetPathMask($type, $name)
	{
		return sprintf('%s/%s/%s.%s', $this->targetPath, $type, $name, '%s');
	}

}

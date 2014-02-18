<?php


namespace ZL\AssetsLoader\FileProcessor;


use ZL\AssetsLoader\Structure\File;

class ImageRevisionProcessor implements FileProcessorInterface
{

	/**
	 * @var string
	 */
	private $imagesPath;
	/**
	 * @var
	 */
	private $cachePath;

	public function __construct($imagesPath, $cachePath)
	{
		$this->imagesPath = $imagesPath;
		$this->cachePath = $cachePath;
	}

	/**
	 * @param File $source
	 * @param      $mode
	 *
	 * @return File[]|File
	 */
	public function processAsset(File $source, $mode)
	{
		$content = preg_replace_callback('/(?<=url\()[\/."\']*(?<url>\/.+?)(?:\?.+)?[\'"]*(?=\))/', function ($m)
		{
			$url = trim($m['url'], chr(34).chr(39));
			$path = $this->imagesPath . $url;
			if (false === file_exists($path))
			{
				return chr(39) . $url . chr(39);
			}

			return chr(39) . $url . '?' . crc32(file_get_contents($path)) . chr(39);

		}, $source->getContent());

		$filePath = $this->cachePath . '/imagerev-' . basename($source->getFilePath());
		file_put_contents($filePath, $content);

		return [
			new File($filePath, $source->getType())
		];
	}

	/**
	 * @param File $source
	 * @param      $mode
	 *
	 * @return bool
	 */
	public function isHandling(File $source, $mode)
	{
		return $source->isStylesheet();
	}
}
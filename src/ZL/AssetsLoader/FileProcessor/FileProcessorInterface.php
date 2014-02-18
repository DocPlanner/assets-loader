<?php


namespace ZL\AssetsLoader\FileProcessor;

use ZL\AssetsLoader\Structure\File;

interface FileProcessorInterface
{
	/**
	 * @param File $source
	 * @param      $mode
	 *
	 * @return File[]|File
	 */
	public function processAsset(File $source, $mode);

	/**
	 * @param File $source
	 * @param      $mode
	 *
	 * @return bool
	 */
	public function isHandling(File $source, $mode);
}
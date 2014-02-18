<?php


namespace ZL\AssetsLoader\SourceProcessor;

use ZL\AssetsLoader\Structure\Source;

interface SourceProcessorInterface
{
	/**
	 * @param Source $source
	 * @param string $mode
	 * @param string $originalContent
	 *
	 * @return Source[]|Source
	 */
	public function processAsset(Source $source, $mode, $originalContent = "");

	/**
	 * @param Source $source
	 * @param string $mode
	 * @param string $originalContent
	 *
	 * @return bool
	 */
	public function isHandling(Source $source, $mode, $originalContent = "");
}
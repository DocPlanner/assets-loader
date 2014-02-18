<?php


namespace ZL\AssetsLoader\SourceProcessor;


use ZL\AssetsLoader\Structure\Source;

class StylesheetMinifierProcessor implements SourceProcessorInterface
{

	/**
	 * @param Source $source
	 * @param string $mode
	 * @param string $originalContent
	 *
	 * @return Source[]|Source
	 */
	public function processAsset(Source $source, $mode, $originalContent = "")
	{
		return [
			$source,
			new Source(
				str_replace(';}', '}', preg_replace('/(?<=[:{};])[\r\n\s]+|[\r\n\s]+(?={)|(?<=\#)([A-F])\1([A-F])\2([A-F])\3\s*(?=;)/m', '$1$2$3', $source->getContent())),
				$source->getType(),
				$source->getName() . '.min'
			)
		];
	}

	/**
	 * @param Source $source
	 * @param string $mode
	 * @param string $originalContent
	 *
	 * @return bool
	 */
	public function isHandling(Source $source, $mode, $originalContent = "")
	{
		return $source->isStylesheet();
	}
}
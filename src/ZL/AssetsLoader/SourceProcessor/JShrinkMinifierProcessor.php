<?php


namespace ZL\AssetsLoader\SourceProcessor;

use JShrink\Minifier;
use ZL\AssetsLoader\Structure\Source;

class JShrinkMinifierProcessor implements SourceProcessorInterface
{

	public function processAsset(Source $source, $mode, $originalContent = "")
	{
		return [
			$source,
			new Source(
				Minifier::minify($source->getContent(), ['flaggedComments' => false]),
				$source->getType(),
				$source->getName() . '.min'
			),
		];
	}

	public function isHandling(Source $source, $mode, $originalContent = "")
	{
		return $source->isJavascript() && false === $source->isSame($originalContent);
	}
}
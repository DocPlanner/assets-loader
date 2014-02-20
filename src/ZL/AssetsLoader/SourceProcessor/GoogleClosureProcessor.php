<?php


namespace ZL\AssetsLoader\SourceProcessor;

use ZL\AssetsLoader\SourceProcessor\SourceProcessorInterface;
use ZL\AssetsLoader\Structure\Source;

class GoogleClosureProcessor implements SourceProcessorInterface
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
				$this->closureCompress($source->getContent()),
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
		return $source->isJavascript() && false === $source->isSame($originalContent);
	}

	/**
	 * @param string $source
	 *
	 * @throws \RuntimeException
	 * @return string
	 */
	private function closureCompress($source)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Expect:',
			'Content-type: application/x-www-form-urlencoded',
		]);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_POSTFIELDS,
			'output_format=json' . '&output_info=compiled_code' . '&output_info=errors'
			. '&compilation_level=ADVANCED_OPTIMIZATIONS' . '&js_code=' . urlencode($source)
		);
		curl_setopt($ch, CURLOPT_URL, 'http://closure-compiler.appspot.com/compile');

		$response = curl_exec($ch);

		$response = json_decode($response, true);
		if (array_key_exists('errors', $response))
		{
			$message = implode("\n", array_map(function ($error)
			{
				return $error['error'];
			}, $response['errors']));
			throw new \RuntimeException($message);
		}

		return $response['compiledCode'];
	}

}
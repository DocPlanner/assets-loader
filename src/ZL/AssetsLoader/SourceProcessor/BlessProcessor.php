<?php


namespace ZL\AssetsLoader\SourceProcessor;


use ZL\AssetsLoader\AssetsLoader;
use ZL\AssetsLoader\Structure\Source;

class BlessProcessor implements SourceProcessorInterface
{
	const ENDPOINT_URL = "http://assets-processor.herokuapp.com/";

	/**
	 * @var string
	 */
	private $endpoint;

	public function __construct($endpoint = null)
	{
		$this->endpoint = $endpoint ?: self::ENDPOINT_URL;
	}

	public function processAsset(Source $source, $mode, $originalContent = "")
	{
		$name = basename($source->getName());
		$curl = curl_init($this->endpoint . $name . '.css');
		curl_setopt_array($curl, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $source->getContent(),
		]);

		$response = curl_exec($curl);
		curl_close($curl);

		$results = [$source];
		$response = json_decode($response, true);
		foreach (array_slice($response['files'], 1) as $idx => $file)
		{
			$name = $source->getName() . ".blessed" . (($idx > 0) ? "-".$idx : "");
			$results[] = new Source($file['content'], $source->getType(), $name);
		}
		return $results;
	}

	public function isHandling(Source $source, $mode, $originalContent = "")
	{
		return $source->isStylesheet()
			&& false === $source->isSame($originalContent);
//			&& AssetsLoader::MODE_MAIN === $mode;
	}

}
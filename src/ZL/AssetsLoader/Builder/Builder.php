<?php


namespace ZL\AssetsLoader\Builder;


use ZL\AssetsLoader\AssetsLoader;
use ZL\AssetsLoader\FileProcessor\FileProcessorInterface;
use ZL\AssetsLoader\Indexer\IndexerInterface;
use ZL\AssetsLoader\SourceFactory;
use ZL\AssetsLoader\SourceProcessor\SourceProcessorInterface;
use ZL\AssetsLoader\Structure\Source;
use ZL\AssetsLoader\Structure\File;

class Builder implements BuilderInterface
{
	/** @var SourceFactory */
	protected $sourceFactory;

	/**
	 * @var \ZL\AssetsLoader\Indexer\IndexerInterface
	 */
	private $indexer;
	/**
	 * @var string
	 */
	private $type;

	/**
	 * @param \ZL\AssetsLoader\Indexer\IndexerInterface $indexer
	 * @param string                                    $type
	 * @param \ZL\AssetsLoader\SourceFactory            $sourceFactory
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct(IndexerInterface $indexer, $type, SourceFactory $sourceFactory = null)
	{
		if (false === in_array($type, [AssetsLoader::TYPE_CSS, AssetsLoader::TYPE_JS]))
		{
			throw new \InvalidArgumentException('Unknown type: ' . $type);
		}
		$this->indexer = $indexer;
		$this->type = $type;
		$this->sourceFactory = $sourceFactory ?: new SourceFactory;
	}

	/**
	 * @var FileProcessorInterface[]|SourceProcessorInterface[]
	 */
	private $processors = [];

	public function addFileProcessor(FileProcessorInterface $processor)
	{
		$this->processors[] = $processor;
	}

	public function addSourceProcessor(SourceProcessorInterface $processor)
	{
		$this->processors[] = $processor;
	}

	/**
	 * @param string $path
	 * @param string $target
	 *
	 * @return array
	 */
	public function buildLayout($path, $target)
	{
		$layoutName = basename($target);
		$index = $this->indexer->indexDirectory($path);

		$variants = [
			$layoutName => $this->processFiles($index->getFilePaths()->getArrayCopy(), $this->type, AssetsLoader::MODE_MAIN),
		];

		foreach ($index->getSubModules() as $name => $subModule)
		{
			$moduleFiles = $subModule->getFilePaths();
			if (0 !== $moduleFiles->count())
			{
				$variants[$layoutName . '-' . $name] = $this->processFiles($moduleFiles->getArrayCopy(), $this->type, AssetsLoader::MODE_MODULE);
			}
		}

		$result = [];
		while (list ($name, $files) = each($variants))
		{
			$filePath = $this->getPath($target, $name);
			$content = $this->combineSources($files);
			$file = $this->sourceFactory->createFile($filePath, $this->type);
			$mode = $name === $layoutName ? AssetsLoader::MODE_MAIN : AssetsLoader::MODE_MODULE;

			$sources = $this->processSource($content, $name, $this->type, $mode, $file->getContent());
			foreach ($sources as $source)
			{
				$path = $this->getPath($target, $source->getName());
				$result[$path] = $source->getContent();
			}
		}
		return $result;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param array $paths
	 * @param string $type
	 * @param string $mode
	 *
	 * @return File[]
	 */
	protected function processFiles($paths, $type, $mode)
	{
		$files = array_map(function ($path) use ($type)
		{
			return $this->sourceFactory->createFile($path, $type);
		},
		(array) $paths);

		foreach ($this->processors as $processor)
		{
			if (false === ($processor instanceof FileProcessorInterface))
			{
				continue;
			}

			$files = $this->doProcessSource($processor, $files, $mode);
		}
		return $files;
	}

	/**
	 * @param string $content
	 * @param string $name
	 * @param string $type
	 * @param string $mode
	 * @param string $originalContent
	 *
	 * @return Source[]
	 */
	protected function processSource($content, $name, $type, $mode, $originalContent)
	{
		$sources = [$this->sourceFactory->createSource($content, $type, $name)];
		foreach ($this->processors as $processor)
		{
			if (false === ($processor instanceof SourceProcessorInterface))
			{
				continue;
			}

			$sources = $this->doProcessSource($processor, $sources, $mode, $originalContent);
		}
		return $sources;
	}

	/**
	 * @param FileProcessorInterface|SourceProcessorInterface $processor
	 * @param File[]|Source[]                                 $sources
	 * @param string                                          $mode
	 * @param string                                          $originalContent
	 *
	 * @return array
	 */
	private function doProcessSource($processor, $sources, $mode, $originalContent = null)
	{
		$results = [];
		foreach ($sources as $source)
		{
			if (false === $processor->isHandling($source, $mode, $originalContent))
			{
				$results[] = $source;
				continue;
			}

			$results = array_merge($results, $processor->processAsset($source, $mode, $originalContent));
			$results = array_filter($results);
		}
		return $results;
	}

	/**
	 * @param Source[] $sources
	 * @return string
	 */
	private function combineSources(array $sources)
	{
		$cb = function (Source $item)
		{
			return $item->getContent() . ($item->isJavascript() ? ";" : "");
		};

		return implode("", array_map($cb, $sources));
	}

	/**
	 * @param string $target
	 * @param string $name
	 * @return string
	 */
	private function getPath($target, $name)
	{
		return dirname($target) . '/' . $name . '.' . $this->type;
	}

}
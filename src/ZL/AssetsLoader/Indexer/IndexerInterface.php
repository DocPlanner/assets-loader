<?php


namespace ZL\AssetsLoader\Indexer;


interface IndexerInterface
{
	/**
	 * @param string $directory
	 *
	 * @return Index
	 */
	public function indexDirectory($directory);
}
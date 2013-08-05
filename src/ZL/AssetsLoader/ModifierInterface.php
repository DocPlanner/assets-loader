<?php
/**
 * Author: Łukasz Barulski
 * Date: 05.08.13 11:02
 */

namespace ZL\AssetsLoader;

interface ModifierInterface
{
	/**
	 * @param string $source
	 *
	 * @return string
	 */
	public function modify($source);
}
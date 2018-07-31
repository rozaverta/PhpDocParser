<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:57
 */

namespace RozaVerta\PhpDocParser\DocBlocks;

/**
 * Plain text description
 *
 * Class DocPlainText
 * @package RozaVerta\PhpDocParser\DocBlocks
 */
class DocPlainText extends AbstractDocBlock
{
	public function __construct( array $lines = [] )
	{
		$this->lines = $lines;
	}

	/**
	 * @return bool
	 */
	public function isTextNode(): bool
	{
		return true;
	}
}
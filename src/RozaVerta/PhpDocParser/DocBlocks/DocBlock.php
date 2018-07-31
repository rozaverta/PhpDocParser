<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:57
 */

namespace RozaVerta\PhpDocParser\DocBlocks;

class DocBlock extends AbstractDocBlock
{
	public function __construct( string $name, array $lines = [] )
	{
		$this->name = $name;
		$this->lines = $lines;
	}

	public function isTextNode(): bool
	{
		return false;
	}
}
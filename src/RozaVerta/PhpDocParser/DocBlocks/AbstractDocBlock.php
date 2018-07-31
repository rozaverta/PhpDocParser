<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:57
 */

namespace RozaVerta\PhpDocParser\DocBlocks;

use \RozaVerta\PhpDocParser\Interfaces\DocBlock as DocBlockInterface;

abstract class AbstractDocBlock implements DocBlockInterface
{
	protected $name;

	protected $lines;

	protected $valid = true;

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->isTextNode() ? "" : $this->name;
	}

	/**
	 * @return bool
	 */
	public function isValid(): bool
	{
		return $this->valid;
	}

	/**
	 * @return string
	 */
	public function getText(): string
	{
		return ' * ' . ($this->name === null ? '' : ('@' . $this->name . ' ')) . implode("\n * ", $this->lines );
	}

	/**
	 * @param string $glue
	 * @return string
	 */
	public function getContext( string $glue = " " ): string
	{
		return implode($glue, $this->lines );
	}

	/**
	 * @return array <string>
	 */
	public function getLines(): array
	{
		return $this->lines;
	}

	public function jsonSerialize()
	{
		if( $this->isTextNode() )
		{
			return [
				"type" => "text",
				"lines" => $this->getLines()
			];
		}
		else
		{
			return [
				"type"  => "block",
				"name"  => $this->getName(),
				"valid" => $this->isValid(),
				"lines" => $this->getLines()
			];
		}
	}

	public function __toString()
	{
		$result = $this->getContext();
		if( ! $this->isTextNode() )
		{
			$result = "@" . $this->getName() . " " . $result;
		}

		return $result;
	}
}
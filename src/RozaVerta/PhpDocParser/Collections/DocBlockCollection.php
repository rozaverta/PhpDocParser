<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:04
 */

namespace RozaVerta\PhpDocParser\Collections;

use RozaVerta\PhpDocParser\DocBlocks\AbstractDocBlock;
use RozaVerta\PhpDocParser\DocBlocks\DocPlainText;
use RozaVerta\PhpDocParser\Interfaces\DocBlock;

class DocBlockCollection extends AbstractCollection
{
	private $length = 0;

	private $nodes = [];

	private $text_nodes = [];

	public function __construct( array $items = [] )
	{
		foreach($items as $instance) {
			$this->items[] = $instance;
		}
	}

	public function getTextParams(): array
	{
		return $this->getParamsFilter( null );
	}

	public function getParams(string $name): array
	{
		return $this->getParamsFilter( strtolower($name) );
	}

	public function getDescription(): DocPlainText
	{
		return $this->hasDescription() ? $this->items[0] : new DocPlainText();
	}

	private function getParamsFilter($name): array
	{
		$result = [];

		if( is_null($name) )
		{
			$idx = $this->text_nodes;
		}
		else if( isset($this->nodes[$name]) )
		{
			$idx = $this->nodes[$name];
		}
		else
		{
			return $result;
		}

		foreach($idx as $index)
		{
			$result[] = $this->items[$index];
		}

		return $result;
	}

	public function hasDescription(): bool
	{
		if( $this->length > 0 )
		{
			/** @var AbstractDocBlock $node */
			$node = $this->items[0];
			return $node->isTextNode();
		}
		return false;
	}

	public function hasTextParam(): bool
	{
		return count($this->text_nodes) > 0;
	}

	public function hasParam($name): bool
	{
		return isset($this->nodes[$name]);
	}

	public function filterByName($name)
	{
		return $this->filter(function(DocBlock $doc) use ($name) { return $doc->getName() === $name; });
	}

	/**
	 * Count elements of an object
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * </p>
	 * <p>
	 * The return value is cast to an integer.
	 * @since 5.1.0
	 */
	public function count()
	{
		return $this->length;
	}

	/**
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 * @since 5.0.0
	 */
	public function offsetExists( $offset )
	{
		if( is_numeric($offset) )
		{
			$offset = (int) $offset;
			return $offset >= 0 && $offset < $this->length;
		}
		else if( array_key_exists($offset, $this->nodes) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetSet( $offset, $value )
	{
		if( ! is_null($offset) )
		{
			throw new \InvalidArgumentException("Collections add error");
		}

		if ( $value instanceof DocBlock )
		{
			if( $value->isTextNode() )
			{
				$this->text_nodes[] = $this->length;
			}
			else
			{
				$name = $value->getName();
				if( !isset($this->nodes[$name]) )
				{
					$this->nodes[$name] = [];
				}
				$this->nodes[$name][] = $this->length;
			}

			$this->items[$this->length++] = $value;
		}
		else
		{
			throw new \InvalidArgumentException("Invalid implement interface");
		}
	}

	/**
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset( $offset )
	{
		if( is_numeric($offset) )
		{
			$offset = (int) $offset;
			if( $offset >= 0 && $offset < $this->length )
			{
				unset($this->items[$offset]);
			}
			else
			{
				return;
			}
		}
		else if( isset($this->nodes[$offset]) )
		{
			foreach($this->nodes[$offset] as $index)
			{
				unset($this->items[$index]);
			}
		}
		else
		{
			return;
		}

		// reload indexes
		$new = array_values($this->items);

		// clean data
		$this->empty();

		/** @var DocBlock $doc_block */
		foreach( $new as $doc_block )
		{
			$this[] = $doc_block;
		}
	}

	public function empty()
	{
		$this->items = [];
		$this->length = 0;
		$this->text_nodes = [];
		$this->nodes = [];
		return $this;
	}

	/**
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 * @return mixed Can return all value types.
	 * @since 5.0.0
	 */
	public function offsetGet( $offset )
	{
		if( is_numeric($offset) )
		{
			$offset = (int) $offset;
			return $offset >= 0 && $offset < $this->length ? $this->items[$offset] : null;
		}
		else
		{
			return $this->getParams($offset);
		}
	}

	public function jsonSerialize()
	{
		return $this->json(
			[
				"indexes" => [
					"texts" => $this->text_nodes,
					"blocks" => $this->nodes
				]
			]
		);
	}
}
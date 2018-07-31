<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 18:07
 */

namespace RozaVerta\PhpDocParser;

use Iterator;
use RozaVerta\PhpDocParser\Collections\DocBlockCollection;
use RozaVerta\PhpDocParser\DocBlocks\DocBlock;
use RozaVerta\PhpDocParser\DocBlocks\DocPlainText;

class DocBlocks implements Iterator
{
	protected $all = [];

	protected $pos = 0;

	protected $length = 0;

	protected $name = null;

	protected $lines = [];

	protected $is_next = false;

	public function __construct( $str )
	{
		$str = trim($str);
		if(substr($str, 0, 2) === "/*")
		{
			$str = str_replace(["\r\n", "\r"], "\n", $str);
			$str = preg_replace('/[ \t]+/', " ", $str);
			$str = ltrim( substr($str, 1), "*" );
			if( strrpos($str, '*/') === strlen($str) - 2 )
			{
				$str = rtrim($str, ' */');
			}
			$this->all = array_map("trim", explode("\n", $str) );
			$this->length = count($this->all);
		}
	}

	public function getCollection(): DocBlockCollection
	{
		$collection = new DocBlockCollection();

		$classes = Factory::getDocBlocksClasses();

		foreach( $this as $name => $lines )
		{
			if( is_null($name) )
			{
				$collection[] = new DocPlainText($lines);
			}
			else
			{
				$name = strtolower($name);
				if( isset($classes[$name]) )
				{
					$class_name = $classes[$name];
				}
				else
				{
					$class_name = DocBlock::class;
				}

				$collection[] = new $class_name($name, $lines);
			}
		}

		return $collection;
	}

	/**
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public function current()
	{

		return $this->lines;
	}

	/**
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key()
	{
		return $this->name;
	}

	/**
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	public function valid()
	{
		return $this->is_next;
	}

	/**
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind()
	{
		$this->pos = 0;
		$this->loadNext();
	}

	/**
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next()
	{
		$this->loadNext();
	}

	protected function loadNext()
	{
		$this->name = null;
		$this->lines = [];

		$n = -1;

		for( ; $this->pos < $this->length; )
		{
			$key = $this->all[$this->pos++];

			$merge = strlen($key) && $key[0] === "*";
			if( $merge )
			{
				$key = ltrim($key, "*");
				$key = ltrim($key);
			}

			if(strlen($key) < 1) {
				if( $n > -1 )
				{
					break;
				}
				else
				{
					continue;
				}
			}

			if( $key[0] === "@" )
			{
				if( $n > -1 )
				{
					$this->pos --;
					break;
				}

				$key = $this->setName($key);
				if( strlen($key) )
				{
					$this->lines[++$n] = $key;
				}
				else if( $this->name )
				{
					$n = 0;
				}
			}
			else if( $merge || $n < 0 )
			{
				$this->lines[++$n] = $key;
			}
			else
			{
				$this->lines[$n] .= " " . $key;
			}
		}

		$this->is_next = $n > -1;
	}

	protected function setName($key)
	{
		if(preg_match('/^@+(.*?)(?:\s+|$)/', $key, $m))
		{
			$name = trim($m[1]);
			if(strlen($name))
			{
				$this->name = $name;
			}

			$value = substr($key, strlen($m[0]));
			return trim($value);
		}
		else
		{
			return ltrim($key, "@");
		}
	}
}
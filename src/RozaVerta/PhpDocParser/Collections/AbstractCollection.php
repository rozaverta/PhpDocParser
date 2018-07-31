<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 22:28
 */

namespace RozaVerta\PhpDocParser\Collections;

use ArrayIterator;
use JsonSerializable;
use RozaVerta\PhpDocParser\Interfaces\Collection;

abstract class AbstractCollection implements Collection
{
	protected $items = [];

	public function getAll(): array
	{
		return $this->items;
	}

	/**
	 * Retrieve an external iterator
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return \Traversable An instance of an object implementing <b>Iterator</b> or
	 * <b>Traversable</b>
	 * @since 5.0.0
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->items);
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize()
	{
		return $this->json([]);
	}

	public function each( \Closure $closure, $break_value = false ): Collection
	{
		if( !$this->count() )
		{
			return $this;
		}

		if($this instanceof AbstractComponentCollection)
		{
			foreach($this->items as $name => $item) {
				if( $closure($name, $item) === $break_value ) break;
			}
		}
		else
		{
			foreach($this->items as $name => $item) {
				if( $closure($item) === $break_value ) break;
			}
		}

		return $this;
	}

	public function filter( \Closure $closure ): Collection
	{
		$class_name = self::class;
		return new $class_name(
			array_filter($this->items, $closure, $this instanceof AbstractComponentCollection ? ARRAY_FILTER_USE_BOTH : 0)
		);
	}

	protected function json( array $from )
	{
		$from["length"] = $this->count();
		$from["items"] = array_map(function ($value) {
				return $value instanceof JsonSerializable ? $value->jsonSerialize() : $value;
			}, $this->items);

		return $from;
	}
}
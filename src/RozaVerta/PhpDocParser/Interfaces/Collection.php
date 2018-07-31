<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:09
 */

namespace RozaVerta\PhpDocParser\Interfaces;

use ArrayAccess;
use IteratorAggregate;
use Countable;
use JsonSerializable;

interface Collection extends ArrayAccess, IteratorAggregate, Countable, JsonSerializable
{
	public function each( \Closure $closure, $break_value = false ): Collection;

	public function filter( \Closure $closure ): Collection;

	public function getAll(): array;

	public function empty();
}
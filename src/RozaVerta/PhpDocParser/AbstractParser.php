<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 31.07.2018
 * Time: 3:18
 */

namespace RozaVerta\PhpDocParser;

abstract class AbstractParser
{
	/**
	 * @var Reflector
	 */
	protected $reflector;

	public function __construct()
	{
		$this->reflector = new Reflector();
	}

	/**
	 * @return Reflector
	 */
	public function getReflector(): Reflector
	{
		return $this->reflector;
	}
}
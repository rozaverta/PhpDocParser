<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:55
 */

namespace RozaVerta\PhpDocParser\Components\Traits;

use RozaVerta\PhpDocParser\Exceptions\NotFoundException;

trait ComponentReflectorTrait
{
	protected $reflector;

	/**
	 * @return \Reflection | null
	 */
	public function getNativeReflector()
	{
		return $this->reflector;
	}

	public function __call($name, $args)
	{
		if( $this->reflector && method_exists($this->reflector, $name) )
		{
			return $this->reflector->{$name}( ... $args );
		}
		else
		{
			throw new NotFoundException("Callback function '{$name}' not found", NotFoundException::IS_CALLBACK);
		}
	}
}
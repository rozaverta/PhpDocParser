<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:55
 */

namespace RozaVerta\PhpDocParser\Components;

use RozaVerta\PhpDocParser\Interfaces\DocComponentReflector;
use ReflectionClassConstant;

class ClassConstantComponent extends ComponentAbstract implements DocComponentReflector
{
	use Traits\ComponentReflectorTrait;

	protected $name_namespace_delimiter = '::';

	public function __construct( $name, $name_space, \ReflectionClassConstant $reflector = null )
	{
		$this->reflector = $reflector;
		parent::__construct( $name, $name_space, $reflector->getDocComment() );
	}
}
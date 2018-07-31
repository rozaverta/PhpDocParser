<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:55
 */

namespace RozaVerta\PhpDocParser\Components;

use RozaVerta\PhpDocParser\Interfaces\DocComponentReflector;
use ReflectionProperty;

class PropertyComponent extends ComponentAbstract implements DocComponentReflector
{
	use Traits\ComponentReflectorTrait;

	const IS_STATIC = ReflectionProperty::IS_STATIC;
	const IS_PUBLIC = ReflectionProperty::IS_PUBLIC;
	const IS_PROTECTED = ReflectionProperty::IS_PROTECTED;
	const IS_PRIVATE = ReflectionProperty::IS_PRIVATE;

	protected $name_namespace_delimiter = '::$';

	public function __construct( $name, $name_space, \ReflectionProperty $reflector = null )
	{
		$this->reflector = $reflector;
		parent::__construct( $name, $name_space, $reflector->getDocComment() );
	}

	public function jsonSerialize()
	{
		$result = parent::jsonSerialize();
		$result["is_static"] = $this->isStatic();
		$result["is_public"] = $this->isPublic();
		$result["is_protected"] = $this->isProtected();
		$result["is_private"] = $this->isPrivate();
		return $result;
	}
}
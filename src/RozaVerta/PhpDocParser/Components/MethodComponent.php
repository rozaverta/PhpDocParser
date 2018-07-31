<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:55
 */

namespace RozaVerta\PhpDocParser\Components;

use RozaVerta\PhpDocParser\Interfaces\DocComponentReflector;
use ReflectionMethod;

class MethodComponent extends ComponentAbstract implements DocComponentReflector
{
	use Traits\ComponentReflectorTrait;

	const IS_PUBLIC = ReflectionMethod::IS_PUBLIC;
	const IS_PROTECTED = ReflectionMethod::IS_PROTECTED;
	const IS_PRIVATE = ReflectionMethod::IS_PRIVATE;
	const IS_FINAL = ReflectionMethod::IS_FINAL;
	const IS_ABSTRACT = ReflectionMethod::IS_ABSTRACT;
	const IS_STATIC = ReflectionMethod::IS_STATIC;

	protected $name_namespace_delimiter = '::';

	public function __construct( $name, $name_space, \ReflectionMethod $reflector = null )
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
		$result["is_abstract"] = $this->isAbstract();
		$result["is_final"] = $this->isFinal();
		return $result;
	}
}
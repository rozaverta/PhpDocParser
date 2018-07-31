<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:55
 */

namespace RozaVerta\PhpDocParser\Components;

use RozaVerta\PhpDocParser\Collections\ClassConstantComponentCollection;
use RozaVerta\PhpDocParser\Collections\PropertyComponentCollection;
use RozaVerta\PhpDocParser\Collections\MethodComponentCollection;
use RozaVerta\PhpDocParser\Exceptions\NotFoundException;
use RozaVerta\PhpDocParser\Interfaces\DocComponentReflector;
use ReflectionException;
use ReflectionClass;
use ReflectionClassConstant;

class ClassComponent extends ComponentAbstract implements DocComponentReflector
{
	use Traits\ComponentReflectorTrait;

	const IS_IMPLICIT_ABSTRACT = ReflectionClass::IS_IMPLICIT_ABSTRACT;
	const IS_EXPLICIT_ABSTRACT = ReflectionClass::IS_EXPLICIT_ABSTRACT;
	const IS_FINAL = ReflectionClass::IS_FINAL;

	protected $final = false;

	protected $abstract = false;

	protected $trait = false;

	protected $interface = false;

	public function __construct( $name, $name_space, $doc_blocks = null )
	{
		$class_name = $name;
		if($name_space)
		{
			$class_name = $name_space . "\\" . $class_name;
		}

		try {
			$this->reflector = new ReflectionClass($class_name);
		}
		catch(ReflectionException $e)
		{
			throw new NotFoundException( $e->getMessage(), NotFoundException::IS_CLASS, $e );
		}

		parent::__construct( $name, $name_space, $this->reflector->getDocComment() );
	}

	public function getConstantComponent( $name )
	{
		$class_name = $this->getFullName();
		try {
			$reflector = new ReflectionClassConstant($class_name, $name);
		}
		catch(ReflectionException $e ) {
			throw new NotFoundException( $e->getMessage(), NotFoundException::IS_CONSTANT, $e );
		}

		return new ClassConstantComponent( $name, $class_name, $reflector );
	}

	public function getConstantComponents()
	{
		$class_name = $this->getFullName();
		$collection = new ClassConstantComponentCollection($this);

		foreach( array_keys( $this->reflector->getConstants()) as $name )
		{
			$reflector = new ReflectionClassConstant($class_name, $name);
			$collection[] = new ClassConstantComponent($name, $class_name, $reflector);
		}

		return $collection;
	}

	public function getPropertyComponent( $name )
	{
		try {
			$reflector = $this->reflector->getProperty($name);
		}
		catch(ReflectionException $e) {
			throw new NotFoundException( $e->getMessage(), NotFoundException::IS_PROPERTY, $e );
		}

		$name_space = ($this->name_space ? ($this->name_space . "\\") : "" ) . $this->name;

		return new PropertyComponent( $name, $name_space, $reflector );
	}

	public function getPropertyComponents( $flag = 0 )
	{
		if( !$flag )
		{
			$flag = PropertyComponent::IS_PUBLIC | PropertyComponent::IS_PROTECTED | PropertyComponent::IS_PRIVATE | PropertyComponent::IS_STATIC;
		}
		$collection = new PropertyComponentCollection($this);
		$name_space = ($this->name_space ? ($this->name_space . "\\") : "" ) . $this->name;

		foreach( $this->reflector->getProperties( $flag ) as $property )
		{
			$collection[] = new PropertyComponent( $property->getName(), $name_space, $property );
		}

		return $collection;
	}

	public function getMethodComponents( $flag = 0 )
	{
		if( !$flag )
		{
			$flag = MethodComponent::IS_PUBLIC | MethodComponent::IS_PROTECTED | MethodComponent::IS_PRIVATE;
		}

		$collection = new MethodComponentCollection($this);
		foreach( $this->reflector->getMethods($flag) as $method )
		{
			$collection[] = new MethodComponent( $method->getName(), $this->name_space, $method );
		}

		return $collection;
	}

	public function getMethodComponent( $name )
	{
		try {
			$reflector = $this->reflector->getMethod($name);
		}
		catch(ReflectionException $e) {
			throw new NotFoundException( $e->getMessage(), NotFoundException::IS_METHOD, $e );
		}

		return new MethodComponent( $name, $this->name_space, $reflector );
	}

	public function jsonSerialize()
	{
		$result = parent::jsonSerialize();

		$result['is_interface'] = $this->isInterface();
		$result['is_trait'] = $this->isTrait();
		$result['is_abstract'] = $this->isAbstract();
		$result['is_final'] = $this->isFinal();
		$result['is_anonymous'] = $this->isAnonymous();
		$result['is_cloneable'] = $this->isCloneable();
		$result['is_instantiable'] = $this->isInstantiable();
		$result['is_iterateable'] = $this->isIterateable();

		$result["constants"] = $this->getConstantComponents()->jsonSerialize();
		$result["properties"] = $this->getPropertyComponents()->jsonSerialize();
		$result["methods"] = $this->getMethodComponents()->jsonSerialize();

		return $result;
	}
}
<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 31.07.2018
 * Time: 5:41
 */

namespace RozaVerta\PhpDocParser\Collections\Traits;

use RozaVerta\PhpDocParser\Components\ClassComponent;

trait ClassConstructorTrait
{
	/**
	 * @var ClassComponent
	 */
	private $class_component;

	public function __construct( ClassComponent $class_component, array $items = [] )
	{
		parent::__construct( $items );
		$this->class_component = $class_component;
	}

	/**
	 * @return ClassComponent
	 */
	public function getClassComponent(): ClassComponent
	{
		return $this->class_component;
	}
}
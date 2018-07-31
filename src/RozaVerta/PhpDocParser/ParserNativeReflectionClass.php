<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 18:07
 */

namespace RozaVerta\PhpDocParser;

use RozaVerta\PhpDocParser\Components\ClassComponent;

class ParserNativeReflectionClass extends AbstractParser
{
	public function __construct( \ReflectionClass $reflection )
	{
		parent::__construct();
		$this->getReflector()->addClass(
			new ClassComponent($reflection->getName(), $reflection->getNamespaceName())
		);
	}
}
